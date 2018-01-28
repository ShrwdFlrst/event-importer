# CSV Importer

## Running

Initialise environment vars and run the importer using test data.

    ./run.sh

### Requirements

- PHP 7.1
- MySql

### TODOs/Improvements

- Allow multiple instance at same time but working on different files
- Allow stop/error and resume
- Composer support, namespacing
- Support large CSVs that can't be read in one go using generators/`yield`
