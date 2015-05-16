#!/bin/bash
cd /home/shawn/to_process/ac3
rm -f /home/shawn/to_process/ac3/latest 
FILEPATH=`dirname $(readlink -f $0)`
echo $FILEPATH
FILE=$FILEPATH/latest
LOG=$FILEPATH/processing.log
/usr/bin/s3cmd get s3://burkeagro/ac3/latest /home/shawn/to_process/ac3/ >> $LOG
i=0
while read line; do
  if [[ $line != "" ]]; then
    arr[$i]=$line
    let i=$i+1
  fi
done < "$FILE"
if [[ $i == 0 ]]; then
  exit;
fi
echo "Number of files in the array: ${#arr[*]}" >> $LOG
for i in ${!arr[*]};
do
  sql_dir=`printf "%s" "${arr[$i]}"`
  mkdir $FILEPATH/$sql_dir
  echo "Copying $sql_dir ..." >> $LOG

  /usr/bin/s3cmd get "s3://burkeagro/ac3/$sql_dir/*" /home/shawn/to_process/ac3/$sql_dir >> $LOG
done
gunzip /home/shawn/to_process/ac3/$sql_dir/*
array=( ac3_wp_acopio ac3_wp_acopiometa ac3_wp_manifiesto ac3_wp_manifiestometa ac3_wp_tcp_orders ac3_wp_tcp_orders_costs ac3_wp_tcp_orders_costsmeta ac3_wp_tcp_orders_details ac3_wp_tcp_orders_detailsmeta ac3_wp_tcp_ordersmeta )
for i in "${array[@]}"
do
        echo $i
        FILE="/home/shawn/to_process/ac3/$sql_dir/$i"
                if [[ ! -z "$FILE" ]];
                then
		echo mysql -u root -pfr1ck0ff wp --local-infile=1 -e "LOAD DATA LOCAL INFILE '$FILE' INTO TABLE $i FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 ROWS;" >> $LOG
		mysql -u root -pfr1ck0ff wp --local-infile=1 -e "LOAD DATA LOCAL INFILE '$FILE' INTO TABLE $i FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 1 ROWS;" >> $LOG
                rm -f $ZIPPEDFILE
                echo "found file $FILE and processed" >> $LOG
                fi

done


