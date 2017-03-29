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
    EXIT_STRING=$(grep "ERROR" -R $DIRECTORY | grep ' . date('Y-m-d', time()) . ' | tr -d '\n')
    EXIT_CODE=2
else
    EXIT_STRING=`Logs Jeedom OK`
    EXIT_CODE=0
fi

printf "$EXIT_STRING\n"
exit $EXIT_CODE
