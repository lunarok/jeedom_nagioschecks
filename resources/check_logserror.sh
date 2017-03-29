#!/bin/bash

while getopts "D:" OPTION;
do
        case $OPTION in
                "D") # Assign hostname
                        DIRECTORY="$OPTARG"
                ;;
        esac
done

if [ $(grep "ERROR" -R $DIRECTORY | grep ' . date('Y-m-d', time()) . ' | wc -l ) -gt 0 ]; then
    EXIT_STRING=`grep "ERROR" -R $DIRECTORY | grep ' . date('Y-m-d', time()) . ' | tr -d '\n'`
    EXIT_CODE=0
else
    EXIT_STRING="Error in logs"
    EXIT_CODE=2
fi

printf "$EXIT_STRING\n"
exit $EXIT_CODE
