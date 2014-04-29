<?php
/*
 * Template Name: Acopiar Pitaya Page
 * Description: A Page Template for the Manifiesto 
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
if ($user_display_name == "")
{
	$user_display_name = $current_user->user_first_name . ' ' . $current_user->user_lastname;
	if ($user_display_name == "")
	{
		$user_display_name = '<b>Favor verificar los campos de los nombres en el Perfil del productor</b>';
	}
}
$raw = json_decode( stripslashes( $_COOKIE[OLDUSER_COOKIE] ) );
$raw_value = $raw['0'];
$old_users = explode("|", $raw_value);
$buyer = $old_users['0'];
if ($buyer == "")
{
	echo 'No ha seleccionado un productor!  Redirijiendo a esa pantalla...';
//	echo '<!--:es-->No ha seleccionado un productor!  Redirijiendo a esa pantalla...<!--:--><!--:en-->You have not selected a producer!  Redirecting to that screen...<!--:-->';
	$url = site_url('/wp-admin/users.php?role=customer');
	echo '<meta http-equiv="refresh" content="5; URL=' . $url . '">';
	
}
echo '<br><b>Comprador: </b>' . $buyer;
echo '<br><b>Productor: </b>' . $user_display_name . '<br><b># ID de Productor: </b>' . $username;
$url = site_url('/myprofile/');
echo '<br><a href="' . $url . '">Si el nombre del productor no es correcto, haga clic aqui para corregirlo</a>';
//echo '<b>Comprador: ' . $buyer . '</b>';
echo '<title>Acopiar Fruta</title>';
$url = site_url('/pitaya/');
echo '<link rel="stylesheet" type="text/css" href="' . $url . 'styles.css" />';
echo '<script language="javascript" src="' . $url . 'products.js" type="text/javascript"></script>';
echo '<form name="frmUser" method="post" action="" target="_blank">';
echo '<div style="width:800px;">';
echo '<table id="tbl" border="0" cellpadding="10" cellspacing="1" width="500" class="tblListForm">';
echo '<tr class="listheader">';
echo '<td>Acopiar estos productos</td>';
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
$argu = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all'); 
$test_get_obj = get_objects_in_term( 3, 'tcp_product_tag', $argu );
$tag_test = get_term_by( 'id', 3, 'tcp_product_tag', ARRAY_A);
$tcp_products_list = implode(",", $test_get_obj);
$acopio_products = $wpdb->get_results(
        "
	SELECT p.ID, p.post_title as Descripcion, m.meta_value as Precio
	FROM wp_posts p JOIN wp_postmeta m ON p.ID = m.post_id
	WHERE p.post_type = 'tcp_product' and p.post_status = 'publish' and m.meta_key = 'tcp_price' and p.ID IN ($tcp_products_list)
	ORDER BY p.ID ASC;
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
echo '<tr class="' . $classname . '">';
echo '<td><input type="checkbox" name="products[]" value="' . $acopio_product['ID'] . '" unchecked ></td>';
echo '<td><input type="hidden" name="product_descriptions[]" value="' . $acopio_product['Descripcion'] . '">' . $acopio_product['Descripcion'] . '</td>';
echo '<td>' . $acopio_product['Precio'] . '</td>';
echo '</tr>';
$i++;
}
echo '<tr class="listheader">';
//echo '<td colspan="3"><input type="button" name="create" value="Create" onClick="setCreateAction();" /> <input type="button" name="delete" value="Delete"  onClick="setDeleteAction();" /></td>';
echo '<td colspan="3"><input type="button" name="create" value="Acopiar Estos" onClick="setCreateAction();" /></td>';
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
