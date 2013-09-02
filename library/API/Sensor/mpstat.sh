#!/bin/sh

if [ -n $1 ]; then
	aphaea="/var/www/development/Aphaea";
else
	aphaea=$1;
fi
offset=2
db="$aphaea/db/sensor/cpu[`date +%F`].log"

str=`mpstat -P ALL | grep "all" | sed "s/ \+/ /g"`
#echo $str
#echo `echo $str | cut -d" " -f1`
timestamp=$(echo "(`echo $str | cut -d" " -f1 | cut -d: -f1` * 3600) + (`echo $str | cut -d" " -f1 | cut -d: -f2` * 60) + `echo $str | cut -d" " -f1 | cut -d: -f3`" | bc)
ampm=$(echo $str | cut -d" " -f2)
#echo "+$timestamp:`echo $str | cut -d' ' -f2`:$ampm:..."
if [ "PM" = $ampm ] && [ `echo $str | cut -d: -f1` -ne 12 ]; then
	timestamp=`echo "$timestamp + (12*60*60)" | bc`
else
	timestamp=`echo $timestamp`
fi
#echo "+$timestamp:..."
#echo $db
echo "+$timestamp:`echo $str | cut -d" " -f$offset- | sed 's/all //g' | sed 's/PM //g' | sed 's/AM //g' | sed 's/ \+/:/g'`" >> $db

