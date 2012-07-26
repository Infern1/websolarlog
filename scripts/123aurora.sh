#!/opt/bin/bash
# Louviaux Jean-Marc
# 123aurora start and stop script
WWWDIR="$( dirname $( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd))"

looping ()
{ 
while [ "true" ] # To infinity ... and beyond!
do
#Check if the lock file is too old
find $WWWDIR"/data/lock" -mmin +2 -delete 2> /dev/null

if [ ! -f $WWWDIR"/data/lock" ] # Port lock
then
  /mnt/ext/opt/apache/bin/php $WWWDIR"/scripts/worker.php" 2> worker.log
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
        echo "123aurora stopped"
    else
        echo "123aurora was already stopped"
    fi
;;
admin)
    shuf -i 10000-9999999 -n 1 > /tmp/123AURORAPASS

    set -e
    function cleanup {
      echo "Session terminated"
      rm  /tmp/123AURORAPASS
    }

    function pause(){
       read -p "$*"
       trap cleanup EXIT
    }

    IP=`netstat -n -t | awk '{print $4}' | grep -o "[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*" | grep -v "127.0.0.1" | sort -u`

    clear
    echo "123aurora administration"
    echo ""
    echo "Log on to http://$IP/config/index.php"
    echo "User: admin"
    echo "One-time password :" `more /tmp/123AURORAPASS`
    echo ""
    pause 'Press [Enter] key to stop...'
;;
*)
clear
pathtosrv=`pwd`
echo "Welcome to 123Aurora - Louviaux Jean-Marc

Usage : simply run as root $pathtosrv/123aurora.sh { admin | start | stop }"
;;
esac
exit 0