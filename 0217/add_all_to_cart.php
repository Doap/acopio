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
        SELECT acopiometa_id, acopio_id, product_id, item_id, SUM( quantity ) as quantity, cajillas, status 
        FROM wp_acopiometa
        WHERE acopio_id = $acopio_id
	GROUP BY product_id
        ORDER BY product_id ASC;
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

?>
<script>
jQuery(document).ready(function() {
        var post_id = '<?php echo $product_id; ?>';
        var tcp_count = '<?php echo $quantity; ?>';
        data = 'action=tcp_shopping_cart_actions&to_do=add&tcp_add_to_shopping_cart=&tcp_count%5B%5D=<?php echo $quantity; ?>&tcp_post_id%5B%5D=<?php echo $product_id; ?>';

        jQuery.getJSON( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data ).done( function( response ) {
                tcpDispatcher.fire( <?php echo $product_id; ?> );
        } ).fail( function (error ) {
                tcpDispatcher.fire( <?php echo $product_id; ?>);
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
