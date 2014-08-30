<?php
/**
 * This file is part of TheCartPress.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<html>
<head>
<meta charset="UTF-8" />
<title>

<?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?>

</title>
<style>
	.tcp_order_page_name {
		text-align: left;
	}
	body,td,th {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		color: #333;
	}
	a:link {
		color: #C00;
		text-decoration: none;
	}
	a:hover {
		color: #960000;
		text-decoration: none;
	}
	a:visited {
		text-decoration: none;
		color: #C00;
	}
	a:active {
		text-decoration: none;
		color: #960000;
	}
	.tcp_shopping_cart_table .tcp_cart_name {
		text-align: left;
		bbackground-color: #f0f0f0;
		width: inherit;
		font-size: 12px;
	}
	.tcp_shopping_cart_table .tcp_cart_name a{
		text-align: left;
		text-decoration: none;
	}
	.tcp_shopping_cart_table {
		width: 90%;
		border:0px;
	}
	.tcp_shopping_cart_table tr th,
	.tcp_shopping_cart_table thead th {
		background-color: #333;
		padding: 4px 10px;
		line-height: 22px;
		color: #CCC;
	}
	.tcp_shopping_cart_table tr td {
		background-color: #f7f7f7;
		font-size: 11px;
		padding: 4px 10px;
		border-top: 1px dotted #ccc;
	}

	.tcp_shopping_cart_table tr.odd td {
		background-color: #FfF7FC;
	}
	
	#shipping_info {
		width: 50%;
		float: left;
	}

	#tcp_order_id th,
	#tcp_order_id td,
	#tcp_status th,
	#tcp_status td {
		text-align: left !important;
	}
</style>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>

<body>

<h1 id="site-title"><?php bloginfo( 'name' ); ?></h1>

<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
<table id="tcp_order_id" width="100%" cellpading="0" cellspacing="0">
		<tbody><tr valign="top">
			<th class="tcp_order_id_row" scope="row" style="text-align: left; width:160px;">
<?php if ( isset( $_REQUEST['order_id'] ) ) {
	require_once( TCP_CLASSES_FOLDER . 'OrderPage.class.php' );
global $wpdb;
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$raw = json_decode( stripslashes( $_COOKIE[OLDUSER_COOKIE] ) );
$raw_value = $raw['0'];
$old_users = explode("|", $raw_value);
$buyer = $old_users['0'];
?>
<b>Comprador: </b></th>
			<td class="tcp_order_id_value tcp_order_id"> <?php echo $buyer; ?>
</td>
		</tr>
</tbody></table>
<?php
	$order_id = $_REQUEST['order_id'];
//echo PHP_EOL . '<br>source = <br>' . var_dump($source) . PHP_EOL;
//echo PHP_EOL . '<br>request = <br>' . var_dump($_REQUEST) . PHP_EOL; 
$order_total = $wpdb->get_var("SELECT SUM(original_price * qty_ordered) FROM wp_tcp_orders_details where order_id = $order_id");
//echo "order total = $order_total";
/*
foreach( $source->get_orders_details() as $order_detail )
{
	$price = $order_detail->get_price() - $discount;
               $price = round( $price, $decimals );
               $tax = $price * $order_detail->get_tax() / 100;
                $tax = round( $tax, $decimals );
                $total_tax += $tax * $order_detail->get_qty_ordered();
                $price = round( $price * $order_detail->get_qty_ordered(), $decimals );
                $price = apply_filters( 'tcp_shopping_cart_row_price', $price, $order_detail );
                //if ($price < 1000) $total_tax = 0;
                $total += $price;
}
*/
//	echo "TOTAL = $total";

	$blah =	OrderPage::show( $order_id, array( 'see_sku' => true ), false, true );
	$blah = str_replace('.00&nbsp;gr.</td>', '&nbsp;gr.</td>', $blah);
if ($order_total < 1000)
{
	$blah = str_replace('Subtotal', 'Subtotal con retencion', $blah);
	$blah = str_replace('Impuestos', '+ Retencion que no aplica', $blah);
	$blah = str_replace('tcp_cart_total_title" style="text-align: right; padding:4px 4px 24px 4px;">Total', 'tcp_cart_total_title" style="text-align: right; padding:4px 4px 24px 4px;">Monto a pagar', $blah);
}
else
{
	$blah = str_replace('Subtotal', 'Monto a pagar', $blah);
	$blah = str_replace('Impuestos', 'Retencion', $blah);
	$blah = str_replace('tcp_cart_total_title" style="text-align: right; padding:4px 4px 24px 4px;">Total', 'tcp_cart_total_title" style="text-align: right; padding:4px 4px 24px 4px;">Total antes de retencion', $blah);
}
	echo $blah;
}?>

<p>
	<a href="javascript:print();"><?php _e( 'print', 'tcp' );?></a>
	&nbsp;|&nbsp;
	<a href="javascript:close();"><?php _e( 'close', 'tcp' );?></a>
</p>

</body>
</html>
