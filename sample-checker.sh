#!/bin/bash
#rename sample-checker.sh to checker.sh
#chmod +x checker.sh
#replace basedir with your path to project
#and add to /etc/crontab: */10 * * * * root /path/to/checker.sh >/dev/null 2>&1
basedir="/root/project"
cd $basedir
date > lastrun.log
/usr/bin/php checker.php >> lastrun.log
/usr/bin/php filter.php >> lastrun.log
/usr/bin/php reporter.php >> lastrun.log