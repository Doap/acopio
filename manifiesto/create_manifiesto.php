<?php
require_once("../wp-config.php");
require_once("../wp-load.php");
#require_once("ABSPATH/wp-content/plugins/user-switching/user-switching.php");
require_once("/var/www/html/acopio/wp-content/plugins/user-switching/user-switching.php");
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
//get_header();
//echo "Hello";
//echo count($_POST["orders"]);
//echo $_POST["orders"][1] . "\n";
//echo $_POST["submit"] . "\n";
$username = $current_user->user_login;
$user_id = get_current_user_id();
date_default_timezone_set('America/Managua');
$date = date('m/d/Y h:i:s a', time());
if (!current_user_can( 'switch_to_user', $user_id ) && current_user_switched())
{
	$url = user_switching::switch_back_url();
//	echo $url . PHP_EOL;
//	echo 'user_id = ' . $user_id . PHP_EOL;
	$old_user = user_switching::get_old_user();

	switch_to_user($old_user->ID);
	$user_id = get_current_user_id();
	$username = $current_user->user_login;
//	echo 'user_id = ' . $user_id . PHP_EOL;
//	echo 'old user_id = ' . $old_user->ID . PHP_EOL;
        //echo '<meta http-equiv="refresh" content="5; URL=' . $url . '">';

//exit;
}

$ordersCount = count($_POST["orders"]);
//var_dump($_POST);
if(isset($ordersCount))
{
//header("Location:list_user.php");

$manifiesto_insert = $wpdb->insert(
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
if ($manifiesto_insert) {
$manifiesto_id = $wpdb->insert_id;
// echo "\n" . $manifiesto_id . "\n";
for($i=0;$i<$ordersCount;$i++) 
        {

        $manifiesto_meta_insert = $wpdb->insert(
                'wp_manifiestometa',
                array(
                        'manifiesto_id' => $manifiesto_id,
                        'order_id' => $_POST["orders"][$i],
                        'buyer' => $username,
                        'received' => 0
                ),
                array(
                        '%s',
                        '%d',
                        '%s',
                        '%d'
                )
        );
if ($manifiesto_meta_insert) {
$manifiesto_meta_id = $wpdb->insert_id;
//        echo "\n manifiesto_meta_id = " . $manifiesto_meta_id . "\n";
//        echo "\n" . $pending_order['order_id'] . "\n";
        }
	}
}
}
?>
<link rel="stylesheet" type="text/css" href="styles.css" />
<form name="frmUser" method="post" action="">
<div style="width:800px;">
<table border="0" cellpadding="10" cellspacing="1" width="500" class="tblListForm" media="printer">
<tr><td>Manifiesto #: <?php echo $manifiesto_id;?></td></tr>
<tr><td>Fecha: <?php echo $date;?></td></tr>
<tr><td>Comprador: <?php echo $username;?></td></tr>
<tr class="listheader">
                        <td># Productor</td>
                        <td># de la Compra</td>
                        <td>Producto</td>
                        <td>Peso (kg)</td>
                        <td>Unidades</td>
</tr>


<?php
$i=0;
$manifiesto_orders = $wpdb->get_results(
        "
        SELECT * 
        FROM wp_manifiestometa
        WHERE manifiesto_id = $manifiesto_id; 
        "
,ARRAY_A);
//        WHERE manifiesto_id = '$manifiesto_id'; 
foreach ($manifiesto_orders as $manifiesto_order)
 {
if($i%2==0)
$classname="evenRow";
else
$classname="oddRow";
$order_id = $manifiesto_order['order_id'];
                        $order_details = $wpdb->get_results(
                                "
                                SELECT * FROM wp_tcp_orders_details where order_id = ABS($order_id);
                                "
,ARRAY_A);
	foreach ($order_details as $order_detail)
	{

	echo '<tr class="$classname">';
	$cust_id = $wpdb->get_var("SELECT customer_id FROM wp_tcp_orders where order_id = ABS($order_id);");
	$cust_info = get_user_by('id', $cust_id);
	$producer_id = $cust_info->user_login;
	$product_id = $order_detail['post_id'];
$weight = $order_detail['weight']/100;
$qty= $order_detail['qty_ordered'];
//echo "prod id = $producer_id";
$acopio_ids = $wpdb->get_col('SELECT acopio_id FROM wp_acopio where customer = "' . $producer_id . '" and DATE(created) = DATE(NOW());');
//var_dump($acopio_ids);
$qty_counter = $qty;
foreach ($acopio_ids as $acopio_id)
{
	$acopiometa_id = '';
	$num_lines = $wpdb->get_var("SELECT COUNT(acopiometa_id) FROM wp_acopiometa where acopio_id = $acopio_id and product_id = $product_id and status = 0;");
	if ($num_lines > 1)
	{
		$qty_product = $wpdb->get_var("SELECT SUM(quantity) FROM wp_acopiometa where acopio_id = $acopio_id and product_id = $product_id and status = 0;");
	}
	else
	{
		$qty_product = $wpdb->get_var("SELECT quantity FROM wp_acopiometa where acopio_id = $acopio_id and product_id = $product_id and status = 0;");
		$acopiometa_id = $wpdb->get_var("SELECT acopiometa_id FROM wp_acopiometa where acopio_id = $acopio_id and product_id = $product_id and quantity = $qty_product and status = 0;");
	}
	if ($qty_product == $qty && $qty_counter > 0)
	{
		$num_cajillas = $wpdb->get_var("SELECT SUM(cajillas) FROM wp_acopiometa where acopio_id = $acopio_id and product_id = $product_id;;");
		$acopio_cajillas += $num_cajillas;
		//echo "num = $num_cajillas    acopio = $acopio_cajillas";
		if ($acopiometa_id)
		{
		$wpdb->update( 
			'wp_acopiometa', 
			array( 
				'status' => 1	// string
			), 
			array( 'acopiometa_id' => $acopiometa_id ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
		);
		}
		else
		{
		$wpdb->update( 
			'wp_acopiometa', 
			array( 
				'status' => 1	// string
			), 
			array( 'acopio_id' => $acopio_id ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
		);
		}
	$qty_counter -= $qty_product;
/*
		$wpdb->update( 
			'wp_acopio', 
			array( 
				'status' => 1	// string
			), 
			array( 'acopio_id' => $acopio_id ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
		);
*/
	}
}

	echo '<td>' . $producer_id . '</td>';
	echo '<td>' . $order_detail['order_id'] . '</td>';
	echo '<td>' . $order_detail['name'] . '</td>';
//if ($order_detail['weight'])
if ($weight)
	{
	$qty = "N/A";
	}
	else
	{
	$weight= "N/A";
	}
	echo '<td>' . $weight . '</td>';
	echo '<td>' . $qty . '</td>';
	echo '</tr>';
	}
 }
echo "<tr><td># de Cajillas : $acopio_cajillas </td></tr>";
?>
<form>
<input type="button" value="Imprimir" onClick="window.print()">
</form>
