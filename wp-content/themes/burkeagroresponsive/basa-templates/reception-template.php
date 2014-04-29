<?php
/*
 * Template Name: Reception Page
 * Description: A Page Template for Receiving Product at the plant 
 */


//Trying to comment out
//require_once("../wp-config.php");
//require_once("../wp-load.php");
//$wp->init(); $wp->parse_request(); $wp->query_posts();
//$wp->register_globals();
//$wp->send_headers();



get_header();
if ( !is_user_logged_in() ) {
    echo 'Tiene que ingresar su usuario y contraseña para continuar...';
	get_footer();
} else {
show_admin_bar(true);
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$user_display_name = $current_user->display_name;
echo '<br><b>Persona recibiendo la fruta: </b>' . $user_display_name . '<br><b># Usuario: </b>' . $username;
echo '<title>Recepcionar Fruta en Planta</title>';
$url = site_url('/planta/');
echo '<link rel="stylesheet" type="text/css" href="' . $url . 'styles.css" />';
echo '<script language="javascript" src="' . $url . 'products.js" type="text/javascript"></script>';
echo '<form name="frmUser" method="post" action="" target="_blank">';
echo '<div style="width:800px;">';
echo '<table id="tbl" border="0" cellpadding="10" cellspacing="1" width="500" class="tblListForm">';
echo '<tr class="listheader">';
echo '<td>Recibir estos productos</td>';
echo '<td>Descripcion</td>';
echo '<td>Precio</td>';
echo '</tr>';
$i=0;
$wpdb->show_errors();
//$username="bob";
date_default_timezone_set('America/Managua');
$date = date('m/d/Y h:i:s a', time());
//$acopio_array= $wpdb->get_col( "SELECT distinct order_id FROM wp_manifiestometa ORDER BY ABS(order_id) ASC;" );
//$orders_imported = implode( ",", $orders_imported_array);
$acopio_products = $wpdb->get_results(
        "
	SELECT ID, post_title as Descripcion
	FROM wp_posts 
	WHERE post_type = 'tcp_reception' and post_status = 'publish' 
	ORDER BY ID ASC;
        "
,ARRAY_A);
if (!empty($acopio_products ))
{
foreach ($acopio_products as $acopio_product )
 {
if($i%2==0)
$classname="evenRow";
else
$classname="oddRow";
$product_id = $acopio_product['ID'];
$acopio_product['Descripcion'] = __($acopio_product['Descripcion']);
echo '<tr class="' . $classname . '">';
echo '<td><input type="checkbox" name="products[]" value="' . $acopio_product['ID'] . '" unchecked ></td>';
echo '<td><input type="hidden" name="product_descriptions[]" value="' . $acopio_product['Descripcion'] . '">' . $acopio_product['Descripcion'] . '</td>';
//echo '<td><input type="hidden" name="product_descriptions[]" value="' . _e($acopio_product['Descripcion']) . '">' . _e($acopio_product['Descripcion']) . '</td>';
echo '<td>' . $acopio_product['Precio'] . '</td>';
echo '</tr>';
$i++;
}
echo '<tr class="listheader">';
//echo '<td colspan="3"><input type="button" name="create" value="Create" onClick="setCreateAction();" /> <input type="button" name="delete" value="Delete"  onClick="setDeleteAction();" /></td>';
echo '<td>Manifiesto ID <input type="text" name="manifiesto_id" id="manifiesto_id" value="" autofocus required></td><td colspan="2"><input type="button" name="create" value="Recibir Estos" onClick="setCreateAction();" /></td>';
}
else
{
echo '<td colspan="3"><b>No hay ordenes sin procesar</b></td>';
}
echo '</tr>';
echo '</table>';
echo '</form>';
echo '</div>';
}
//print_r($tag_test);
//print_r($test_get_obj);
get_footer();
?>
