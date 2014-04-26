<?php
  if(isset($_POST) && count($_POST)){
	$action  = mysql_real_escape_string($_POST['action']);
	$fname   = mysql_real_escape_string($_POST['fname']);
	$lname   = mysql_real_escape_string($_POST['lname']);
	$email   = mysql_real_escape_string($_POST['email']);
	$phone   = mysql_real_escape_string($_POST['phone']);	
	$item_id = mysql_real_escape_string($_POST['item_id']);	

	if($action == "save"){
		// Add code to save data into mysql
		echo json_encode(
			array(
				"success" => "1",
				"row_id"  => time(),
				"fname"   => htmlentities($fname),
				"lname"   => htmlentities($lname),
				"email"   => htmlentities($email),
				"phone"   => htmlentities($phone),
			)
		);
	}
	else if($action == "delete"){
		// Add code to remove record from database
		unset($pending_orders[$item_id]);
		echo json_encode(
			array(
				"success" => "1",
				"item_id"  => $item_id					
			)	 
		);
	}
  }else{
	echo json_encode(
		array(
			"success" => "0",
			"item_id"  => "No POST data set"					
		)	 
	);
  }
?>
