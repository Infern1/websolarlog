#!/bin/bash
# WebSolarLog start and stop script

SOURCE="${BASH_SOURCE[0]}"
DIR="$( dirname "$SOURCE" )"
EPOCH=`date +%s`

while [ -h "$SOURCE" ]
do 
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE"
  DIR="$( cd -P "$( dirname "$SOURCE"  )" && pwd )"
done
DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
WWWDIR="$( dirname "$DIR" )"

PHP=$(which php)

looping ()
{ 
while [ "true" ] # To infinity ... and beyond!
do
#Check if the lock file is too old
find $WWWDIR"/data/lock" -mmin +2 -delete 2> /dev/null

if [ ! -f $WWWDIR"/data/lock" ] # Port lock
then
  $PHP $WWWDIR"/scripts/worker.php" >> $WWWDIR/worker.log 2>&1
fi
sleep 0.5
done
}


case $1 in
setup)

   if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 1>&2
   echo "Aborting.."; exit 1;
   fi

   if [ -e "/usr/bin/wsl" ]
   then
     rm /usr/bin/wsl
   fi
  ln -s "$WWWDIR"/scripts/wsl.sh /usr/bin/wsl
  chmod 770 "$WWWDIR"/scripts/wsl.sh "$WWWDIR"/scripts/worker.php
  chmod 770 "$WWWDIR"/scripts/wsl.sh "$WWWDIR"/scripts/wsl.sh

;;
start)

   if [ ! -e "/usr/bin/wsl" ]
    then
      $WWWDIR"/scripts/wsl.sh setup" >> $WWWDIR/setup.log 2>&1
    fi

    if [ ! -f /var/lock/wsl.lock ]; then
        touch /var/lock/wsl.lock
        looping &
        echo "Starting WebSolarLog.."
    else
        echo "WebSolarLog is already started"
    fi
;;
stop)

    if [ -f /var/lock/wsl.lock ]; then
    kill `ps -ef | grep 'wsl.sh start' | grep -v grep | awk '{ print $2 }'`
    rm /var/lock/wsl.lock
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
