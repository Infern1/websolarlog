#!/bin/bash
# WebSolarLog start and stop script
BASHDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd)"
WWWDIR="$( dirname $( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd))"
PHP="php"

looping ()
{ 
while [ "true" ] # To infinity ... and beyond!
do
#Check if the lock file is too old
find $WWWDIR"/data/lock" -mmin +2 -delete 2> /dev/null

if [ ! -f $WWWDIR"/data/lock" ] # Port lock
then
  $PHP $WWWDIR"/scripts/worker.php" >> $BASHDIR/worker.log 2>&1
fi
sleep 1
done
}

case $1 in
start)
    if [ ! -f /var/lock/123aurora ]; then
        touch /var/lock/123aurora
        looping &
        echo "Starting 123aurora.."
    else
        echo "123aurora is already started"
    fi
;;
stop)
    if [ -f /var/lock/123aurora ]; then
    kill `ps -ef | grep '123aurora.sh start' | grep -v grep | awk '{ print $1 }'`
    rm /var/lock/123aurora
        if [ -f $WWWDIR'/data/lock' ]; then
        echo "Cleanup port lock"
        rm $WWWDIR'/data/lock'
        fi
        echo "WebSolarLog stopped"
    else
        echo "WebSolarLog was already stopped"
    fi
;;
*)
clear
pathtosrv=`pwd`
echo "Welcome to WebSolarLog

Usage : simply run as root $pathtosrv/wsl.sh { start | stop }"
;;
esac
exit 0