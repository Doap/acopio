#!/bin/bash
#set -x
month=`date +%m`
year=`date +%Y`
day=`date +%d`
hour=`date +%H`
minute=`date +%M`
BUCKET="burkeagro"
echo $year$month$day-$hour-$minute
echo $HOSTNAME
array=( ac3_wp_acopio ac3_wp_acopiometa ac3_wp_manifiesto ac3_wp_manifiestometa ac3_wp_tcp_orders ac3_wp_tcp_orders_costs ac3_wp_tcp_orders_costsmeta ac3_wp_tcp_orders_details ac3_wp_tcp_orders_detailsmeta ac3_wp_tcp_ordersmeta )
for i in "${array[@]}"
do
        echo $i
        FILE="/home/shawn/ac3/$i"
        FILETOZIP="$FILE-$year$month$day-$hour-$minute"
        ZIPPEDFILE="$FILE-$year$month$day-$hour-$minute.gz"
        ZIPPEDFILENAME="$i-$year$month$day-$hour-minute.gz"
        if [ -f $FILE ];
        then
        echo "File $FILE exists."
        mv $FILE $FILETOZIP
        gzip $FILETOZIP
        s3cmd put --config=/var/www/html/.s3cfg --no-check-md5 --no-encrypt --skip-existing $ZIPPEDFILE s3://$BUCKET/almidon/$HOSTNAME/

        S3FILE=$(s3cmd ls --config=/var/www/html/.s3cfg s3://$BUCKET/almidon/$HOSTNAME/$ZIPPEDFILENAME)
        echo $S3FILE

                if [[ ! -z "$S3FILE" ]];
                then
                #rm -f $ZIPPEDFILE
                fi

        fi
done

