<?php

require './src/FileManager.php';
require './src/CsvManager.php';
require './src/DbManager.php';
require './src/Logger.php';
require './src/EventImporter.php';

$logger = new Logger();
$fileManager = new FileManager();
$csvManager = new CsvManager();

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
    getenv('BASE_PATH')
);

$importer->init();
$importer->run();

$dbManager->close();

