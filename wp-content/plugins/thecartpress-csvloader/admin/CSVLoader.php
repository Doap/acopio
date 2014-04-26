<?php
/**
 * This file is part of TheCartPress-csvloader.
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

/**
 * NOTICES
 * -------
 * Since TCP 1.0.9: the tax should be an index, not the real tax rate.
 */
if ( ! session_id() ) session_start();
$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'tcp_product';
$separator = isset( $_REQUEST['separator'] ) ? $_REQUEST['separator'] : ',';
//$enclosure	= isset( $_REQUEST['enclosure'] ) ? $_REQUEST['enclosure'] : '"';
$titeled = isset( $_REQUEST['titeled'] );
$updated_products	= isset( $_REQUEST['updated_products'] ) ? $_REQUEST['updated_products'] : 'all';
$taxonomy = isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : 'tcp_product_category';
$cat = isset( $_REQUEST['tcp_cat'] ) ? $_REQUEST['tcp_cat'] : '';
$tcp_status	= isset( $_REQUEST['tcp_status'] ) ? $_REQUEST['tcp_status'] : '';
$display_rows = isset( $_REQUEST['display_rows'] ) ? $_REQUEST['display_rows'] : 'four';
$data = array();
$titles = array();
$errors = array();

if ( isset( $_REQUEST['tcp_load_csv'] ) && isset( $_FILES['upload_file'] ) && strlen( $_FILES['upload_file']['tmp_name'] ) > 0 ) {
	if ( ( $handle = fopen( $_FILES['upload_file']['tmp_name'], 'r' ) ) !== FALSE ) {
		while ( ( $line = fgetcsv( $handle, 10240, $separator ) ) !== FALSE ) {
			$data[] = $line;
		}
		fclose( $handle );
		if ( $titeled ) {
			$titles = $data[0];
			unset( $data[0] );
		} else {
			for( $i = 0; $i < count( $data[0] ); $i++ )
				$titles[] = 'col_' . $i;
		}
	}
	$_SESSION['tcp_csv_titles'] = $titles;
	$_SESSION['tcp_csv_data'] = $data;
} elseif ( isset( $_REQUEST['tcp_load_products_from_csv'] ) && isset( $_SESSION['tcp_csv_titles'] ) ) {
	$titles = $_SESSION['tcp_csv_titles'];
	$data = $_SESSION['tcp_csv_data'];
	unset( $_SESSION['tcp_csv_titles'] );
	unset( $_SESSION['tcp_csv_data'] );
	if ( is_array( $data ) ) {
		$custom_field_defs = tcp_get_custom_fields_def( $post_type );
		$taxonomies = get_object_taxonomies( $post_type );
		$count = 0;
		$i = 0;
		foreach( $data as $cols ) {
			$i++;
			$use_name = false;
			$name = '';
			$use_content = false;
			$content = '';
			$use_excerpt = false;
			$excerpt = '';
			$use_price = false;
			$price = 0;
			$use_order = false;
			$order = '';
			$use_weight = false;
			$weight = 0;
			$sku = '';
			$use_stock = false;
			$stock = -1;
			$use_tax = false;
			$tax = 0;
			$upload = '';
			$attachments = array();
			$thumbnail = '';
			$custom_values = array();
			$taxo_values = array();
			foreach( $cols as $i => $col ) {
				$col_names = isset( $_REQUEST['col_' . $i] ) ? $_REQUEST['col_' . $i] : array();
				if ( is_array( $col_names ) && count( $col_names ) > 0 ) {
					foreach( $col_names as $col_name ) {
						if ( 'tcp_name' == $col_name ) {
							$use_name = true;
							$name = $col;//string
						}
						if ( 'tcp_content' == $col_name ) {
							$use_content = true;
							$content = $col;//string
						}
						if ( 'tcp_excerpt' == $col_name ) {
							$use_excerpt = true;
							$excerpt = $col;//string
						}
						if ( 'tcp_price' == $col_name ) {
							$use_price = true;
							$price = tcp_csv_get_price( $col );
						}
						if ( 'tcp_order' == $col_name ) {
							$use_order = true;
							$order = $col;//string
						}
						if ( 'tcp_weight' == $col_name ) {
							$use_weight = true;
							$weight = (float)$col;
						}
						if ( 'tcp_sku' == $col_name ) {
							$sku = $col;
						}
						if ( 'tcp_stock' == $col_name ) {
							$use_stock = true;
							$stock = (int)$col;
						}
						if ( 'tcp_tax' == $col_name ) {
							$use_tax = true;
							$tax = (int)$col;
						}
						if ( 'tcp_upload' == $col_name ) {
							$upload = $col;//path
						}
						if ( 'tcp_attachment' == $col_name ) {
							$attachments[] = $col;//url
						}
						if ( 'tcp_thumbnail' == $col_name ) {
							$thumbnail = $col;//url
						}
						if ( is_array( $custom_field_defs ) && count( $custom_field_defs ) > 0 ) {
							foreach( $custom_field_defs as $custom_field_def ) {
								if ( $col_name == $custom_field_def['id'] ) {
									$custom_values[$col_name] = $col;
									break;
								}
							}
						}
						if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
							foreach( $taxonomies as $taxmy ) {
								if ( $col_name == 'tcp_tax_' . $taxmy ) {
									$taxo_values[$taxmy] = explode( ',', $col );
									break;
								}
							}
						}

					}
				}
			}
			$post = array(
				//'comment_status'=> 'closed',
				'post_status'	=> $tcp_status,
				'post_type'		=> $post_type,
			);
			//$post = apply_filters( 'tcp_csv_loader_new_post', $post, $cols );//has been moved some lines down
			$post_id = false;
			$process_this_row = true;
			if ( strlen( $sku ) > 0 ) {
				$post_id = tcp_get_product_by_sku( $sku );
				$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
				if ( ( $post_id && $updated_products == 'new' ) || ( $post_id === false && $updated_products == 'updated' ) ) $process_this_row = false;
				if ( $post_id ) {
					$post['ID'] = $post_id;
					$current_post = get_post( $post_id );
					$post['post_title'] = $current_post->post_title;
					$post['post_content'] = $current_post->post_content;
					$post['post_excerpt'] = $current_post->post_excerpt;
				}
			}
			if ( $process_this_row ) {
				if ( $use_name ) $post['post_title'] = $name;
				if ( $use_content ) $post['post_content'] = $content;
				if ( $use_excerpt ) $post['post_excerpt'] = $excerpt;
				$post = apply_filters( 'tcp_csv_loader_new_post', $post, $cols );
				$post_id = wp_insert_post( $post, true );
				if ( is_wp_error( $post_id ) ) die( $post_id->get_error_message() );
				if ( $cat > 0 ) wp_set_object_terms( $post_id, (int)$cat, $taxonomy, false );
				if ( $use_tax ) update_post_meta( $post_id, 'tcp_tax_id', $tax );
				update_post_meta( $post_id, 'tcp_is_visible', true );
				update_post_meta( $post_id, 'tcp_is_downloadable', false );
				update_post_meta( $post_id, 'tcp_max_downloads', 0 );
				update_post_meta( $post_id, 'tcp_days_to_expire', 0 );
				update_post_meta( $post_id, 'tcp_type', 'SIMPLE' );
				if ( $use_price ) update_post_meta( $post_id, 'tcp_price', $price );
				if ( $use_weight ) update_post_meta( $post_id, 'tcp_weight', $weight );
				if ( $use_order ) update_post_meta( $post_id, 'tcp_order', $order );
				elseif ( ! isset( $post['ID'] ) ) update_post_meta( $post_id, 'tcp_order', '' );
				update_post_meta( $post_id, 'tcp_sku', $sku );
				if ( $use_stock ) update_post_meta( $post_id, 'tcp_stock', $stock );
				else update_post_meta( $post_id, 'tcp_stock', -1 );

				foreach( $custom_values as $id => $custom_value ) {
					$custom_value = apply_filters( 'tcp_csv_loader_custom_value', $custom_value, $post_id, $cols );
					update_post_meta( $post_id, $id, $custom_value );
				}

				foreach( $taxo_values as $tax => $terms ) {
					if ( ! is_array( $terms ) ) $terms = array( $terms );
					wp_set_post_terms( $post_id, null, $tax );
					foreach( $terms as $term ) {
						$new_term = term_exists( $term, $tax );
						if ( ! is_array( $new_term ) ) {
							$term = apply_filters( 'tcp_csv_loader_term', $term, $post_id, $cols );
							$new_term = wp_insert_term(	$term, $tax, array( 'slug' => sanitize_key( $term ) ) );
							if ( is_wp_error( $new_term ) ) $errors[] = $new_term->get_error_message() . ' (' . $term . ')';
							//tcp_add_term_translation( $new_term['term_id'], $tax );
						}
						//wp_set_post_terms( $post_id, $term, $tax, false );
					}
					wp_set_object_terms( $post_id, $terms, $tax );
				}

				if ( strlen( $upload )  > 0 ) {
					update_post_meta( $post_id, 'tcp_is_downloadable', true );
					update_post_meta( $post_id, 'tcp_max_downloads', -1 );
					update_post_meta( $post_id, 'tcp_days_to_expire', -1 );
					$upload = apply_filters( 'tcp_csv_loader_upload_url', $upload, $post_id, $cols );
					tcp_set_the_file( $post_id, $upload );
				}

				if ( $post_id > 0 && ( is_array( $attachments ) && count( $attachments ) > 0 ) ) { // || strlen( $thumbnail ) > 0 ) {
					$post_attachments = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image' ) );
					foreach( $post_attachments as $post_attachment )
						wp_delete_attachment( $post_attachment->ID, true );
				}

				if ( is_array( $attachments ) && count( $attachments ) > 0 ) {
					foreach( $attachments as $url ) {
						if ( $url != '' ) {
							$base = basename( $url );
							$path = wp_upload_dir();
							$path = $path['path'];
							$dest = $path . '/' . $base;
							$url = str_replace( ' ', '%20', $url );
							$url = apply_filters( 'tcp_csv_loader_attachment_url', $url, $post_id, $cols );
							if ( tcp_copy( $url, $dest ) ) {
								$wp_filetype = wp_check_filetype( basename( $dest ), null );
								$attachment = array(
									'post_mime_type'	=> $wp_filetype['type'],
									'post_title'		=> preg_replace( '/\.[^.]+$/', '', basename( $dest ) ),
									'post_content'		=> '',
									'post_status'		=> 'inherit',
									'post_parent'		=> $post_id,
								);
								$attach_id = wp_insert_attachment( $attachment, $dest, $post_id );
								require_once( ABSPATH . 'wp-admin/includes/image.php' );
								$attach_data = wp_generate_attachment_metadata( $attach_id, $dest );
								wp_update_attachment_metadata( $attach_id,  $attach_data );
							} else {
								$errors[] = sprintf( __( 'Error copying %s to %s (attachment)', 'tcp_csvl' ), $url, $dest );
							}
						}
					}
				}
				if ( strlen( $thumbnail ) > 0 ) {
					$base = basename( $thumbnail );
					$path = wp_upload_dir();
					$path = $path['path'];
					$dest = $path . '/' . $base;
					$thumbnail = str_replace( ' ', '%20', $thumbnail );
					$thumbnail = apply_filters( 'tcp_csv_loader_attachment_url', $thumbnail, $post_id, $cols );
					if ( tcp_copy( $thumbnail, $dest ) ) {
						$wp_filetype = wp_check_filetype( basename( $dest ), null );
						$attachment = array(
							'post_mime_type'	=> $wp_filetype['type'],
							'post_title'		=> preg_replace( '/\.[^.]+$/', '', basename( $dest ) ),
							'post_content'		=> '',
							'post_status'		=> 'inherit',
							'post_parent' 		=> $post_id,
						);
						$attach_id = wp_insert_attachment( $attachment, $dest, $post_id );
						// you must first include the image.php file for the function wp_generate_attachment_metadata() to work
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						$attach_data = wp_generate_attachment_metadata( $attach_id, $dest );
						wp_update_attachment_metadata( $attach_id,  $attach_data );
						update_post_meta( $post_id, '_thumbnail_id', $attach_id );
					} else {
						$errors[] = sprintf( __( 'Error copying %s to %s (thumbnail)', 'tcp_csvl' ), $thumbnail, $dest );
					}
				}
				do_action( 'tcp_csv_loader_row', $post_id, $cols );
				$count++;
			}
		}
		?>
		<div id="message" class="updated"><p>
			<?php printf( __( '%s products have been uploaded', 'tcp_csvl' ), $count ); ?>
		</p>
		<?php if ( count( $errors ) > 0 ) { ?>
		<ul><?php foreach( $errors as $error ) { ?>
			<li><?php echo $error; ?></li>
		<?php } ?></ul><?php } ?>
		</div><?php
	} else {?>
		<div id="message" class="error"><p>
			<?php _e( 'No product has been uploaded', 'tcp_csvl' ); ?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_load_csv'] ) && isset( $_FILES['upload_file'] ) && strlen( $_FILES['upload_file']['tmp_name'] ) == 0 ) { ?>
<div id="message" class="error"><p>
	<?php _e( 'File cannot be empty', 'tcp_csvl' ); ?>
</p></div>
<?php } ?>
<div class="wrap">

