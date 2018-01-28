<?php

/**
 * Class EventImporter
 */
class EventImporter
{
    const DIR_UPLOADS = 'uploaded/';
    /**
     * @var FileManager
     */
    private $fileManager;
    /**
     * @var CsvManager
     */
    private $csvManager;
    /**
     * @var DbManager
     */
    private $dbManager;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var string
     */
    private $basePath;
    /**
     * @var Semaphore
     */
    private $semaphore;

    /**
     * EventImporter constructor.
     * @param FileManager $fileManager
     * @param CsvManager $csvManager
     * @param DbManager $dbManager
     * @param Logger $logger
     * @param Semaphore $semaphore
     * @param string $basePath
     */
    public function __construct(
        FileManager $fileManager,
        CsvManager $csvManager,
        DbManager $dbManager,
        Logger $logger,
        Semaphore $semaphore,
        string $basePath
    ) {

        $this->fileManager = $fileManager;
        $this->csvManager = $csvManager;
        $this->dbManager = $dbManager;
        $this->logger = $logger;
        $this->basePath = $basePath;
        $this->semaphore = $semaphore;
    }

    /**
     * Normally this would be in a db migration.
     */
    public function init()
    {
        // Create table
        $this->dbManager->query("CREATE TABLE IF NOT EXISTS `events`(
            id INT NOT NULL AUTO_INCREMENT,
            `eventDatetime` DATETIME NOT NULL,
            `eventAction` VARCHAR(20) NOT NULL,
            `callRef` INT NOT NULL,
            `eventValue` DECIMAL (10, 2),
            `eventCurrencyCode` VARCHAR(3),
            PRIMARY KEY (id)
        )");
    }

    /**
     * Read files, validate and import into db.
     */
    public function run()
    {
        if (!$this->semaphore->get()) {
            $this->logger->warning('Process already running');
            return;
        }

        $filePath = $this->basePath. self::DIR_UPLOADS;

        try {
            $files = $this->fileManager->readDir($filePath);
        } catch (Exception $e) {
            $this->logger->error(sprintf('Couldn\'t read from dir "%s": %s'), $filePath, $e->getMessage());
        }

        foreach($files as $f) {
            try {
                $data = $this->csvManager->readFile($f);
                $this->saveData($data);
            } catch (Exception $e) {
                $this->logger->error(sprintf('Couldn\'t process %s: %s', $f, $e->getMessage()));
            }
        }

        $this->semaphore->release();
    }

    /**
     * @param array $data
     */
    private function saveData(array $data)
    {
        $this->dbManager->beginTransaction();
        $defaults = ['eventValue' => null, 'eventCurrencyCode' => null];

        $sql = 'INSERT INTO `events` 
          (callRef, eventAction, eventCurrencyCode, eventDatetime, eventValue) 
          VALUES 
        (:callRef, :eventAction, :eventCurrencyCode, :eventDatetime, :eventValue)';
        $stmt = $this->dbManager->prepare($sql);

        foreach ($data as $row) {
            // Remove empty cells
            $row = array_filter($row, function($item){
                return $item !== '';
            });

            // If data is invalid, log and try the next row
            if (!$this->validateRow($row)) {
                $this->logger->warning('Invalid data: '.var_export($row, true));
                continue;
            }

            // Save valid data as part of transaction
            $row = array_merge($defaults, $row);
            $stmt->bindParam('callRef', $row['callRef'], PDO::PARAM_INT);
            $stmt->bindParam('eventAction', $row['eventAction'], PDO::PARAM_STR);
            $stmt->bindParam('eventCurrencyCode', $row['eventCurrencyCode'], PDO::PARAM_STR);
            $stmt->bindParam('eventDatetime', $row['eventDatetime'], PDO::PARAM_STR);
            $stmt->bindParam('eventValue', $row['eventValue'], PDO::PARAM_STR);
            $stmt->execute();
        }

        $this->dbManager->commitTransaction();
    }

    /**
     * @param array $row
     * @return bool
     */
    private function validateRow(array $row): bool
    {
        $keys = array_keys($row);
        $minRequired = ['eventDatetime', 'eventAction', 'callRef'];
        $maxRequired = ['eventDatetime', 'eventAction', 'callRef', 'eventValue', 'eventCurrencyCode'];
        sort($minRequired);
        sort($maxRequired);
        sort($keys);

        $hasKeys = $keys == $minRequired || $keys == $maxRequired;
        $hasRequired = !empty($row['eventDatetime']) && !empty($row['eventAction']) && !empty($row['callRef']);

        if (count($keys) === count($maxRequired)) {
            $hasRequired = $hasRequired && isset($row['eventValue']) && !empty($row['eventCurrencyCode']);
        }

        return $hasKeys && $hasRequired;
    }
}