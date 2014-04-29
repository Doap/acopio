<?php
require_once("../wp-load.php");
  if(isset($_POST) && count($_POST)){
	$action  = mysql_real_escape_string($_POST['action']);
	$product_id = mysql_real_escape_string($_POST['product_id']);
	$product_description = mysql_real_escape_string($_POST['product_description']);
	$peso_bruto = mysql_real_escape_string($_POST['peso_bruto']);
	$item_id = mysql_real_escape_string($_POST['item_id']);	
	$cajillas = mysql_real_escape_string($_POST['cajillas']);	
	$peso_neto = (int)$peso_bruto - ((int)$cajillas*1.6);
	$quantity = $peso_neto * 10;
	$peso_neto = mysql_real_escape_string($peso_neto);
	$save_action = "save_$product_id";
	$acopio_id = mysql_real_escape_string($_POST['acopio_id']);
	$status = 0;
	$row_id = time();

//		echo var_dump($_POST);
	if($action == $save_action){
		// Add code to save data into mysql
        $acopio_meta_insert = $wpdb->insert(
                'wp_acopiometa',
                array(
                        'acopio_id' => $acopio_id,
                        'product_id' => $product_id,
                        'item_id' => $row_id,
                        'quantity' => $quantity,
                        'cajillas' => $cajillas,
                        'status' => $status
                ),
                array(
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%d'
                )
        );
	
	if ($manifiesto_meta_insert) {
		$manifiesto_meta_id = $wpdb->insert_id;
        }

		//echo var_dump($_POST);
		echo json_encode(
			array(
				"success" => "1",
				"row_id"  => htmlentities($row_id),
				"product_id"   => htmlentities($product_id),
				"product_description"   => htmlentities($product_description),
				"peso_bruto"   => htmlentities($peso_bruto),
				"cajillas"   => htmlentities($cajillas),
				"peso_neto"   => htmlentities($peso_neto),
			)
		);
	}
	else if($action == "delete"){
		// Add code to remove record from database
		 $acopio_meta_delete = $wpdb->delete(
                		'wp_acopiometa',
                		array(
                		        'item_id' => $item_id
                		),
                		array(
                		        '%d'
                		));


		echo json_encode(
			array(
				"success" => "1",
				"item_id"  => $item_id					
			)	 
		);
	}
  else{
	echo json_encode(
		array(
			"success" => "0",
			"item_id"  => "No POST data set"					
		)	 
	);
  }
}
?>
