# Event Importer

## Running

Initialise environment vars and run the importer using test data.

    ./run.sh

##### Requirements

- PHP 7.1
- MySql
    
    
### Assumptions

- Tab separated file is invalid CSV so ignored
- 0 is a valid eventValue
- Logger outputs to both syslog and commandline for the purpose of the demo
- Processed files are copied for the purpose of the demo so script can be called multiple times easily



### TODOs/Improvements

- Allow multiple instance at same time but working on different files
- Allow stop/error and resume
- Composer support, namespacing
- Support large CSVs that can't be read in one go using generators/`yield`
