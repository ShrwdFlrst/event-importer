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
     * EventImporter constructor.
     * @param FileManager $fileManager
     * @param CsvManager $csvManager
     * @param DbManager $dbManager
     * @param Logger $logger
     * @param string $basePath
     */
    public function __construct(
        FileManager $fileManager,
        CsvManager $csvManager,
        DbManager $dbManager,
        Logger $logger,
        string $basePath
    ) {

        $this->fileManager = $fileManager;
        $this->csvManager = $csvManager;
        $this->dbManager = $dbManager;
        $this->logger = $logger;
        $this->basePath = $basePath;
    }

    /**
     * Normally this would be in a db migration.
     */
    public function init()
    {
        // Clear table
        $this->dbManager->query("DROP TABLE IF EXISTS `events`");

        // Create table
        $this->dbManager->query("CREATE TABLE IF NOT EXISTS `events`(
            id INT NOT NULL AUTO_INCREMENT,
            `eventDatetime` DATETIME NOT NULL,
            `eventAction` VARCHAR(20) NOT NULL,
            `callRef` INT NOT NULL,
            `eventValue` DECIMAL,
            `eventCurrencyCode` VARCHAR(3),
            PRIMARY KEY (id)
        )");
    }

    /**
     * Read files, validate and import into db.
     */
    public function run()
    {
        $files = $this->fileManager->readDir($this->basePath. self::DIR_UPLOADS);

        foreach($files as $f) {
            try {
                $data = $this->csvManager->readFile($f);
                $this->saveData($data);
            } catch (Exception $e) {
                $this->logger->error(sprintf('Couldn\'t process %s: %s', $f, $e->getMessage()));
            }
        }
    }

    private function saveData(array $data)
    {
        
    }
}