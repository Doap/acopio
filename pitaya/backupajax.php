<?php
  if(isset($_POST) && count($_POST)){
	$action  = mysql_real_escape_string($_POST['action']);
	$product_id = mysql_real_escape_string($_POST['product_id']);
	$product_description = mysql_real_escape_string($_POST['product_description']);
	$peso_bruto = mysql_real_escape_string($_POST['peso_bruto']);
	$item_id = mysql_real_escape_string($_POST['item_id']);	
	$cajillas = mysql_real_escape_string($_POST['cajillas']);	
	$peso_neto = (int)$peso_bruto - ((int)$cajillas*1.6);
	$peso_neto = mysql_real_escape_string($peso_neto);
	$save_action = 'save_' . $product_id;
	if($action == $save_action){
		// Add code to save data into mysql
		//echo var_dump($_POST);
		echo json_encode(
			array(
				"success" => "1",
				"row_id"  => time(),
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
		echo json_encode(
			array(
				"success" => "1",
				"item_id"  => $item_id					
			)	 
		);
	}
	else if($action == "save_456"){
		// Add code to remove record from database
		echo json_encode(
			array(
				"success" => "1",
                                "row_id"  => time(),
                                "product_id"   => htmlentities($product_id),
                                "product_description"   => htmlentities($product_description),
                                "peso_bruto"   => htmlentities($peso_bruto),
                                "cajillas"   => htmlentities($cajillas),
                                "peso_neto"   => htmlentities($peso_neto),
			)	 
		);
  }else{
	echo json_encode(
		array(
			"success" => "0",
			"item_id"  => "No POST data set"					
		)	 
	);
  }
?>
