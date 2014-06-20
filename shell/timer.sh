#!/bin/bash

rm -rf /www/cache/www.upgrade.run.status.d /www/cache/www.restart.status.d /tmp/ltc /tmp/btc
mkdir /tmp/ltc/
chmod 777 /tmp/ltc
mkdir /tmp/btc/
chmod 777 /tmp/btc
chmod 777 /www/ -R
chown www-data:www-data /www/ -R

echo 'nihao'

count=0
while true ; do
	if [ $count -eq 0 ] ; then
		wget http://127.0.0.1/index.php?r=port/generatemac -O /dev/null >/dev/null 2>&1
	fi
	
	wget http://127.0.0.1/index.php?r=index/checkrun -O /dev/null >/dev/null 2>&1
	wget http://127.0.0.1/index.php?r=port/generatekey -O /dev/null >/dev/null 2>&1

	to_sync=$((count%10))
    	if [ $to_sync -eq 0 ] ; then
		wget http://127.0.0.1/index.php?r=sync/start -O /dev/null >/dev/null 2>&1
    	fi

	to_speed=$((count%15))
	if [ $to_speed -eq 0 ] ; then
		wget http://127.0.0.1/index.php?r=speed/index -O /dev/null >/dev/null 2>&1
	fi
	
	if [ $to_speed -eq 0 ] ; then
		wget http://127.0.0.1/index.php?r=sync/speed -O /dev/null >/dev/null 2>&1
	fi

	count=$((count+5))
	if [ $count -ge 600 ] ; then
		count=0
	fi
	
	sleep 10
done
exit 0
