<?php
require_once("../wp-config.php");
require_once("../wp-load.php");
function curHostURL() {
 $HostURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$HostURL .= "s";}
 $HostURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $HostURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
 } else {
  $HostURL .= $_SERVER["SERVER_NAME"];
 }
 return $HostURL;
}
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
//echo $_POST['acopio_id'];
$acopio_id = $_POST['acopio_id'];
$products_to_add = $wpdb->get_results(
        "
        SELECT *
        FROM wp_acopiometa
        WHERE acopio_id = $acopio_id
        ORDER BY acopiometa_id ASC;
        " 
,ARRAY_A);
get_header();
if (!empty($products_to_add))
{        
foreach ($products_to_add as $product_to_add)
 {
	$acopiometa_id = $product_to_add['acopiometa_id'];
	$acopio_id = $product_to_add['acopio_id'];
	$product_id = $product_to_add['product_id'];
	$item_id = $product_to_add['item_id'];
	$quantity = $product_to_add['quantity'];
	$cajillas = $product_to_add['cajillas'];

//	echo $acopiometa_id . '<br>';
//	echo $acopio_id . '<br>';
//	echo $product_id . '<br>';
//	echo $item_id . '<br>';
//	echo $quantity . '<br>';
//	echo $cajillas . '<br>';
//	echo '<br>';
//$request_url = curHostURL();
//$request_url .= '/wp-admin/admin-ajax.php?action=tcp_shopping_cart_actions&to_do=add&tcp_add_to_shopping_cart=&tcp_count%5B%5D=' . $quantity .'&tcp_post_id%5B%5D=' . $product_id;
//$response = http_get('/wp-admin/admin-ajax.php?action=tcp_shopping_cart_actions&to_do=add&tcp_add_to_shopping_cart=&tcp_count%5B%5D=' . $quantity .'&tcp_post_id%5B%5D=' . $product_id, array("timeout"=>1), $info);
//print_r($info);
//$curl = curl_init();
// Set some options - we are passing in a useragent too here
//curl_setopt_array($curl, array(
//    CURLOPT_RETURNTRANSFER => 1,
//    CURLOPT_URL => $request_url
//));
// Send the request & save response to $resp
//$resp = curl_exec($curl);
// Close request to clear up some resources
//curl_close($curl);
//print_r($resp);
//wp_remote_get($request_url);
?>
<script>
jQuery(document).ready(function(){
	var post_id = '<?php echo $product_id; ?>';
	var tcp_count = '<?php echo $quantity; ?>';
	data = 'action=tcp_shopping_cart_actions&to_do=add&tcp_add_to_shopping_cart=&tcp_count%5B%5D=' . tcpcount . '&tcp_post_id%5B%5D=' . post_id;
        feedback.show();

        jQuery.getJSON( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data ).done( function( response ) {
                feedback.hide();
                tcpDispatcher.fire( post_id );
        } ).fail( function (error ) {
                feedback.hide();
                tcpDispatcher.fire( post_id );
        } );
});
</script>
<?php
 }
}
else
{
echo 'Nothing to do';
}
get_footer();
?>
