<?php
/**
 * This file is part of TheCartPress-ProductOptions.
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

class OptionCustomFieldsMetabox {

	function register_metabox() {
		add_meta_box( 'tcp-option-custom-fields', __( 'Option Custom Fields', 'tcp_po' ), array( &$this, 'showCustomFields' ), 'tcp_product_option', 'normal', 'high' );
	}

	function showCustomFields() {
		global $post;
		if ( $post->post_type != 'tcp_product_option' ) return;
		$post_id = tcp_get_default_id( $post->ID, 'tcp_product_option' );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang = isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : '';
		$is_translation = $lang != $source_lang;
		if ( $is_translation && $post_id == $post->ID) {
			_e( 'After saving the title and content, you will be able to edit the specific fields of the option.', 'tcp_po' );
			return;
		}
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		$tcp_product_option_parent_id = isset( $_REQUEST['tcp_product_option_parent_id'] ) ? $_REQUEST['tcp_product_option_parent_id'] : 0;
		if ( $tcp_product_parent_id == 0 ) {
			$tcp_product_parent_id = RelEntities::getParent( $post_id, 'OPTIONS' );
			$post_parent = get_post( $tcp_product_parent_id );
			if ( $post_parent->post_type == 'tcp_product_option') {
				$tcp_product_option_parent_id = $tcp_product_parent_id;
				$tcp_product_parent_id = RelEntities::getParent( $tcp_product_parent_id, 'OPTIONS' );
				$relEntity = RelEntities::get( $tcp_product_option_parent_id, $post_parent->ID, 'OPTIONS' );
			} else {
				$tcp_product_option_parent_id = 0;
			}
		}
		$product_parent = get_post( $tcp_product_parent_id );
		if ( $tcp_product_option_parent_id > 0 ) $option_parent = get_post( $tcp_product_option_parent_id ); ?>
		<ul class="subsubsub">
			<li><a href="post.php?action=edit&post=<?php echo $tcp_product_parent_id; ?>"><?php printf( __( 'return to %s', 'tcp_po' ), $product_parent->post_title ); ?></a></li>
		<?php if ( $tcp_product_option_parent_id > 0 ) : ?>
			<li>|</li>
			<li><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $tcp_product_option_parent_id; ?>"><?php printf( __( 'return to %s', 'tcp_po' ), $option_parent->post_title ); ?></a></li>
		<?php endif; ?>
			<li>|</li>
			<li><a href="<?php echo TCP_PO_ADMIN_PATH; ?>OptionsList.php&post_id=<?php echo $tcp_product_parent_id; ?>"><?php echo __( 'return to Options list', 'tcp_po' ); ?></a></li>
			<li>|</li>
			<li><a href="post-new.php?post_type=tcp_product_option&tcp_product_parent_id=<?php echo $tcp_product_parent_id; ?>&tcp_product_option_parent_id=<?php echo $tcp_product_option_parent_id; ?>" title="<?php echo __( 'create a new \'sister\' option', 'tcp_po' ); ?>"><?php echo __( 'create new option', 'tcp_po' ); ?></a></li>
		<?php if ( $tcp_product_option_parent_id == 0 ) : ?>
			<li>|</li>
			<li><a href="post-new.php?post_type=tcp_product_option&tcp_product_parent_id=<?php echo $tcp_product_parent_id; ?>&tcp_product_option_parent_id=<?php echo $post->ID; ?>" title="<?php echo __( 'create a new second level option', 'tcp_po' ); ?>"><?php echo __( 'create child option', 'tcp_po' ); ?></a></li>
		<?php endif; ?>
			<?php do_action( 'tcp_option_metabox_toolbar', $post_id ); ?>
		</ul>
		<?php //if ( $create_grouped_relation ) : ?>
			<input type="hidden" name="tcp_product_parent_id" id="tcp_product_parent_id" value="<?php echo $tcp_product_parent_id; ?>" />
			<input type="hidden" name="tcp_product_option_parent_id" id="tcp_product_option_parent_id" value="<?php echo $tcp_product_option_parent_id; ?>" />
		<?php //endif; ?>
		<div class="form-wrap">
			<?php wp_nonce_field( 'tcp-option-custom-fields', 'tcp-option-custom-fields_wpnonce', false, true ); ?>
			<table class="form-table"><tbody>
			<tr valign="top">
				<th scope="row"><label for="tcp_price"><?php _e( 'Price', 'tcp_po' ); ?>:</label></th>
				<td><input type="text" min="0" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_price" id="tcp_price" value="<?php echo tcp_number_format( tcp_get_the_price( $post_id ) ); ?>" class="regular-text" style="width:12em">&nbsp;<?php tcp_the_currency(); ?>
				<p class="description"><?php _e( 'This price will be added to the price of the parent.', 'tcp_po' ); ?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_weight"><?php _e( 'Weight', 'tcp_po' ); ?>:</label></th>
				<td><input type="text" min="0" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_weight" id="tcp_weight" value="<?php echo tcp_number_format( tcp_get_the_weight( $post_id ) ); ?>" class="regular-text" style="width:12em" />&nbsp;<?php tcp_the_unit_weight(); ?>
				<p class="description"><?php _e( 'If value is zero then the weight will be the weight of the parent. This weight will not be added to the weight of the parent anyway.', 'tcp_po' ); ?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_order"><?php _e( 'Order', 'tcp_po' ); ?>:</label></th>
				<td><input name="tcp_order" id="tcp_order" value="<?php echo htmlspecialchars( tcp_get_the_order( $post_id ) ); ?>" class="regular-text" type="text" min="0" style="width:4em" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_sku"><?php _e( 'Sku', 'tcp_po' ); ?>:</label></th>
				<td><input name="tcp_sku" id="tcp_sku" value="<?php echo htmlspecialchars( tcp_get_the_sku( $post_id ) ); ?>" class="regular-text" type="text" style="width:12em" /></td>
			</tr>
			<?php do_action( 'tcp_options_metabox_custom_fields', $post_id ); ?>
			</tbody></table>
		</div> <!-- form-wrap -->
		<?php
	}

	function saveCustomFields( $post_id, $post ) {
		if ( $post->post_type != 'tcp_product_option' ) return array( $post_id, $post );
		if ( ! isset( $_POST[ 'tcp-option-custom-fields_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ 'tcp-option-custom-fields_wpnonce' ], 'tcp-option-custom-fields' ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		$post_id = tcp_get_default_id( $post_id, 'tcp_product_option' );
		$tcp_parent_id = isset( $_REQUEST['tcp_product_option_parent_id'] ) ? $_REQUEST['tcp_product_option_parent_id'] : 0;
		if ( $tcp_parent_id == 0 ) $tcp_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		$price	= isset( $_POST['tcp_price'] )  ? tcp_input_number( $_POST['tcp_price'] ) : 0;
		$order	= isset( $_POST['tcp_order'] )  ? $_POST['tcp_order'] : '';
		$weight	= isset( $_POST['tcp_weight'] )  ? tcp_input_number( $_POST['tcp_weight'] ) : 0;
		$sku	= isset( $_POST['tcp_sku'] ) ? $_POST['tcp_sku'] : '';
		if ( ! Relentities::exists( $tcp_parent_id, $post_id, 'OPTIONS' ) ) {
			RelEntities::insert( $tcp_parent_id, $post_id, 'OPTIONS', $order );
		} else {
			RelEntities::update( $tcp_parent_id, $post_id, 'OPTIONS', $order );
		}
		update_post_meta( $post_id, 'tcp_price', $price );
		update_post_meta( $post_id, 'tcp_order', $order );
		update_post_meta( $post_id, 'tcp_weight', $weight );
		update_post_meta( $post_id, 'tcp_sku', $sku );
		do_action( 'tcp_options_metabox_save_custom_fields', $post_id );
	}

	function deleteCustomFields( $post_id ) {
		$post_id = tcp_get_default_id( $post_id, 'tcp_product_option' );
		//if ( ! isset( $_POST[ 'tcp-option-custom-fields_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ 'tcp-option-custom-fields_wpnonce' ], 'tcp-option-custom-fields' ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		$post = get_post( $post_id );
		if ( $post->post_type != 'tcp_product_option' ) return $post_id;
		RelEntities::deleteAllTo( $post_id, 'OPTIONS' );
		delete_post_meta( $post_id, 'tcp_price' );
		delete_post_meta( $post_id, 'tcp_order' );
		delete_post_meta( $post_id, 'tcp_weight' );
		delete_post_meta( $post_id, 'tcp_sku' );
		$translations = tcp_get_all_translations( $post_id );
		if ( is_array( $translations ) && count( $translations ) > 0 ) {
			foreach( $translations as $translation ) {
				if ( $translation->element_id != $post_id ) {
					wp_delete_post( $post_id );
				}
			}
		}
		$options = RelEntities::select( $post_id, 'OPTIONS' );
		if ( is_array( $options ) ) {
			foreach( $options as $option ) {
				wp_delete_post( $option->id_to, true );
			}
		}
		RelEntities::deleteAll( $post_id, 'OPTIONS' );
		do_action( 'tcp_options_metabox_delete_custom_fields', $post_id );
	}
	
	function __construct() {
		add_action( 'admin_init', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'saveCustomFields' ), 1, 2 );
		add_action( 'delete_post', array( $this, 'deleteCustomFields' ) );
	}
}

new OptionCustomFieldsMetabox();
?>
