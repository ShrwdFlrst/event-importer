<?php

// Import classes
require './src/FileManager.php';
require './src/CsvManager.php';
require './src/DbManager.php';
require './src/Logger.php';
require './src/Semaphore.php';
require './src/EventImporter.php';

// Instantiate and configure
$logger = new Logger();
$fileManager = new FileManager();
$csvManager = new CsvManager();
$semaphore = new Semaphore("./importer_semaphore");
$dbManager = new DbManager(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD')
);
$importer = new EventImporter(
    $fileManager,
    $csvManager,
    $dbManager,
    $logger,
    $semaphore,
    getenv('BASE_PATH')
);

// Clear table for the purpose of the demo
$dbManager->query("DROP TABLE IF EXISTS `events`");

// Execute importer
$importer->init();
$importer->run();

// Close db connection
$dbManager->close();

