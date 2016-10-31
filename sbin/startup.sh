#!/bin/sh

LOGFILE=/srv/SOA/log/$(basename $0 .sh).log
PATTERN="rabbitmq.php"
RECOVERY="php /srv/SOA/sbin/rabbitmq.php restart"

while true
do
    TIMEPOINT=$(date -d "today" +"%Y-%m-%d_%H:%M:%S")
    PROC=$(pgrep -o -f ${PATTERN})
    #echo ${PROC}
    if [ -z "${PROC}" ]; then
		${RECOVERY} >> $LOGFILE
		echo "[${TIMEPOINT}] ${PATTERN} ${RECOVERY}" >> $LOGFILE
    #else
        #echo "[${TIMEPOINT}] ${PATTERN} ${PROC}" >> $LOGFILE
    fi
sleep 5
done &
