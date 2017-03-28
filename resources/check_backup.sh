#!/bin/bash

while getopts "D:" OPTION;
do
        case $OPTION in
                "D") # Assign hostname
                        DIRECTORY="$OPTARG"
                ;;
        esac
done

if [ $(find $DIRECTORY -mtime -1 | wc -l) -gt 0 ]; then 
    EXIT_STRING="Backup find\n"
    EXIT_CODE=0
else
    EXIT_STRING="Backup not find\n"
    EXIT_CODE=2
fi

printf "$EXIT_STRING\n"
exit $EXIT_CODE
