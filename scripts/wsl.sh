#!/bin/bash
# WebSolarLog start and stop script

SCRIPTDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PIDFOLDER=$SCRIPTDIR
PHPSCRIPTNAME="worker.php"
PHP=$(which php)
NOHUP=$(which nohup)
LOGFILE=$SCRIPTDIR"/workerNew.log"

cd $SCRIPTDIR

RESULT="NO"
is_running(){
        RESULT="NO"
        if [ ! -f $PIDFOLDER"/"$PHPSCRIPTNAME".pid" ] 
        then
          return
        fi

        kill -0 `cat $PIDFOLDER"/"$PHPSCRIPTNAME".pid"` 2> /dev/null
        if [ "$?" -eq "1" ]
        then
          return
        fi

        RESULT="YES"
}

looping ()
{ 
	while [ "true" ] # To infinity ... and beyond!
	do
		is_running
		if [ "$RESULT" = 'NO' ]
		then
		        echo "not running, starting"
		        $NOHUP $PHP $PHPSCRIPTNAME >> $LOGFILE &
		fi
        sleep 10 # Wait for 10 seconds
	done
}

is_running
case $1 in
start)
	if [ "$RESULT" = 'YES' ]
	then
        echo "WebSolarLog is already started"
    else
        looping &
        echo "Starting WebSolarLog.."
    fi
;;
stop)
	if [ "$RESULT" = 'YES' ]
	then
		kill `cat $PIDFOLDER"/"$PHPSCRIPTNAME".pid"` 2> /dev/null
	else
		echo "WebSolarLog is not running"
	fi
	kill `pgrep wsl.sh` &
	exit 0
;;
status)
	if [ "$RESULT" = 'YES' ]
	then
		echo "WebSolarLog is running"
	else
		echo "WebSolarLog is not running"
	fi
	exit 0
;;
*)
	echo .
	echo "Welcome to WebSolarLog
	echo .
	echo Usage : simply run as root $SCRIPTDIR/wsl.sh { start | stop | status }"
;;
esac
exit 0