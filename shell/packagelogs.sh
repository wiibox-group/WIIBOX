#!/bin/bash

# single miner mode - ltc log
if [ -f "/www/logs/l-all.log" ] ; then
	tail -6000 /www/logs/l-all.log > /www/logs/exp-l-all.log
fi
# single miner mode - btc log
if [ -f "/www/logs/b-all.log" ] ; then
	tail -6000 /www/logs/b-all.log > /www/logs/exp-b-all.log
fi

# multi miner mode - ltc log
if [ -f "/www/logs/l-ttyUSB1.log" ] ; then
	tail -2000 /www/logs/l-ttyUSB1.log > /www/logs/exp-l-ttyUSB1.log
fi
if [ -f "/www/logs/l-ttyUSB3.log" ] ; then
	tail -2000 /www/logs/l-ttyUSB3.log > /www/logs/exp-l-ttyUSB3.log
fi
if [ -f "/www/logs/l-ttyUSB5.log" ] ; then
	tail -2000 /www/logs/l-ttyUSB5.log > /www/logs/exp-l-ttyUSB5.log
fi
if [ -f "/www/logs/l-ttyUSB7.log" ] ; then
	tail -2000 /www/logs/l-ttyUSB7.log > /www/logs/exp-l-ttyUSB7.log
fi
if [ -f "/www/logs/l-ttyUSB9.log" ] ; then
	tail -2000 /www/logs/l-ttyUSB9.log > /www/logs/exp-l-ttyUSB9.log
fi

# multi miner mode - btc log
if [ -f "/www/logs/b-ttyUSB0.log" ] ; then
	tail -2000 /www/logs/b-ttyUSB0.log > /www/logs/exp-b-ttyUSB0.log
fi
if [ -f "/www/logs/b-ttyUSB2.log" ] ; then
	tail -2000 /www/logs/b-ttyUSB2.log > /www/logs/exp-b-ttyUSB2.log
fi
if [ -f "/www/logs/b-ttyUSB4.log" ] ; then
	tail -2000 /www/logs/b-ttyUSB4.log > /www/logs/exp-b-ttyUSB4.log
fi
if [ -f "/www/logs/b-ttyUSB6.log" ] ; then
	tail -2000 /www/logs/b-ttyUSB6.log > /www/logs/exp-b-ttyUSB6.log
fi
if [ -f "/www/logs/b-ttyUSB8.log" ] ; then
	tail -2000 /www/logs/b-ttyUSB8.log > /www/logs/exp-b-ttyUSB8.log
fi

# package log files
zip /www/wiibox-log-$1.zip /www/logs/exp-*.log
exit 0
