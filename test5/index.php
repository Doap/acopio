<?php
//include("config.php");
require_once("../wp-config.php");
require_once("../wp-load.php");
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
get_header();
?>
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
 <head>
  <title>Ajax Table Editing</title>
  <script src="js/jquery.js"></script>        
  <script src="js/script.js"></script>        
  <script src="js/jquery-ui-1.8.17.custom.min.js"></script>  
  <link rel="stylesheet" href="css/style.css">
 </head>
 <body>
 <br>
   <div style="margin-left: 20%;margin-top: 5%;">
	  <input type="button" value="Add Record" id="add_new"><p>
	  <input type="button" value="Crear Manifiesto" id="create"><p>
	  <table width="70%" border="0" cellpadding="0" cellspacing="0" class="table-list">
		<tr>
			<th width="20%"># de la Compra</th>
			<th width="20%">Fecha</th>
			<th width="40%">Productor</th>
			<th width="20%">Valor</th>
			<th width="20%">Delete</th>
		</tr>
		<?php
			/* PDO 
			$sql = "SELECT * FROM info";
			$q = $conn->prepare($sql);
			$q->execute(array($title));
			$q->setFetchMode(PDO::FETCH_BOTH);
			// fetch
			while($r = $q->fetch()){
			*/
//			$res = mysql_query("select * from info");
$wpdb->show_errors();
//$username="bob";
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$pending_orders = $wpdb->get_results( 
	"
	SELECT order_id, created_at, billing_company 
	FROM wp_tcp_orders
	WHERE status = 'PROCESSING'; 
	"
,ARRAY_A);
$i=0;
//			while($r = mysql_fetch_assoc($res)){
			foreach ($pending_orders as $pending_order) 
			{
			$i++;
			$order_id = $pending_order['order_id'];
			$order_value = $wpdb->get_var($wpdb->prepare(
				"
				SELECT SUM(qty_ordered*original_price) FROM wp_tcp_orders_details where order_id = ABS(%s)
				",
				$order_id
) );
				echo '<tr>
						<td>'.$pending_order['order_id'].'</td>
						<td>'.$pending_order['created_at'].'</td>
						<td>'.$pending_order['billing_company'].'</td>
						<td>'.$order_value.'</td>
						<td><a href="#" id="'.$i.'" class="del">Delete</a></td>
					  </tr>';
			}

		?>

	  </table>

	  <table width="70%" border="0" cellpadding="0" cellspacing="0" class="table-list">
<form action="http://www.inthingslimited.com/test2/index.php" method="get" name="save-manifest" id="save-manifest">
<tr>
<?php if (!isset($_GET['manifest-submitted'])) {  ?>
	<td colspan=4> <input type="hidden" name="username" value="<?php echo $username;?>"><?php echo $username;?></td>
	<td colspan=4> <input type="hidden" name="val1" value="<?php echo $username;?>"><?php echo $username;?></td>
	<td colspan=4> <input type="hidden" name="val2" value="<?php echo $username;?>"><?php echo $username;?></td>
	<td colspan=4> <input type="hidden" name="val3" value="<?php echo $username;?>"><?php echo $username;?></td>
	<input type="hidden" name="manifest-submitted" value="yes">
	<td><input type="submit" value="Submit"></td>
<?php } ?>
<?php if (isset($_GET['manifest-submitted']))
 {
 echo "did it";
$manifiesto_id = $wpdb->insert( 
	'wp_manifiesto', 
	array( 
		'buyer' => $username, 
		'comments' => '' 
	), 
	array( 
		'%s', 
		'%s' 
	) 
);
 echo "\n" . $manifiesto_id . "\n";

foreach ($pending_orders as $pending_order)
	{

	$manifiesto_meta_id = $wpdb->insert(
        	'wp_manifiestometa',
        	array(
                	'manifiesto_id' => $manifiesto_id,
                	'order_id' => $pending_order['order_id'],
			'buyer' => $username,
			'received' => 0
        	),
        	array(
        	        '%s',
        	        '%s',
        	        '%s',
        	        '%d'
        	)
	);
	
 	echo "\n manifiesto_meta_id = " . $manifiesto_meta_id . "\n";
 	echo "\n" . $pending_order['order_id'] . "\n";
	}
 }
?>
</tr>
</form>
</table>
	</div>
	<div class="entry-form">
		<form name="userinfo" id="userinfo"> 
		<table width="100%" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="2" align="right"><a href="#" id="close">Close</a></td>
			</tr>
			<tr>
				<td>First Name</td>
				<td><input type="text" name="fname"></td>
			</tr>
			<tr>
				<td>Last Name</td>
				<td><input type="text" name="lname"></td>
			</tr>
			<tr>
				<td>Email</td>
				<td><input type="text" name="email"></td>
			</tr>
			<tr>
				<td>Phone Number</td>
				<td><input type="text" name="phone"></td>
			</tr>
			<tr>
				<td align="right"></td>
				<td><input type="button" value="Save" id="save"><input type="button" value="Cancel" id="cancel"></td>
			</tr>
		</table>
		</form>
	</div>
 </body>
</html>
