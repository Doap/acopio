<?php
//$conn = mysql_connect("localhost","root","");
//mysql_select_db("phppot_examples",$conn);
//$result = mysql_query("SELECT * FROM users");
require_once("../wp-config.php");
require_once("../wp-load.php");
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
get_header();
if ( !is_user_logged_in() ) {
    echo 'Tiene que ingresar su usuario y contraseña para continuar...';
	get_footer();
} else {
$current_user = wp_get_current_user();
$username = $current_user->user_login;
echo 'Bienvenidos, ' . $username . '!';
echo '<title>Crear Manifiesto</title>';
echo '<link rel="stylesheet" type="text/css" href="styles.css" />';
echo '<script language="javascript" src="orders.js" type="text/javascript"></script>';
echo '<form name="frmUser" method="post" action="" target="_blank">';
echo '<div style="width:800px;">';
echo '<table id="tbl" border="0" cellpadding="10" cellspacing="1" width="500" class="tblListForm">';
echo '<tr class="listheader">';
echo '<td>Agregar al Manifiesto</td>';
echo '<td># de la Compra</td>';
echo '<td>Fecha</td>';
echo '<td>Productor</td>';
echo '<td>Valor</td>';
echo '</tr>';
$i=0;
$wpdb->show_errors();
//$username="bob";
date_default_timezone_set('America/Managua');
$date = date('m/d/Y h:i:s a', time());
$orders_imported_array = $wpdb->get_col( "SELECT distinct order_id FROM wp_manifiestometa ORDER BY ABS(order_id) ASC;" );
$orders_imported = implode( ",", $orders_imported_array);
$pending_orders = $wpdb->get_results(
        "
        SELECT order_id, created_at, billing_company 
        FROM wp_tcp_orders
        WHERE status = 'PROCESSING' and order_id NOT IN ($orders_imported); 
        "
,ARRAY_A);
if (!empty($pending_orders))
{
foreach ($pending_orders as $pending_order)
 {
if($i%2==0)
$classname="evenRow";
else
$classname="oddRow";
$order_id = $pending_order['order_id'];
                        $order_value = $wpdb->get_var($wpdb->prepare(
                                "
                                SELECT SUM(qty_ordered*original_price) FROM wp_tcp_orders_details where order_id = ABS(%s)
                                ",
                                $order_id
) );
echo '<tr class="' . $classname . '">';
echo '<td><input type="checkbox" name="orders[]" value="' . $pending_order['order_id'] . '" checked ></td>';
echo '<td>' . $pending_order['order_id'] . '</td>';
echo '<td>' . $pending_order['created_at'] . '</td>';
echo '<td>' . $pending_order['billing_company'] . '</td>';
echo '<td>' . $order_value . '</td>';
echo '</tr>';
$i++;
}
echo '<tr class="listheader">';
echo '<td colspan="5"><input type="button" name="create" value="Create" onClick="setCreateAction();" /> <input type="button" name="delete" value="Delete"  onClick="setDeleteAction();" /></td>';
}
else
{
echo '<td colspan="5"><b>No hay ordenes sin procesar</b></td>';
}
echo '</tr>';
echo '</table>';
echo '</form>';
echo '</div>';
}
get_footer();
?>
