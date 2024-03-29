<?php
/*
Plugin Name: Doap Product Options
Plugin URI: http://thecartpress.com
Description: Product options management for TheCartPress
Version: 1.0.9
Author: DevOps and Platforms
Author URI: http://thecartpress.com
License: GPL
Parent: thecartpress
*/

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

define( 'TCP_PO_FOLDER'		, dirname( __FILE__ ) . '/' );
define( 'TCP_PO_ADMIN_PATH'	, 'admin.php?page=' . plugin_basename( TCP_PO_FOLDER ) . '/admin/' );

require_once( 'daos/RelEntitiesOptions.class.php' );
require_once( 'customposttypes/OptionCustomPostType.class.php' );

if ( ! class_exists( 'ProductOptionsForTheCartPress' ) ) {
class ProductOptionsForTheCartPress {

function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ), 99 );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'tcp_product_metabox_toolbar', array( &$this, 'tcp_product_metabox_toolbar' ) );
			add_action( 'tcp_relations_metabox_options_toolbar', array( &$this, 'tcp_relations_metabox_options_toolbar' ) );
			add_action( 'tcp_assigned_products_product_toolbar', array( &$this, 'tcp_assigned_products_product_toolbar' ), 10, 2 );
			require_once( 'metaboxes/OptionCustomFieldsMetabox.class.php' );

			add_action( 'tcp_update_price_search_controls', array( &$this, 'tcp_update_price_search_controls' ) );
			add_action( 'tcp_update_price', array( &$this, 'tcp_update_price' ) );
			add_action( 'tcp_update_price_controls', array( &$this, 'tcp_update_price_controls' ) );

			add_action( 'tcp_update_stock_search_controls', array( &$this, 'tcp_update_stock_search_controls' ) );
			add_action( 'tcp_update_stock', array( &$this, 'tcp_update_stock') );
			add_action( 'tcp_update_stock_controls', array( &$this, 'tcp_update_stock_controls' ) );

			add_action( 'tcp_copying_product', array( &$this, 'tcp_copying_product' ), 1, 2 );

			add_action( 'tcp_options_metabox_custom_fields', array( &$this, 'tcp_options_metabox_custom_fields' ) );
			add_action( 'tcp_options_metabox_save_custom_fields', array( &$this, 'tcp_options_metabox_save_custom_fields' ) );
			add_action( 'tcp_options_metabox_delete_custom_fields', array( &$this, 'tcp_options_metabox_delete_custom_fields' ) );
			
			add_action( 'tcp_theme_compatibility_settings_page', array( &$this, 'tcp_theme_compatibility_settings_page' ) );
			add_filter( 'tcp_theme_compatibility_settings_action', array( &$this, 'tcp_theme_compatibility_settings_action' ), 10, 2 );
			add_filter( 'tcp_theme_compatibility_settings_unset_action', array( &$this, 'tcp_theme_compatibility_settings_unset_action' ), 10, 2 );
		} else {
			add_filter( 'tcp_buy_button_options', array( &$this, 'tcp_buy_button_options' ), 10, 3 );
			add_filter( 'tcp_get_image_in_content', array( &$this, 'tcp_get_image_in_content' ), 10, 3 );
			add_filter( 'tcp_get_image_in_grouped_buy_button', array( &$this, 'tcp_get_image_in_grouped' ), 10, 3 );
			add_filter( 'tcp_get_the_price_label', array( &$this, 'tcp_get_the_price_label' ), 10, 2 );
		}
	}

	function tcp_get_product_types( $types ) {
		$types['SIMPLE']['tcp_product_options_supported'] = true;
		return $types;
	}

	function admin_init() {
		add_filter( 'tcp_product_row_actions', array( $this, 'productRowActions' ) );
	}
	
	function tcp_theme_compatibility_settings_page( $suffix) {
		global $thecartpress;
		$options_type = $thecartpress->get_setting( 'options_type'. $suffix, 'single' );
		$tcp_settings_page = TCP_ADMIN_FOLDER . 'Settings.class.php'; ?>

<h3><?php _e( 'Product Options settings', 'tcp'); ?></h3>

<div class="postbox">

<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
	<label for="continue_url"><?php _e( 'Options type', 'tcp_po' ); ?></label>
	</th>
	<td>
		<input type="radio" id="options_type_single" name="options_type" value="single" <?php checked( 'single', $options_type ); ?> />
		<label for="options_type_single"><?php _e( 'Single', 'tcp_po' ); ?></label>
		<br />
		<input type="radio" id="options_type_double" name="options_type" value="double" <?php checked( 'double', $options_type ); ?> />
		<label for="options_type_double"><?php _e( 'Double', 'tcp_po' ); ?></label>
		<br />
		<input type="radio" id="options_type_list" name="options_type" value="list" <?php checked( 'list', $options_type ); ?> />
		<label for="options_type_list"><?php _e( 'List', 'tcp_po' ); ?></label>
	</td>
</tr>
</tbody>
</table>

</div><?php
	}

	function tcp_theme_compatibility_settings_action( $settings, $suffix ) {
		$settings['options_type' . $suffix] = isset( $_POST['options_type'] ) ? $_POST['options_type'] : 'single';
		return $settings;
	}

	function tcp_theme_compatibility_unset_settings_action( $settings, $suffix ) {
		unset( $settings['options_type' . $suffix] );
		return $settings;
	}

	function admin_menu() {
		add_submenu_page( 'tcp', __( 'list of Options', 'tcp_po' ), __( 'list of Options', 'tcp_po' ), 'tcp_edit_product', dirname( __FILE__ ) . '/admin/OptionsList.php' );
	}

	function productRowActions( $actions ) {
		if ( ! current_user_can( 'tcp_edit_products' ) ) return $actions;
		global $post;
		$type = tcp_get_the_product_type( $post->ID );
		$types = tcp_get_product_types();
		if ( ! isset( $types[$type] ) ) return;
		$type = $types[$type];
		if ( isset( $type['tcp_product_options_supported'] ) && $type['tcp_product_options_supported'] ) {
			$count = RelEntities::count( $post->ID, 'OPTIONS' );
			$count = ( $count > 0 ) ? ' (' . $count . ')' : '';
			$actions['tcp_options'] = '<a href="' . TCP_PO_ADMIN_PATH . 'OptionsList.php&post_id=' . $post->ID . '" title="' . esc_attr( __( 'options', 'tcp_po' ) ) . '">' . __( 'options', 'tcp_po' ) . $count . '</a>';
		}
		return $actions;
	}

	function tcp_product_metabox_toolbar( $post_id ) {	
		$type = tcp_get_the_product_type( $post_id );
		$types = tcp_get_product_types();
		if ( ! isset( $types[$type] ) ) return;
		$type = $types[$type];
		if ( isset( $type['tcp_product_options_supported'] ) && $type['tcp_product_options_supported'] ) {
			echo '<li>|</li>';
			$count = RelEntities::count( $post_id, 'OPTIONS' );
			$count = $count > 0 ? ' (' . $count . ')' : '';
			echo '<li><a href="', TCP_PO_ADMIN_PATH, 'OptionsList.php&post_id=', $post_id, '">', __( 'options', 'tcp_po' ), $count, '</a></li>';
		}
	}

	function tcp_relations_metabox_options_toolbar( $post_id ) {
		$type = tcp_get_the_product_type( $post_id );
		$types = tcp_get_product_types();
		$type = $types[$type];
		if ( isset( $type['tcp_product_options_supported'] ) && $type['tcp_product_options_supported'] ) {
			$count = RelEntities::count( $post_id, 'OPTIONS' );
			$count = $count > 0 ? ' (' . $count . ')' : '';
			echo '<li><a href="', TCP_PO_ADMIN_PATH, 'OptionsList.php&post_id=', $post_id, '">', __( 'Manage options', 'tcp_po' ), $count, '</a></li>';
		}
	}
	function tcp_assigned_products_product_toolbar( $parent_id, $post_id ) {
		$type = tcp_get_the_product_type( $post_id );
		$types = tcp_get_product_types();
		$type = $types[$type];
		if ( isset( $type['tcp_product_options_supported'] ) && $type['tcp_product_options_supported'] ) {
			$count = RelEntities::count( $post_id, 'OPTIONS' );
			$count = $count > 0 ? ' (' . $count . ')' : '';
			echo '&nbsp;|&nbsp;<a href="', TCP_PO_ADMIN_PATH, 'OptionsList.php&post_id=', $post_id, '">', __( 'options', 'tcp_po' ), $count, '</a>';
		}
	}

	static function tcp_has_option( $post_id ) {
		return RelEntities::count( tcp_get_default_id( $post_id, get_post_type( $post_id ) ), 'OPTIONS' ) > 0;
	}

	function tcp_buy_button_options( $html, $post_id, $parent_id = 0 ) {
		global $thecartpress;
		$options_type = isset( $thecartpress->settings['options_type'] ) ? $thecartpress->settings['options_type'] : 'single';
		//if ( RelEntities::count( tcp_get_default_id( $post_id, get_post_type( $post_id ) ), 'OPTIONS' ) == 0 ) {
		if ( ! ProductOptionsForTheCartPress::tcp_has_option( $post_id ) ) {
			return $html;
		} elseif ( $options_type == 'single' ) {//one combo
			$product_price = tcp_get_the_price_to_show( $post_id );
			$script = ' var id = jQuery(this).val(); id = id.split(\'-\'); if (id.length > 1) id = id[1]; if (jQuery(\'.tcp_thumbnail_option_\' + id).length) {jQuery(\'.tcp_thumbnail_' . $post_id .'\').hide();jQuery(\'.tcp_thumbnail_option_\' + id).show();}';
			$out = '<select name="tcp_option_id[]" id="tcp_option_id_' . $post_id . '" onchange="' . $script . '">' . "\n";
			//$price = tcp_get_the_price( $post_id );
			$options = RelEntitiesOptions::getOptionsTree( tcp_get_default_id( $post_id, get_post_type( $post_id ) ) );
			foreach( $options as $option_id => $option ) {
				$current_id = tcp_get_current_id( $option_id, OptionCustomPostType::$PRODUCT_OPTION );
				$option_price = tcp_get_the_price( $option_id );
				$price = $product_price + tcp_get_the_price_to_show( $post_id, $option_price );
				remove_filter( 'the_title', 'wptexturize' );
				if ( is_array( $option ) ) {
					foreach( $option as $sub_id => $sub_option ) {
						$current_sub_id = tcp_get_current_id( $sub_id, OptionCustomPostType::$PRODUCT_OPTION );
						$sub_option_price = tcp_get_the_price( $sub_id );
						$sub_option_price = $price + tcp_get_the_price_to_show( $post_id, $sub_option_price );
						$option_title = get_the_title( $current_id ) . '&nbsp;-&nbsp;' . htmlspecialchars( get_the_title( $current_sub_id ) ) . ':&nbsp;<span class="tcp_price">' . tcp_format_the_price( $sub_option_price ) . '</span>';
						$option_title = apply_filters( 'tcp_options_title', $option_title, $post_id, $option_id, $sub_id );
						$out .= '<option value="' . $option_id . '-' . $sub_id . '">' . html_entity_decode( $option_title, ENT_NOQUOTES, "UTF-8") . '</option>' . "\n";
					}
				} else {
					$option_title = get_the_title( $current_id );
					if ( $price > 0 ) $option_title .= '&nbsp;' . tcp_format_the_price( $price );
					$option_title = apply_filters( 'tcp_options_title', $option_title, $post_id, $option_id );
					$out .= '<option value="' . $option_id . '">' .  html_entity_decode( $option_title, ENT_NOQUOTES, "UTF-8");
					$out .= '</option>' . "\n";
				}
				add_filter( 'the_title', 'wptexturize' );
			}
			$out .= '</select>' . "\n";
			$out .= '<input type="hidden" name="tcp_option_1_id[]" id="tcp_option_1_id"/>' . "\n";
			$out .= '<input type="hidden" name="tcp_option_2_id[]" id="tcp_option_2_id"/>' . "\n";
		} elseif ( $options_type == 'double' ) { //two combos
			$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
			$options = RelEntitiesOptions::getOptionsTree( $post_id );
			$product_price = tcp_get_the_price( $post_id );
			if ( is_array( $options ) ) {
				$out = '<script type="text/javascript">' . "\n";
				$out .= 'jQuery(document).ready(function() {' . "\n";
				$out .= '	jQuery("#tcp_option_1_id_' . $post_id . '_' . $parent_id . ' option:first").attr("selected", "selected");' . "\n";
				$out .= '	jQuery("#tcp_option_1_id_' . $post_id . '_' . $parent_id . '").trigger(\'change\');' . "\n";
				$out .= '});' . "\n";
				foreach( $options as $option_id => $option ) {
					//$current_id = tcp_get_current_id( $option_id, OptionCustomPostType::$PRODUCT_OPTION );
					$price = $product_price + tcp_get_the_price( $option_id );
					if ( is_array( $option ) ) {
						$out .= 'function add_options_' . $post_id . '_' . $parent_id . '_' . $option_id . '(sel) {' . "\n";
						$out .= "\t" . 'jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . '").show();' . "\n";
						remove_filter( 'the_title', 'wptexturize' );
						foreach( $option as $sub_id => $sub_option )
							if ( get_post_status( $sub_id ) == 'publish' ) {
								$current_sub_id = tcp_get_current_id( $sub_id, OptionCustomPostType::$PRODUCT_OPTION );
								$sub_price = $price + tcp_get_the_price( $sub_id );
								$sub_price = tcp_get_the_price_to_show( $post_id, $sub_price );
								$option_title = get_the_title( $current_sub_id ) . ': ' . tcp_format_the_price( $sub_price );
								$option_title = html_entity_decode( apply_filters( 'tcp_options_title', $option_title, $post_id, $option_id, $sub_id ), ENT_NOQUOTES, "UTF-8");
								$out .= "\t" . 'jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . '").append(jQuery("<option></option>").attr("value", ' . $sub_id . ').text("' . $option_title . '"));' . "\n";
							}
						add_filter( 'the_title', 'wptexturize' );
						//$out .= '	jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . '").attr("id", "tcp_option_2_id_' . $post_id . '_' . $option_id . '");' . "\n";
						$out .= '}' . "\n";
					} else {
						$out .= 'function add_options_' . $post_id . '_' . $parent_id . '_' . $option_id . '(sel) {' . "\n";
						$out .= "\t" . 'jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . '").hide();' . "\n";
						$out .= '}' . "\n";
					}
					$out .= 'function load_option_' . $post_id . '_' . $parent_id . '(id) {
						jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . '").children().remove();
						//var id = jQuery("#tcp_option_1_id_' . $post_id . '_' . $parent_id . '").val();
						var fun_name = "add_options_' . $post_id . '_' . $parent_id . '_" + id + "()";
						eval(fun_name);
						if (jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . ' option").size() > 0) {
							jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . '").show();
							jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . '").trigger(\'change\');
						} else {
							jQuery("#tcp_option_2_id_' . $post_id . '_' . $parent_id . '").hide();
						}
					}' . "\n";
				}
			}
			$out .= '</script>' . "\n";
			$out .= '<input type="hidden" name="tcp_option_id[]"value="">' . "\n";
			$script = 'var id = \'.tcp_thumbnail_option_\' + jQuery(this).val();
			if (jQuery(id).length) {
				jQuery(\'.tcp_thumbnail_' . $post_id .'\').hide();
				jQuery(id).show();
			}';
			$out .= '<select name="tcp_option_1_id[]" id="tcp_option_1_id_' . $post_id . '_' . $parent_id . '" onchange="load_option_' . $post_id . '_' . $parent_id . '(this.value);' . $script . '">' . "\n";
			foreach( $options as $id => $option ) {
				$price = $product_price + tcp_get_the_price( $id );
				$option_title = get_the_title( tcp_get_current_id( $id, OptionCustomPostType::$PRODUCT_OPTION ) );
				if ( ! is_array( $option ) ) {
					$price = tcp_get_the_price_to_show( $post_id, $price );
					if ( $price > 0 ) $option_title .= ': ' . tcp_format_the_price( $price );
				}
				$option_title = apply_filters( 'tcp_options_title', $option_title, $post_id, $id );
				$out .= '<option value="' . $id . '">' . html_entity_decode( $option_title ) . '</option>' . "\n";
			}
			$out .= '</select>' . "\n";
			$script = 'if (jQuery(\'.tcp_thumbnail_option_\' + jQuery(this).val()).length) { jQuery(\'.tcp_thumbnail_' . $post_id .'\').hide(); jQuery(\'.tcp_thumbnail_option_\' + jQuery(this).val()).show();}';
			$out .= '<select name="tcp_option_2_id[]" id="tcp_option_2_id_' . $post_id . '_' . $parent_id . '" style="display:none;" onchange="' . $script . '"></select>' . "\n";
		} elseif ( $options_type == 'list' ) {
			$product_price = tcp_get_the_price_to_show( $post_id );
			$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
			$options = RelEntitiesOptions::getOptionsTree( $post_id );
			$out = '<ul>';
			$first = true;
			$value_first = '';
			foreach( $options as $option_id_1 => $option_1 ) {
				$current_id_1 = tcp_get_current_id( $option_id_1, OptionCustomPostType::$PRODUCT_OPTION );
				$option_price_1 = tcp_get_the_price( $option_id_1 );
				$option_price_1 = $product_price + tcp_get_the_price_to_show( $post_id, $option_price_1 );
				remove_filter( 'the_title', 'wptexturize' );
				if ( is_array( $option_1 ) ) {
					$out .= '<li>' . get_the_title( $current_id_1 ) . '<ul>' . "\n";
					foreach( $option_1 as $option_id_2 => $option_2 ) {
						$current_id_2 = tcp_get_current_id( $option_id_2, OptionCustomPostType::$PRODUCT_OPTION );
						$option_price_2 = tcp_get_the_price( $option_id_2 );
						$option_price_2 = $option_price_1 + tcp_get_the_price_to_show( $post_id, $option_price_2 );
						$script = 'jQuery(\'#tcp_option_id_' . $post_id . '\').val(jQuery(\'#tcp_list_id_' . $option_id_2 . '\').val());';
						$script .= 'jQuery(\'.tcp_thumbnail_' . $post_id .'\').hide();jQuery(\'.tcp_thumbnail_option_' . $option_id_2 .'\').show();';
						$out .= '<li><input type="radio" ';
	 					if ( $first ) {
							$out .= 'checked="true"';
							$value_first = $option_id_1 . '-' . $option_id_2;
							$first = false;
						}
						$out .= ' onclick="' . $script . '" id="tcp_list_id_' . $option_id_2 . '" name="tcp_list_id_' . $post_id . '" value="' . $option_id_1 . '-' . $option_id_2 . '"/>';
						if ( $first ) $value_first = $option_id_1 . '-' . $option_id_2;
						$first = false;
						$out .= '<label for="tcp_list_id_' . $option_id_2 . '">' . htmlspecialchars( get_the_title( $current_id_2 ) );

						if ( $option_price_2 > 0 ) $out .= ':&nbsp;<span class="tcp_price">' . apply_filters( 'tcp_options_title', tcp_format_the_price( $option_price_2 ), $post_id, $option_id_1, $option_id_2 ) . '</span>';
						$out .= '</label>';
						$out .= '</li>' . "\n";
					}
					$out .= '</ul></li>' . "\n";
				} else {
					$script = 'jQuery(\'#tcp_option_id_' . $post_id . '\').val(jQuery(\'#tcp_list_id_' . $option_id_1 . '\').val());';
					$script .= 'jQuery(\'.tcp_thumbnail_' . $post_id .'\').hide();jQuery(\'.tcp_thumbnail_option_' . $option_id_1 .'\').show();';
					$out .= '<li><input type="radio" ';
					if ( $first ) {
						$out .= 'checked="true"';
						$value_first = $option_id_1;
						$first = false;
					}
					$out .= ' onclick="' . $script . '" id="tcp_list_id_' . $option_id_1 . '" name="tcp_list_id_' . $post_id . '" value="' . $option_id_1 . '"/>';
					$out .= '<label for="tcp_list_id_' . $option_id_1 . '" class="tcp_price">';
					$option_title = get_the_title( $current_id_1 );
					if ( $option_price_1 > 0 ) $option_title .= ': ' . tcp_format_the_price( $option_price_1 );
					$option_title = apply_filters( 'tcp_options_title', $option_title, $post_id, $option_id_1 );
					$out .= $option_title . '</label>';
					$out .= '</li>' . "\n";
				}
				add_filter( 'the_title', 'wptexturize' );
			}
			$out .= '<input type="hidden" id="tcp_option_id_' . $post_id . '" name="tcp_option_id[]" value="' . $value_first . '" />';
			$out .= '</ul>' . "\n";
		}
		$out = apply_filters( 'tcp_po_get_option_select', $out, $post_id );
		return $out;
	}

	function tcp_get_image_in_grouped( $image, $post_id, $size = 'thumbnail' ) {
		global $thecartpress;
		$args['size'] = $thecartpress->get_setting( 'image_size_grouped_by_button', 'thumbnail' );
		return $this->tcp_get_image_in_content( $image, $post_id, $args );
	}
	
	function tcp_get_image_in_content( $image, $post_id, $args = false ) {
		if ( 'SIMPLE' != tcp_get_the_product_type( $post_id ) ) return $image;
		$default_post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
		$options = RelEntitiesOptions::getOptionsTree( $default_post_id );
		if ( is_array( $options ) && count( $options ) > 0 ) {
			$image = '';
			$first = true;
			foreach( $options as $option_id_1 => $option_1 ) {
				if ( is_array( $option_1 ) ) {
					foreach( $option_1 as $option_id_2 => $option_2 ) {
						if ( $first ) {
							$first = false;
							$image .= '<script>jQuery(document).ready(function() {
								jQuery(\'.tcp_thumbnail_' . $post_id .'\').hide();jQuery(\'.tcp_thumbnail_option_' . $option_id_2 .'\').show();
							});</script>';
						}
						if ( has_post_thumbnail( $option_id_2 ) ) {
							$image .= $this->get_thumbnail_link( $option_id_2, $args, $post_id, $option_id_2 );
						} else {
							$option_id_2 = tcp_get_default_id( $option_id_2, OptionCustomPostType::$PRODUCT_OPTION );
							if ( has_post_thumbnail( $option_id_2 ) ) {	
								$image .= $this->get_thumbnail_link( $option_id_2, $args, $post_id, $option_id_2 );
							} elseif ( has_post_thumbnail( $option_id_1 ) ) {
								$image .= $this->get_thumbnail_link( $option_id_1, $args, $post_id, $option_id_2 );
							} else {
								$option_id_1 = tcp_get_default_id( $option_id_1, OptionCustomPostType::$PRODUCT_OPTION );
								if ( has_post_thumbnail( $option_id_1 ) ) {
									$image .= $this->get_thumbnail_link( $option_id_1, $args, $post_id, $option_id_2 );
								} elseif ( has_post_thumbnail( $post_id ) ) {
									$image .= $this->get_thumbnail_link( $post_id, $args, $post_id, $option_id_2 );
								} else {
									$image .= $this->get_thumbnail_link( $default_post_id, $args, $post_id, $option_id_2 );
								}
							}
						}
					}
				} else {
					if ( $first ) {
						$first = false;
						$image .= '<script>jQuery(document).ready(function() {
							jQuery(\'.tcp_thumbnail_' . $post_id .'\').hide();jQuery(\'.tcp_thumbnail_option_' . $option_id_1 .'\').show();
						});</script>';
					}
					if ( has_post_thumbnail( $option_id_1 ) ) {
						$image .= $this->get_thumbnail_link( $option_id_1, $args, $post_id, $option_id_1 );
					} else {
						$option_id_1 = tcp_get_default_id( $option_id_1, OptionCustomPostType::$PRODUCT_OPTION );
						if ( has_post_thumbnail( $option_id_1 ) ) {
							$image .= $this->get_thumbnail_link( $option_id_1, $args, $post_id, $option_id_1 );
						} elseif ( has_post_thumbnail( $post_id ) ) {
							$image .= $this->get_thumbnail_link( $post_id, $args, $post_id, $option_id_1 );
						} else {
							$image .= $this->get_thumbnail_link( $default_post_id, $args, $post_id, $option_id_1 );
						}
					}
				}
			}
		}
		return $image;
	}

	private function get_thumbnail_link( $post_id, $args, $parent_id, $option_id ) {
		$class = 'class="tcp_thumbnail_' . $parent_id . ' tcp_thumbnail_option_' . $option_id . '"';
		$image = tcp_get_the_thumbnail_with_permalink( $post_id, $args, false );
		$link = '<span '. $class . ' style="display:none;">' . $image . '</span>';
		return $link;
	}

	function tcp_update_price_search_controls() { ?>
		<tr valign="top">
		<th scope="row"><label for="apply_to_options"><?php _e( 'Apply to options', 'tcp_po' ); ?>:</label></th>
		<td>
			<input type="checkbox" id="apply_to_options" name="apply_to_options" value="yes" <?php if ( isset( $_REQUEST['apply_to_options'] ) ) :?>checked="true"<?php endif; ?>/>
		</td>
		</tr><?php
	}

	function tcp_update_price( $post ) {
		if ( isset( $_REQUEST['apply_to_options'] ) ) {
			$first_level_options = RelEntities::select( $post->ID, 'OPTIONS');
			if ( is_array( $first_level_options ) && count( $first_level_options ) > 0 ) {
				foreach( $first_level_options as $first_level_option ) {
					if ( isset( $_REQUEST['tcp_new_price_' . $first_level_option->id_to] ) ) {
						//$new_price = (float)$_REQUEST['tcp_new_price_' . $first_level_option->id_to];
						$new_price = $_REQUEST['tcp_new_price_' . $first_level_option->id_to];
						$new_price = tcp_input_number( $new_price );
						update_post_meta( $first_level_option->id_to, 'tcp_price', $new_price );
					}
					$second_level_options = RelEntities::select( $first_level_option->id_to, 'OPTIONS');
					if ( is_array( $second_level_options ) && count( $second_level_options ) > 0 ) {
						foreach( $second_level_options as $second_level_option ) {
							if ( isset( $_REQUEST['tcp_new_price_' . $second_level_option->id_to] ) ) {
								//$new_price = (float)$_REQUEST['tcp_new_price_' . $second_level_option->id_to];
								$new_price = $_REQUEST['tcp_new_price_' . $second_level_option->id_to];
								$new_price = tcp_input_number( $new_price );
								update_post_meta( $second_level_option->id_to, 'tcp_price', $new_price );
							}
						}
					}
				}
			}
		}
	}

	function tcp_update_price_controls( $post ) {
		if ( isset( $_REQUEST['apply_to_options'] ) ) {
			$currency = tcp_get_the_currency();
			$first_level_options = RelEntities::select( $post->ID, 'OPTIONS');
			if ( is_array( $first_level_options ) && count( $first_level_options ) > 0 ) {
				$per = isset( $_REQUEST['per'] ) ? (int)$_REQUEST['per'] : 0;
				$fix = isset( $_REQUEST['fix'] ) ? (int)$_REQUEST['fix'] : 0;
				$update_type = isset( $_REQUEST['update_type'] ) ? $_REQUEST['update_type'] : 'per';
				foreach( $first_level_options as $first_level_option ) {
					$price = tcp_get_the_price( $first_level_option->id_to );
					if ( $update_type == 'per' ) {
						$new_price = $price * (1 + $per / 100);
					} else { //fixed
						$new_price = $price + $fix;
					}?>
		<tr>
			<td><span style="padding-left: 3em;"><?php echo get_the_title( $first_level_option->id_to ); ?></span></td>
			<td><?php echo tcp_format_the_price( $price ); ?></td>
			<td><input type="text" value="<?php echo tcp_number_format( $new_price ); ?>" name="tcp_new_price_<?php echo $first_level_option->id_to; ?>" size="13" maxlength="13" /> <?php echo $currency; ?></td>
			<td>&nbsp;</td>
		</tr><?php	$second_level_options = RelEntities::select( $first_level_option->id_to, 'OPTIONS');
					if ( is_array( $second_level_options ) && count( $second_level_options ) > 0 ) {
						foreach( $second_level_options as $second_level_option ) {
							$price = tcp_get_the_price( $second_level_option->id_to );
							if ( $update_type == 'per' ) {
								$new_price = $price * (1 + $per / 100);
							} else { //fixed
								$new_price = $price + $fix;
							}?>
		<tr>
			<td><span style="padding-left: 6em;"><?php echo get_the_title( $second_level_option->id_to ); ?></span></td>
			<td><?php echo tcp_format_the_price( $price ); ?></td>
			<td><input type="text" value="<?php echo tcp_number_format( $new_price ); ?>" name="tcp_new_price_<?php echo $second_level_option->id_to; ?>" size="13" maxlength="13" /> <?php echo $currency; ?></td>
			<td>&nbsp;</td>
		</tr><?php		}
					}
				}
			}
		}
	}

	function tcp_update_stock_search_controls() {?>
		<tr valign="top">
		<th scope="row"><label for="apply_to_options"><?php _e( 'Apply to options', 'tcp_po' ); ?>:</label></th>
		<td>
			<input type="checkbox" id="apply_to_options" name="apply_to_options" value="yes" <?php if ( isset( $_REQUEST['apply_to_options'] ) ) :?>checked="true"<?php endif; ?>/>
		</td>
		</tr><?php
	}

	function tcp_update_stock( $post ) {
		if ( isset( $_REQUEST['apply_to_options'] ) ) {
			$first_level_options = RelEntities::select( $post->ID, 'OPTIONS');
			if ( is_array( $first_level_options ) && count( $first_level_options ) > 0 ) {
				foreach( $first_level_options as $first_level_option ) {
					$new_stock = isset( $_REQUEST['tcp_new_stock_' . $first_level_option->id_to] ) ? (int)$_REQUEST['tcp_new_stock_' . $first_level_option->id_to] : '';
					if ( $new_stock == '' || $new_stock < -1 ) $new_stock = -1;
					update_post_meta( $first_level_option->id_to, 'tcp_stock', $new_stock );
					$second_level_options = RelEntities::select( $first_level_option->id_to, 'OPTIONS');
					if ( is_array( $second_level_options ) && count( $second_level_options ) > 0 ) {
						foreach( $second_level_options as $second_level_option ) {
							$new_stock = isset( $_REQUEST['tcp_new_stock_' . $second_level_option->id_to] ) ? (int)$_REQUEST['tcp_new_stock_' . $second_level_option->id_to] : '';
							if ( $new_stock == '' || $new_stock < -1 ) $new_stock = -1;
							update_post_meta( $second_level_option->id_to, 'tcp_stock', $new_stock );
						}
					}
				}
			}
		}
	}

	function tcp_update_stock_controls( $post ) {
		if ( isset( $_REQUEST['apply_to_options'] ) ) {
			$first_level_options = RelEntities::select( $post->ID, 'OPTIONS');
			if ( is_array( $first_level_options ) && count( $first_level_options ) > 0 ) {
				$added_stock = isset( $_REQUEST['added_stock'] ) ? (int)$_REQUEST['added_stock'] : 0;
				foreach( $first_level_options as $first_level_option ) {
					$stock = tcp_get_the_stock( $first_level_option->id_to );
					if ( $added_stock == -1 || $stock == -1 )
						$new_stock = -1;
					elseif ( $added_stock == 0 ) {
						$new_stock = $stock;
					} else {
						if ( $stock > -1 ) {
							$new_stock = $stock + $added_stock;
						} else {
							$new_stock = $added_stock;
						}
					}?>
				<tr>
					<td><span style="padding-left: 3em;"><?php echo get_the_title( $first_level_option->id_to ); ?></span></td>
					<td><?php echo $stock, '&nbsp;', __( 'units', 'tcp_po' ) ; ?></td>
					<td><input type="text" value="<?php echo $new_stock; ?>" name="tcp_new_stock_<?php echo $first_level_option->id_to; ?>" id="tcp_new_stock_<?php echo $first_level_option->id_to; ?>" size="13" maxlength="13" /> <?php _e( 'units', 'tcp_po' ); ?>
					<input type="button" value="<?php _e( 'no stock', 'tcp_po' ); ?>" onclick="jQuery('#tcp_new_stock_<?php echo $first_level_option->id_to; ?>').val(-1);" class="button-secondary" /></td>
					<td>&nbsp;</td>
				</tr><?php
					$second_level_options = RelEntities::select( $first_level_option->id_to, 'OPTIONS');
					if ( is_array( $second_level_options ) && count( $second_level_options ) > 0 ) {
						foreach( $second_level_options as $second_level_option ) {
							$stock = tcp_get_the_stock( $second_level_option->id_to );
							if ( $added_stock == -1 || $stock == -1 )
								$new_stock = -1;
							elseif ( $added_stock == 0 ) {
								$new_stock = $stock;
							} else {
								if ( $stock > -1 ) {
									$new_stock = $stock + $added_stock;
								} else {
									$new_stock = $added_stock;
								}
							}?>
				<tr>
					<td><span style="padding-left: 6em;"><?php echo get_the_title( $second_level_option->id_to ); ?></span></td>
					<td><?php echo $stock, '&nbsp;', __( 'units', 'tcp_po' ); ?></td>
					<td><input type="text" value="<?php echo $new_stock; ?>" name="tcp_new_stock_<?php echo $second_level_option->id_to; ?>" id="tcp_new_stock_<?php echo $second_level_option->id_to; ?>" size="13" maxlength="13" /> <?php _e( 'units', 'tcp_po' ); ?>
					<input type="button" value="<?php _e( 'no stock', 'tcp_po' ); ?>" onclick="jQuery('#tcp_new_stock_<?php echo $second_level_option->id_to; ?>').val(-1);" class="button-secondary" /></td>
					<td>&nbsp;</td>
				</tr>
		<?php			}
					}
				}
			}
		}
	}

	function tcp_get_the_price_label( $label, $post_id ) {
		$type = tcp_get_the_meta( 'tcp_type', $post_id );
		if ( $type == 'SIMPLE' ) {
			$price = tcp_get_the_price( $post_id );
			$options_1 = RelEntities::select( $post_id, 'OPTIONS' );
			if ( is_array( $options_1 ) && count( $options_1 ) > 0 ) {
				$min = false;
				$max = false;
				foreach( $options_1 as $option_1 ) {//first level
					$price_1 = tcp_get_the_price( $option_1->id_to ) + $price;
					$options_2 = RelEntities::select( $option_1->id_to, 'OPTIONS' );
					if ( is_array( $options_2 ) && count( $options_2 ) > 0 ) {
						foreach( $options_2 as $option_2 ) {//second level
							$price_2 = tcp_get_the_price( $option_2->id_to ) + $price_1;
							if ( ! $min || $min > $price_2 ) $min = $price_2;
							if ( ! $max || $max < $price_2 ) $max = $price_2;
						}
					} else {
						if ( ! $min || $min > $price_1 ) $min = $price_1;
						if ( ! $max || $max < $price_1 ) $max = $price_1;
					}					
				}
				if ( $min != $max ) {
					$min = tcp_get_the_price_to_show( $post_id, $min );
					$max = tcp_get_the_price_to_show( $post_id, $max );
					$label = sprintf( __( '%s to %s', 'tcp_po' ), tcp_format_the_price( $min ), tcp_format_the_price( $max ) );
				} else {
					$min = tcp_get_the_price_to_show( $post_id, $min );
					$label = tcp_format_the_price( $min );
				}
				return apply_filters( 'tcp_po_get_the_price_label', $label, $post_id );
			} else {
				return apply_filters( 'tcp_po_get_the_price_label', $label, $post_id );
			}
		} else if ( $type == 'GROUPED' ) {
			$products = RelEntities::select( $post_id );
			if ( is_array( $products ) && count( $products ) > 0 ) {
				$min = false;
				$max = false;
				foreach( $products as $product ) {//grouped level
					if ( ! tcp_is_exclude_range( $product->id_to ) ) {
						$price = tcp_get_the_price( $product->id_to );
						$options_1 = RelEntities::select( $product->id_to, 'OPTIONS' );
						if ( is_array( $options_1 ) && count( $options_1 ) > 0 ) {
							foreach( $options_1 as $option_1 ) {//first level
								$price_1 = tcp_get_the_price( $option_1->id_to ) + $price;
								$options_2 = RelEntities::select( $option_1->id_to, 'OPTIONS' );
								if ( is_array( $options_2 ) && count( $options_2 ) > 0 ) {
									foreach( $options_2 as $option_2 ) {//second level
										$price_2 = tcp_get_the_price( $option_2->id_to ) + $price_1;
										if ( ! $min || $min > $price_2 ) $min = $price_2;
										if ( ! $max || $max < $price_2 ) $max = $price_2;
									}
								} else {
									if ( ! $min || $min > $price_1 ) $min = $price_1;
									if ( ! $max || $max < $price_1 ) $max = $price_1;
								}					
							}
						} else {
							if ( ! $min || $min > $price ) $min = $price;
							if ( ! $max || $max < $price ) $max = $price;
						}
					}
				}
				if ( $min != $max ) {
					$min = tcp_get_the_price_to_show( $post_id, $min );
					$max = tcp_get_the_price_to_show( $post_id, $max );
					$label = sprintf( __( '%s to %s', 'tcp_po' ), tcp_format_the_price( $min ), tcp_format_the_price( $max ) );
				} else {
					$min = tcp_get_the_price_to_show( $post_id, $min );
					$label = tcp_format_the_price( $min );
				}
				return apply_filters( 'tcp_po_get_the_price_label', $label, $post_id );
			} else {
				return apply_filters( 'tcp_po_get_the_price_label', $label, $post_id );
			}
		} else {
			return apply_filters( 'tcp_po_get_the_price_label_unkonw_product_type', $label, $post_id );
		}
	}

	function tcp_copying_product( $post_id, $new_post_id ) {
		$post_id = tcp_get_default_id( $post_id );
		$rels = RelEntities::select( $post_id, 'OPTIONS' );
		foreach( $rels as $rel ) {
			$new_post_id = tcp_duplicate_translatable_post( $post_id );
			RelEntities::insert( $post_id, $new_post_id, $rel->rel_type, $rel->list_order, $rel->units );
		}
	}

	//
	//Custom fields hooks
	//
	function tcp_options_metabox_custom_fields( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type == OptionCustomPostType::$PRODUCT_OPTION ) {
			tcp_display_custom_fields( $post_id, $post_type );
		}
	}

	function tcp_options_metabox_save_custom_fields( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type == OptionCustomPostType::$PRODUCT_OPTION ) {
			tcp_save_custom_fields( $post_id, $post_type );
		}
	}

	function tcp_options_metabox_delete_custom_fields( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type == OptionCustomPostType::$PRODUCT_OPTION ) {
			tcp_delete_custom_fields( $post_id, $post_type );
		}
	}
	//
	// end Custom fields hooks
	//
	function init() {
		require_once( WP_PLUGIN_DIR .'/thecartpress-productoptions/customposttypes/OptionCustomPostType.class.php' );
		require_once( WP_PLUGIN_DIR .'/thecartpress-productoptions/daos/RelEntitiesOptions.class.php' );
		if ( function_exists( 'load_plugin_textdomain' ) ) load_plugin_textdomain( 'tcp_po', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		if ( ! function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'thecartpress/TheCartPress.class.php' ) ) add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_filter( 'tcp_get_product_types', array( &$this, 'tcp_get_product_types' ) );
	}

	function admin_notices() { ?>
		<div class="error">
			<p><?php _e( '<strong>Product Options for TheCartPress</strong> requires TheCartPress plugin is activated.', 'tcp_po' ); ?></p>
		</div><?php
	}
}

$productOptionsForTheCartPress = new ProductOptionsForTheCartPress();
}

function tcp_the_buy_button_options( $product_id, $post_id = 0, $echo = true ) {
	$html = '<input type="hidden" name="tcp_option_id[]" id="tcp_option_id_' . $product_id. '" value="0" />';
	$html .= '<input type="hidden" name="tcp_option_1_id[]" id="tcp_option_1_id_' . $product_id. '" value="0" />';
	$html .= '<input type="hidden" name="tcp_option_2_id[]" id="tcp_option_2_id_' . $product_id . '" value="0" />';
	$html = apply_filters( 'tcp_buy_button_options', $html, $product_id, $post_id );
	if ( $echo ) echo $html;
	else return $html;
}

function tcp_has_options( $post_id ) {
	return ProductOptionsForTheCartPress::tcp_has_option( $post_id );
}
?>
