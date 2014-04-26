<?php
/*
Plugin Name: TheCartPress Custom Field
Plugin URI: http://thecartpress.com
Description: Custom Text for TheCartPress
Version: 1.0
Author: TheCartPress team
Author URI: http://thecartpress.com
License: GPL
Parent: thecartpress
*/

/**
 * This file is part of TheCartPress-CustomField.
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

define( 'TCP_cf_DEFAULT_SIZE', 12 );

class TCPCustomField {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
	}

	function init() {
		add_filter( 'tcp_add_to_shopping_cart', array( &$this, 'tcp_add_to_shopping_cart' ) );
		add_filter( 'tcp_shopping_cart_key', array( &$this, 'tcp_shopping_cart_key' ) );
		add_filter( 'tcp_get_shopping_cart_detail_title', array( &$this, 'tcp_cart_table_title_item' ), 10, 2 );
		add_filter( 'tcp_cart_box_title_item', array( &$this, 'tcp_cart_table_title_item' ), 10, 2 );
		add_action( 'tcp_get_shopping_cart_detail_hidden_fields', array( &$this, 'tcp_get_shopping_cart_detail_hidden_fields' ) );
		add_action( 'tcp_get_shopping_cart_hidden_fields', array( &$this, 'tcp_get_shopping_cart_detail_hidden_fields' ) );
		add_filter( 'tcp_cart_table_title_item', array( &$this, 'tcp_cart_table_title_item' ), 10, 2 );
		add_action( 'tcp_buy_button_bottom', array( &$this, 'tcp_buy_button_bottom' ) );
		add_action( 'wp_footer', array( &$this, 'wp_footer' ) );
		global $thecartpress;
		if ( $thecartpress && $thecartpress->get_setting( 'load_default_buy_button_style', true ) )
			wp_enqueue_style( 'tcp_cf_style', plugins_url( 'thecartpress-customfield/css/style.css' ) );
	}

	function admin_init() {
		add_action( 'tcp_product_metabox_custom_fields_after_price', array( &$this, 'tcp_product_metabox_custom_fields_after_price' ) );
		add_action( 'tcp_product_metabox_save_custom_fields', array( &$this, 'tcp_product_metabox_save_custom_fields' ) );
		add_action( 'tcp_product_metabox_delete_custom_fields', array( &$this, 'tcp_product_metabox_delete_custom_fields' ) );
	}

	function tcp_product_metabox_custom_fields_after_price( $post_id ) { 
		$tcp_cf_customisable = (bool)tcp_get_the_meta( 'tcp_cf_customisable', $post_id );
		$tcp_cf_label = tcp_get_the_meta( 'tcp_cf_label', $post_id );
		$tcp_cf_customisable_size = (integer)tcp_get_the_meta( 'tcp_cf_customisable_size', $post_id );
		$tcp_cf_customisable_size = $tcp_cf_customisable_size > 0 ? $tcp_cf_customisable_size : TCP_cf_DEFAULT_SIZE;
		$tcp_cf_lines = (bool)tcp_get_the_meta( 'tcp_cf_lines', $post_id );
		$tcp_cf_customise_cost = tcp_number_format( tcp_get_the_meta( 'tcp_cf_customise_cost', $post_id ) );
		$tcp_cf_dropdown = (bool)tcp_get_the_meta( 'tcp_cf_dropdown', $post_id ); ?>
		<tr valign="top">
			<th scope="row">
				<label for="tcp_cf_customisable"><?php _e( 'Personalised', 'tcp-customfields' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="tcp_cf_customisable" id="tcp_cf_customisable" value="yes" <?php checked( $tcp_cf_customisable ); ?> />
				<script type="text/javascript">
				jQuery( '#tcp_cf_customisable' ).click( function() {
					if ( jQuery(this).is( ':checked' ) ) {
						jQuery( '#tcp_cf_label' ).show( 200 );
						jQuery( '#tcp_cf_customisable_size_row' ).show( 200 );
						jQuery( '#tcp_cf_customise_cost_row' ).show( 200 );
						jQuery( '#tcp_cf_lines_row' ).show( 200 );
						jQuery( '#tcp_cf_dropdown_row' ).show( 200 );
					} else {
						jQuery( '#tcp_cf_label' ).hide( 200 );
						jQuery( '#tcp_cf_customisable_size_row' ).hide( 200 );
						jQuery( '#tcp_cf_customise_cost_row' ).hide( 200 );
						jQuery( '#tcp_cf_lines_row' ).hide( 200 );
						jQuery( '#tcp_cf_dropdown_row' ).hide( 200 );
					}
				} );
				</script>
			</td>
		</tr>
		<tr valign="top" id="tcp_cf_label" <?php if ( ! $tcp_cf_customisable ) : ?> style="display: none"<?php endif; ?>>
			<th scope="row" style="padding-left: 2em">
				<label for="tcp_cf_lines"><?php _e( 'Label', 'tcp-customfields' ); ?>:</label>
			</th>
			<td>
				<input type="text" name="tcp_cf_label" id="tcp_cf_label" value="<?php echo $tcp_cf_label; ?>" maxlength="250"/>
			</td>
		</tr>
		<tr valign="top" id="tcp_cf_customisable_size_row" <?php if ( ! $tcp_cf_customisable ) : ?> style="display: none"<?php endif; ?>>
			<th scope="row" style="padding-left: 2em">
				<label for="tcp_cf_customisable_size"><?php _e( 'Personalise text size', 'tcp-customfields' ); ?>:</label>
			</th>
			<td>
				<input type="numeric" step="1" max="99" min="1" name="tcp_cf_customisable_size" id="tcp_cf_customisable_size" value="<?php echo $tcp_cf_customisable_size; ?>" size="3" maxlength="3" />
			</td>
		</tr>
		<tr valign="top" id="tcp_cf_lines_row" <?php if ( ! $tcp_cf_customisable ) : ?> style="display: none"<?php endif; ?>>
			<th scope="row" style="padding-left: 2em">
				<label for="tcp_cf_lines"><?php _e( 'Display multiline', 'tcp-customfields' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="tcp_cf_lines" id="tcp_cf_lines" value="yes" <?php checked( $tcp_cf_lines ); ?> />
			</td>
		</tr>
		<tr valign="top" id="tcp_cf_customise_cost_row" <?php if ( ! $tcp_cf_customisable ) : ?> style="display: none"<?php endif; ?>>
			<th scope="row" style="padding-left: 2em">
				<label for="tcp_cf_customise_cost"><?php _e( 'Personalised Cost', 'tcp-customfields' ); ?>:</label>
			</th>
			<td>
				<input type="text" min="0" step="any" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_cf_customise_cost" id="tcp_cf_customise_cost" value="<?php echo $tcp_cf_customise_cost; ?>" class="regular-text" style="width:12em !important" />&nbsp;<?php tcp_the_currency(); ?> <?php tcp_price_include_tax_message(); ?>
				<p class="description"><?php printf( __( 'Current number format is %s', 'tcp-customfields'), tcp_get_number_format_example( 9999.99, false ) ); ?></p>
			</td>
		</tr>
		<tr valign="top" id="tcp_cf_dropdown_row" <?php if ( ! $tcp_cf_customisable ) : ?> style="display: none"<?php endif; ?>>
			<th scope="row" style="padding-left: 2em">
				<label for="tcp_cf_dropdown"><?php _e( 'Dropdown', 'tcp-customfields' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="tcp_cf_dropdown" id="tcp_cf_dropdown" value="yes" <?php checked( $tcp_cf_dropdown ); ?> />
			</td>
		</tr><?php
	}

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		update_post_meta( $post_id, 'tcp_cf_label', $_REQUEST['tcp_cf_label'] );
		update_post_meta( $post_id, 'tcp_cf_customisable', isset( $_REQUEST['tcp_cf_customisable'] ) ? $_REQUEST['tcp_cf_customisable'] == 'yes' : false );
		update_post_meta( $post_id, 'tcp_cf_customisable_size', isset( $_REQUEST['tcp_cf_customisable_size'] ) && is_numeric( $_REQUEST['tcp_cf_customisable_size'] ) ? $_REQUEST['tcp_cf_customisable_size'] : TCP_cf_DEFAULT_SIZE );
		update_post_meta( $post_id, 'tcp_cf_lines', isset( $_REQUEST['tcp_cf_lines'] ) );
		update_post_meta( $post_id, 'tcp_cf_customise_cost', isset( $_REQUEST['tcp_cf_customise_cost'] ) ? tcp_input_number( $_REQUEST['tcp_cf_customise_cost'] ) : 0 );
		update_post_meta( $post_id, 'tcp_cf_dropdown', isset( $_REQUEST['tcp_cf_dropdown'] ) );
	}

	function tcp_product_metabox_delete_custom_fields ( $post_id ) {
		delete_post_meta( $post_id, 'tcp_cf_label' );
		delete_post_meta( $post_id, 'tcp_cf_customisable' );
		delete_post_meta( $post_id, 'tcp_cf_customisable_size' );
		delete_post_meta( $post_id, 'tcp_cf_lines' );
		delete_post_meta( $post_id, 'tcp_cf_customise_cost' );
		delete_post_meta( $post_id, 'tcp_cf_dropdown' );
	}
	
	function tcp_get_shopping_cart_detail_hidden_fields( $item ) {
//		if ( isset( $attributes['tcp_cf_text'] ) && $item->has_attributes() ) : $attributes = $item->get_attributes();
	if ( $item->has_attributes() ) {
			$attributes = $item->get_attributes();
			if ( isset( $attributes['tcp_cf_text'] ) ) : ?>	
			<input type="hidden" name="tcp_cf_text" value="<?php echo $attributes['tcp_cf_text']; ?>" />
		<?php endif;
		}
	}

	function tcp_shopping_cart_key( $shopping_cart_id ) {
		$tcp_cf_text = isset( $_REQUEST['tcp_cf_text'] ) ? $_REQUEST['tcp_cf_text'] : false;
		if ( $tcp_cf_text === false ) return $shopping_cart_id;
		if ( strlen( $tcp_cf_text ) == 0 ) return $shopping_cart_id;
		return $shopping_cart_id . '-' . $tcp_cf_text;
	}

	function tcp_add_to_shopping_cart( $item ) {
		$tcp_cf_text = isset( $_REQUEST['tcp_cf_text'] ) ? trim( $_REQUEST['tcp_cf_text'] ) : false;
		if ( strlen( $tcp_cf_text ) == 0 ) $tcp_cf_text = false;
		if ( $tcp_cf_text !== false ) {
			$attributes = $item->get_attributes();
			$attributes['tcp_cf_text'] = $_REQUEST['tcp_cf_text'];
			$item->set_attributes( $attributes );
			$tcp_cf_customise_cost = tcp_get_the_meta( 'tcp_cf_customise_cost', $item->getPostId() );
			if ( ! $tcp_cf_customise_cost ) $tcp_cf_customise_cost = 0;
			$item->setUnitPrice( $item->getUnitPrice() + $tcp_cf_customise_cost );
		}
		return $item; 
	}

	function tcp_cart_table_title_item( $title, $item ) {
		ob_start();
		if ( $item->has_attributes() ) : $attributes = $item->get_attributes();
			if ( isset( $attributes['tcp_cf_text'] ) && strlen( $attributes['tcp_cf_text'] ) > 0 ) : $tcp_cf_label = tcp_get_the_meta( 'tcp_cf_label', $post_id ); ?>
			<?php if ( strlen( $tcp_cf_label ) ) : ?>
			<dl>
				<dt><?php echo $tcp_cf_label; ?>:</dt>
				<dd><?php echo( $attributes['tcp_cf_text'] ); ?></dd>
			</dl>
			<?php else : ?>
			<div class="tcp_customfield_text">
				<?php echo( $attributes['tcp_cf_text'] ); ?>
			</div>
			<?php endif; ?>
		<?php endif;
		endif;
		return $title . ob_get_clean();
	}

	function tcp_buy_button_bottom( $post_id ) { 
		$is_customisable = get_post_meta( $post_id, 'tcp_cf_customisable', true );
		if ( $is_customisable ) :
			$tcp_cf_label = get_post_meta( $post_id, 'tcp_cf_label', true );
			$customisable_size = get_post_meta( $post_id, 'tcp_cf_customisable_size', true );
			$customisable_size = is_numeric( $customisable_size ) && $customisable_size > 0 ? $customisable_size : TCP_cf_DEFAULT_SIZE;
			$lines = (bool)get_post_meta( $post_id, 'tcp_cf_lines', true );
			$dropdown = (bool)get_post_meta( $post_id, 'tcp_cf_dropdown', true ); ?>
<div class="tcp_cf_customise">
<?php if ( $dropdown ) : ?>
	<a href="#" class="tcp_cf_open_customise" target="<?php echo $post_id; ?>"><?php _e( 'Personalize', 'tcp-customfields' ); ?></a>
	<div class="tcp_cf_customise_area tcp_cf_customise_area_<?php echo $post_id; ?>" <?php if ( $dropdown ) : ?>style="display:none;"<?php endif; ?>>
		<label for="tcp_cf_text"><?php echo $tcp_cf_label; ?>:</label>
		<?php if ( $lines ) : ?>
		<textarea name="tcp_cf_text" cols="20" rows="4" maxlength="<?php echo $customisable_size; ?>" ></textarea>
		<?php else : ?>
		<input type="text" name="tcp_cf_text" size="20" maxlength="<?php echo $customisable_size; ?>" />
		<?php endif; ?>
		<div class="tcp_cf_cancel_button">
			<a href="#" class="tcp_cf_close_customise" target="<?php echo $post_id; ?>"><?php _e( 'Cancel', 'tcp-customfields' ); ?></a>
		</div>
	</div><!-- .tcp_cf_customise_area -->
<?php else : ?>
	<label for="tcp_cf_text"><?php echo $tcp_cf_label; ?>:</label>
	<?php if ( $lines ) : ?>
	<textarea name="tcp_cf_text" cols="20" rows="4" maxlength="<?php echo $customisable_size; ?>" ></textarea>
	<?php else : ?>
	<input type="text" name="tcp_cf_text" size="20" maxlength="<?php echo $customisable_size; ?>" />
	<?php endif; ?>
<?php endif; ?>
</div><!-- .tcp_cf_customise -->
		<?php endif;
	}

	function wp_footer() { ?>
<script type="text/javascript">
jQuery( '.tcp_cf_open_customise' ).click( function() {
	var post_id = jQuery( this ).attr( 'target' );
	jQuery( '.tcp_cf_customise_area_' + post_id ).toggle( 'slow' );
	return false;
} );

jQuery( '.tcp_cf_close_customise' ).click( function() {
	var post_id = jQuery( this ).attr( 'target' );
	jQuery( '.tcp_cf_customise_area_' + post_id ).hide( 'slow' );
	return false;
} );
</script><?php
	}
}

new TCPCustomField();
?>
