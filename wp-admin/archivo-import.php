<?php
define('WP_USE_THEMES', true);
/** Loads the WordPress Environment and Template */
//require_once( '/var/www/html/wp-blog-header.php' );
require_once( '/var/www/html/wp-load.php' );
require_once('/var/www/html/wp-config.php');
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
require_once( '/var/www/html/wp-admin/includes/image.php' );
switch_to_blog('55');
//get_header();
function substring_index($subject, $delim, $count){
    if($count < 0){
        return implode($delim, array_slice(explode($delim, $subject), $count));
    }else{
        return implode($delim, array_slice(explode($delim, $subject), 0, $count));
    }
}

function stripAccents($stripAccents){
  return strtr($stripAccents,'àáâãäçéèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÍÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeeiiiinooooouuuuyyAAAAACEEEEIIIIINOOOOOUUUUY');
}
//date stuff
date_default_timezone_set('America/Managua');
$b = time();
$month = date("m", $b);
$monthspelled = date("M", $b);
$day = date("D", $b);
$numday = date("d", $b);
$year = date("Y", $b);

$pic_search_string = '"</p></div></div>'; 
$s3_destination_base = "s3://s3.laprensa.com.ni";
$section=array("deportes"=>"7","sucesos"=>"6");

$articles_imported2 = $wpdb->get_col( "SELECT distinct meta_value FROM $wpdb->postmeta where meta_key = 'lparchivo' ORDER BY meta_value ASC;" );
$metavals4= implode( ",", $articles_imported2);
$last_imported = $articles_imported2['0'];
print "\n Comenzando Importacion <br>\n\n" . PHP_EOL;
print "\n <br>articulos importado ya = $metavals4 <br>\n\n\n\n" . PHP_EOL;
print "\n <br>ultimo articulo importado = $last_imported <br>\n\n\n\n" . PHP_EOL;

		date_default_timezone_set('America/Managua');
		$articlesection= $section[$dir_name];
		$post_status = 'publish';

		$post = array(
		  'comment_status' => 'closed',
		  'ping_status'    => 'open', // 'closed' means pingbacks or trackbacks turned off
		  'post_author'    => '262',    //The user ID number of the author.
		  'post_content'   => $cat_post_text, //The full text of the post.
		  'post_date'      => $creacion, //The time post was made.
		  'post_date_gmt'  => $creacion, //The time post was made, in GMT.
		//  'post_excerpt'   => $articleexerpt, //For all your post excerpt needs.
		//  'post_name'      => $permalink, // The name (slug) for your post
		//  'post_parent'    => [ <post ID> ] //Sets the parent of the new post.
		  'post_status'    => $post_status,    //Set the status of the new post.
		//  'post_title'     => $permanoticia, //The title of your post.
	//	  'post_category'     => array( '1', '$articlesection', '3', '4'),  //The title of your post.
		  'post_type'      => 'post' //You may want to insert a regular post, page, link, a menu item or some custom post type
		//  'tags_input'     => $articletags //For tags.
		 // 'tax_input'      => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
		);  

		$article_exists = $wpdb->get_var( "SELECT * FROM $wpdb->postmeta where meta_key = 'lparchivo' and meta_value = $catfileinfo limit 1;" );

		if( $article_exists == null ) {
		//if( 1 == 1 ) {
		$post_id = wp_insert_post($post);
		print "\n post creado - # = $post_id \n\n";
		$post_cat = $articlesection;
		$post_term_results = wp_set_post_terms( $post_id, $post_cat, 'category' );
		//$post_term_results = wp_set_post_terms( $post_id, $articletags );

/* This is where we're going to add custom fields to postmeta */

		$migrated_lparchivo = add_post_meta( $post_id, 'lparchivo', $catfileinfo, true );

			$image_exists = -1;
			$args = array( 'post_type' => 'attachment', 'post_parent' => $post_id );
			$attachments = get_posts( $args );
			if ( $attachments ) 
				{
				foreach ( $attachments as $attachment_post ) 
					{
				if ($img_title == $attachment_post->post_title)
						{
						$image_exists = 1;
						}
					}
				}
			if ( $image_exists == -1 )
			{
				$time = $year . "/" . $nummonth;
				$wp_upload_dir = wp_upload_dir($time);
				$file = $cat_pic_file;
				$image_data = file_get_contents($file);
				$filename = basename($cat_post_pic);
				if(wp_mkdir_p($upload_dir['path']))
				    $file = $upload_dir['path'] . '/' . $filename;
				else
				    $file = $upload_dir['basedir'] . '/' . $filename;
				file_put_contents($file, $image_data);
	
				//create post attachment
				$wp_filetype = wp_check_filetype($filename, null);
				$img_title = substring_index ($cat_post_pic, '.', 1);	
				$img_title = preg_replace('/\.[^.]+$/', '', $img_title);
				//$get_img_title_var = get_page_by_title( $img_title);
				//$get_img_title_var = wp_get_attachment_link( '', '' , $img_title, false, false );  
				//$get_pic_title_var = get_page_by_title( $pic);
	
				$attachment = array(
					'guid' => $upload_dir['url'] . '/' . $pic, 
		  			'post_author'    => '262',    //The user ID number of the author.
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => $img_title, 
					'post_content' => '',
					'post_status' => 'inherit'
				);
			
	
				$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
				print "\n attachment creado - # $attach_id \n\n";
				if ( $attach_id )
					{
					$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
					$migrated_idimagen = add_post_meta( $attach_id, 'lparchivo_img', $cat_post_pic, false );
					$migrated_idimagen = add_post_meta( $post_id, 'lparchivo_img', $cat_post_pic, false );
	
					wp_update_attachment_metadata( $attach_id, $attach_data );
					set_post_thumbnail( $post_id, $attach_id );
						
						
$attachment_guid_array = $wpdb->get_col( "SELECT guid FROM $wpdb->posts where ID = $attach_id LIMIT 1;" );
$attachment_guid = $attachment_guid_array['0'];
$attachment_site = substring_index($attachment_guid, '/wp-content', 1);
$attachment_file = substring_index($attachment_guid, '/', -1);
$sourcefiles = substring_index($attachment_guid, $attachment_site, -1 );
$sourcefiles = substring_index($sourcefiles, '.' , 1 );
$sourcefiles = '/var/www/html' . $sourcefiles . '*';
$destination_path = substring_index($attachment_guid, $attachment_site, -1 );
$destination_path = substring_index($destination_path, $attachment_file, 1 );
$destination_path = $s3_destination_base . $destination_path;
$s3command = '/usr/bin/s3cmd put --config=/var/www/html/.s3cfg --no-check-md5 --skip-existing --no-encrypt -P -H --recursive --add-header="Expires:`date -u +"%a, %d %b %Y %H:%M:%S GMT" --date "next Year"`" --add-header="Cache-Control:max-age=31536000, public" ' . $sourcefiles . ' ' . $destination_path;
$s3result = exec($s3command);
print PHP_EOL . $s3result . PHP_EOL;


					$update_post = array(
					  'ID'             => $post_id, 
					  'post_content'   => $cat_post_text, //The full text of the post.
				//	  'post_exerpt'    => $articleexerpt, //The exerpt of the post.
					  'post_date'      => $creacion, //The time post was made.
					  'post_date_gmt'  => $creacion,
					  'post_status'    => $post_status ////The time post was made, in GMT.
					);  

					$update_post_results = wp_update_post($update_post);
					}

?>
