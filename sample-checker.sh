#!/bin/bash
#cp sample-checker.sh checker.sh
#chmod +x checker.sh
#replace basedir with your path to project
#and add to /etc/crontab: */10 * * * * root /root/torrent/checker.sh >/dev/null 2>&1
basedir="/root/torrent"
php=""
((maxdelay = 1 * 60))
((delay = $RANDOM % maxdelay))
((delay = 60 * delay))
/bin/sleep $delay
cd $basedir
date > lastrun.log
$php checker.php >> lastrun.log
$php filter.php >> lastrun.log
#$php reporter.php >> lastrun.log