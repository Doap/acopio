#!/usr/bin/php
<?php
function backup_table_scripts()
{
	$mysqli = new mysqli("localhost", "root", "fr1ck0ff", "wp");


	/* check connection */
	if ($mysqli->connect_errno)
	{
	    printf("Connect failed: %s\n", $mysqli->connect_error);
	    exit();
	}

	$query = 'SELECT * FROM acopio_tbl_cfg;';
	if ($tbl_result = $mysqli->query($query)) 
	{

	    /* fetch associative array */
	    $table_ids = $tbl_result->fetch_assoc();
	}
	$query = 'SELECT * FROM acopio_config;';

	if ($result = $mysqli->query($query)) 
	{

		/* fetch associative array */
		$row = $result->fetch_assoc();

		$script = '#!/bin/bash' . PHP_EOL;
		foreach($row as $key => $value)
		{
			$mykey = $key;
			//echo $mykey . PHP_EOL;
			$myvalue = $value;
			if ($mykey == 'acopio_machine')
			{
				$ac= 'ac' . $myvalue;
				echo 'ac = ' . $ac . PHP_EOL;
			}
			else
			{
				$new_query='SELECT * FROM ' . $mykey . ' WHERE ' . $table_ids[$mykey] . ' > ' . $myvalue . ' LIMIT 1;';
				if ($result = $mysqli->query($new_query)) 
				{
					$script .= 'mysql -u root -pfr1ck0ff wp -e "SELECT * FROM ' . $mykey . ' WHERE ' . $table_ids[$mykey] . ' > ' . $myvalue . ';" > /home/shawn/' . $ac . '/' . $ac . '_' . $mykey . PHP_EOL;
				}
			}
		}
		/* free result set */
		$result->free();
	}

	/* close connection */
	$mysqli->close();


	$acopio_machine=$row['acopio_machine'];
	$wp_acopio=$row['wp_acopio'];
	$wp_acopiometa=$row['wp_acopiometa'];
	$wp_manifiesto=$row['wp_manifiesto'];
	$wp_manifiestometa=$row['wp_manifiestometa'];
	$wp_tcp_orders=$row['wp_tcp_orders'];
	$wp_tcp_orders_costs=$row['wp_tcp_orders_costs'];
	$wp_tcp_orders_costsmeta=$row['wp_tcp_orders_costsmeta'];
	$wp_tcp_orders_details=$row['wp_tcp_orders_details'];
	$wp_tcp_orders_detailsmeta=$row['wp_tcp_orders_detailsmeta'];
	$wp_tcp_ordersmeta=$row['wp_tcp_ordersmeta'];

	/*
	echo $acopio_machine;
	echo $wp_acopio;
	echo $wp_acopiometa;
	echo $wp_manifiesto;
	echo $wp_manifiestometa;
	echo $wp_tcp_orders;
	echo $wp_tcp_orders_costs;
	echo $wp_tcp_orders_costsmeta;
	echo $wp_tcp_orders_details;
	echo $wp_tcp_orders_detailsmeta;
	echo $wp_tcp_ordersmeta;

	echo $query . PHP_EOL;
	echo 'result' . $result . PHP_EOL;
	*/

	//save file
	$handle = fopen('/home/shawn/dbpush.sh', 'w+');
	fwrite($handle,$script);
	fclose($handle);
}
 
backup_table_scripts();
?>

