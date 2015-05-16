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
rm -f /home/shawn/ac3/latest
#rm -f /home/shawn/ac3/ac3*
echo $year$month$day-$hour-$minute >> /home/shawn/ac3/latest
array=( ac3_wp_acopio ac3_wp_acopiometa ac3_wp_manifiesto ac3_wp_manifiestometa ac3_wp_tcp_orders ac3_wp_tcp_orders_costs ac3_wp_tcp_orders_costsmeta ac3_wp_tcp_orders_details ac3_wp_tcp_orders_detailsmeta ac3_wp_tcp_ordersmeta )
for i in "${array[@]}"
do
        echo $i
        FILE="/home/shawn/ac3/$i"
        FILETOZIP="$FILE-$year$month$day-$hour-$minute"
        ZIPPEDFILE="$FILE.gz"
        ZIPPEDFILENAME="$i.gz"
        if [ -f $FILE ];
        then
        echo "File $FILE exists."
        #mv $FILE $FILETOZIP
        gzip $FILE
        s3cmd put --no-check-md5 --no-encrypt --skip-existing $ZIPPEDFILE s3://$BUCKET/ac3/$year$month$day-$hour-$minute/

        S3FILE=$(s3cmd ls s3://$BUCKET/ac3/$year$month$day-$hour-$minute/$ZIPPEDFILENAME)
        echo $S3FILE

                if [[ ! -z "$S3FILE" ]];
                then
                rm -f $ZIPPEDFILE
		echo "file copied successfully"
                fi

        fi
done
s3cmd put --no-check-md5 --no-encrypt --skip-existing /home/shawn/ac3/latest s3://$BUCKET/ac3/