<h2><?php _e( 'CSV Loader', 'tcp_csvl' ); ?></h2>
<ul class="subsubsub">
</ul><!-- subsubsub -->

<div class="clear"></div>

<form method="post" enctype="multipart/form-data">
	<table class="form-table">
	<tbody>
	<tr valign="top">
		<th scope="row">
			<label for="post_type"><?php _e( 'Post type', 'tcp_csvl' )?>:</label>
		</th>
		<td>
			<select name="post_type" id="post_type">
			<?php foreach( tcp_get_saleable_post_types() as $pt ) : $obj = get_post_type_object( $pt ); ?>
				<option value="<?php echo $pt; ?>"<?php selected( $post_type, $pt ); ?>><?php echo $obj->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
			<input type="submit" name="tcp_load_taxonomies" value="<?php _e( 'Load taxonomies', 'tcp_csvl' ); ?>" class="button-secondary"/>
			<p class="description"><?php _e( 'Select what type of saleable post type do you want to use, usually, Products', 'tcp_csvl' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="updated"><?php _e( 'Which products to load?', 'tcp_csvl' )?>:</label>
		</th>
		<td>
			<label><input type="radio" name="updated_products" id="all_products" value="all" <?php checked( 'all', $updated_products ); ?> /> <?php _e( 'All products', 'tcp_csvl' ); ?></label>
			<br/><label><input type="radio" name="updated_products" id="new_products" value="new" <?php checked( 'new', $updated_products ); ?> /> <?php _e( 'Only New products', 'tcp_csvl' ); ?></label>
			<br/><label><input type="radio" name="updated_products" id="updated_products" value="updated" <?php checked( 'updated', $updated_products ); ?> /> <?php _e( 'Only Updated products', 'tcp_csvl' ); ?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="taxonomy"><?php _e( 'Taxonomy', 'tcp_csvl' )?>:</label>
		</th>
		<td>
			<select name="taxonomy" id="taxonomy">
			<?php foreach( get_object_taxonomies( $post_type ) as $taxmy ) : $tax = get_taxonomy( $taxmy ); ?>
				<option value="<?php echo esc_attr( $taxmy ); ?>"<?php selected( $taxmy, $taxonomy ); ?>><?php echo $tax->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
			<p class="description"><?php _e( 'Select the type of taxonomy you want to use. Usually, select Categories (of products).', 'tcp_csvl' ); ?></p>
			<p class="description"><?php _e( 'This action allows to select, later, the category term where to assign all the products.', 'tcp_csvl' ); ?></p>
			<p class="description"><?php _e( 'However, each product could be assigned to different terms if one of the columns, in the CSV file, is used to assign them.', 'tcp_csvl' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="separator"><?php _e( 'Separator', 'tcp_csvl' ); ?>:</label>
		</th>
		<td>
			<input type="text" name="separator" id="separator" value="<?php echo $separator; ?>" size="2" maxlength="1"/>
			<label for="titeled"><?php _e( 'Column titles in the first line', 'tcp_csvl' ); ?>:</label>
			<input type="checkbox" name="titeled" id="titeled" <?php checked( $titeled ); ?> />
			<p class="description"><?php _e( 'These fields are used to define the format of the csv.', 'tcp_csvl' ); ?></p>
			<p class="description"><?php _e( 'The Separator, usually, is a comma or the vertical slash "|". If the CSV has column titles in the first line, set the field "Column titles in the first line".', 'tcp_csvl' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="display_rows" value=""><?php _e( 'Display Rows', 'tcp_csvl' ); ?>:</label>
		</th>
		<td>
			<label><input type="radio" name="display_rows" id="display_rows_4" value="four" <?php checked( $display_rows, 'four' ); ?>/> <?php _e( 'Display only four rows', 'tcp_csvl' ); ?></label>
			<br/>
			<label><input type="radio" name="display_rows" id="display_rows_all" value="all" <?php checked( $display_rows, 'all' ); ?>/> <?php _e( 'Display all rows', 'tcp_csvl' ); ?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="upload_file" value=""><?php _e( 'file', 'tcp_csvl' ); ?>:</label>
		</th>
		<td>
			<input type="file" name="upload_file" id="upload_file" />
		</td>
	</tr>
	</tbody>
	</table>
	<span class="submit"><input type="submit" name="tcp_load_csv" id="tcp_load_csv" value="<?php _e( 'Load', 'tcp_csvl' ); ?>" style="button-secondary" /></span>
	<span class="description"><?php _e( 'This action helps to you to test if the file is right. Only the first four rows will be displayed.', 'tcp_csvl' ); ?></span>
</form>

<?php if ( is_array( $data ) && count( $data ) > 0 ) : ?>
<p><?php _e( 'These lines are the first four products loaded from the CSV file. If you think they are correct continue with the process.', 'tcp_csvl' ); ?></p>
<table class="widefat fixed" cellspacing="0">
	<?php if ( is_array( $titles ) && count( $titles ) > 0 ) : ?>
		<thead>
		<tr scope="col" class="manage-column"><th>&nbsp;</th>
		<?php foreach( $titles as $col ) : ?>
			<th><?php echo $col; ?></th>
		<?php endforeach; ?>
		</tr>
		</thead>
		<tfoot>
		<tr scope="col" class="manage-column"><th>&nbsp;</th>
		<?php foreach( $titles as $col ) : ?>
			<th><?php echo $col; ?></th>
		<?php endforeach; ?>
		</tr>
		</tfoot>
	<?php endif; ?>
		<tbody>
		<?php foreach( $data as $i => $cols ) :
			if ( $i > 4 && $display_rows == 'four' ) :
				break;
			else : ?>
				<tr>
					<td><?php echo $i; ?></td>
				<?php foreach( $cols as $col ) : ?>
					<td><?php echo $col; ?></td>
				<?php endforeach; ?>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<p><?php _e( 'Assign the columns of the CSV file (left column) to the fields of the products (right column).', 'tcp_csvl' ); ?></p>
<form method="post">
<input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />
<input type="hidden" name="taxonomy" value="<?php echo $taxonomy; ?>" />
<input type="hidden" name="separator" value="<?php echo isset( $_REQUEST['separator'] ) ? $_REQUEST['separator'] : ','; ?>" />
<input type="hidden" name="updated_products" value="<?php echo $updated_products; ?>" />
<?php if ( isset( $_REQUEST['titeled'] ) ) :?>
<input type="hidden" name="titeled" value="y"/>
<?php endif; ?>
<table class="widefat fixed" cellspacing="0">
<thead>
	<tr scope="col" class="manage-column">
		<th><?php _e( 'Imported columns', 'tcp_csvl' ); ?></th>
		<th><?php _e( 'TheCartPress columns', 'tcp_csvl' ); ?></th>
	</tr>
</thead>
<tfoot>
	<tr scope="col" class="manage-column">
		<th><?php _e( 'CSV columns', 'tcp_csvl' ); ?></th>
		<th><?php _e( 'TheCartPress columns', 'tcp_csvl' ); ?></th>
	</tr>
</tfoot>
<tbody>
<?php if ( is_array( $titles ) && count( $titles ) > 0 ) : ?>
	<?php foreach( $titles as $i => $col ) : ?>
		<tr>
			<td><?php echo $col; ?></td>
			<td>
			<select name="col_<?php echo $i; ?>[]" multiple="true" size="6" style="height: auto;">
				<?php $upper_col = strtoupper( $col );
				$options = array();
				$options[] = array( '', false, __( 'None', 'tcp_csvl' ) );
				$options[] = array( 'tcp_name', $upper_col == 'TITLE', 'Title (' . __( 'Title', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_content', $upper_col == 'CONTENT', 'Content (' . __( 'Content', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_excerpt', $upper_col == 'EXCERPT', 'Excerpt (' . __( 'Excerpt', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_price', $upper_col == 'PRICE', 'Price (' . __( 'Price', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_stock', $upper_col == 'STOCK', 'Stock (' . __( 'Stock', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_weight', $upper_col == 'WEIGHT', 'Weight (' . __( 'Weight', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_sku', $upper_col == 'SKU', 'SKU (' . __( 'SKU', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_order', $upper_col == 'ORDER', 'Order (' . __( 'Order', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_tax', $upper_col == 'TAX', 'Tax (' . __( 'Tax', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_upload', $upper_col == 'UPLOAD', 'Upload (' . __( 'Upload', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_attachment', $upper_col == 'ATTACHMENT', 'Attachment (' . __( 'Attachment', 'tcp_csvl' ) . ')' );
				$options[] = array( 'tcp_thumbnail', $upper_col == 'THUMBNAIL', 'Thumbnail (' . __( 'Thumbnail', 'tcp_csvl' ) . ')' );				
				$custom_fields_def = tcp_get_custom_fields_def( $post_type );
				if ( is_array( $custom_fields_def ) && count( $custom_fields_def ) > 0 )
					foreach( $custom_fields_def as $custom_field_def )
						$options[] = array( $custom_field_def['id'], $col == $custom_field_def['label'], 'Custom Fields: ' . $custom_field_def['label'] );
				foreach( get_object_taxonomies( $post_type ) as $taxmy ) {
					$tax = get_taxonomy( $taxmy );
					$options[] = array( 'tcp_tax_' . $taxmy, $col == $tax->labels->name, 'Taxonomy: ' . $tax->labels->name );
				}
				$options = apply_filters( 'tcp_csvl_option_columns', $options, $col );
				foreach( $options as $option ) : ?>
					<option value="<?php echo $option[0]; ?>" <?php selected( $option[1] ); ?>><?php echo $option[2]; ?></option>
				<?php endforeach; ?>
			</select>
			</td>
		</tr>
	<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<p>
	<label for="tcp_cat"><?php _e( 'Attach this products to the Taxonomy', 'tcp_csvl' )?>:</label>
	<select id="tcp_cat" name="tcp_cat">
		<option value=""><?php _e( 'No one', 'tcp_csvl' ); ?></option>
	<?php $terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
	if ( is_array( $terms ) && count( $terms ) ) foreach( $terms as $term ) : ?>
		<option value="<?php echo $term->term_id; ?>"<?php selected( $term->term_id, $cat ); ?>><?php echo esc_attr( $term->name ); ?></option>
	<?php endforeach; ?>
	</select>
</p>
<p>
	<label for="tcp_status"><?php _e( 'Set new products status to', 'tcp_csvl' )?>:</label>
	<select id="tcp_post_status" name="tcp_status">
		<option value="publish"><?php _e( 'publish', 'tcp_cvsl' ); ?></option>
		<option value="draft"><?php _e( 'draft', 'tcp_cvsl' ); ?></option>
	</select>
</p>
<span class="submit">
	<input type="submit" name="tcp_load_products_from_csv" id="tcp_load_products_from_csv" value="<?php _e( 'Upload', 'tcp_csvl' ); ?>" class="button-primary" />
	<span><?php _e( 'This action will load the products in the eCommerce. Be patient.', 'tcp_csvl' ); ?></span>
</span>
</form>
<?php endif; ?>
</div>
<?php
function tcp_csv_get_price( $source ) {
	$source = tcp_input_number( $source );
	return apply_filters( 'tcp_csv_get_price', $source );
	//$aux = str_replace( '$', '', $source );
	//return tcp_input_number( $aux );
}

function tcp_copy( $file1, $file2 ) {
	$contentx = @file_get_contents( $file1 );
	$openedfile = fopen( $file2, 'w' );
	fwrite( $openedfile, $contentx );
	fclose( $openedfile );
	if ( $contentx === FALSE ) return false;
	else return true;
}
?>