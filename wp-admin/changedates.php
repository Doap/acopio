<?php
define('WP_USE_THEMES', true);
/** Loads the WordPress Environment and Template */
//require_once( '/var/www/html/wp-blog-header.php' );
require_once( '/var/www/html/wp-load.php' );
require_once("/var/www/html/wp-config.php");
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();

require_once( '/var/www/html/wp-admin/includes/image.php' );
switch_to_blog('51');
get_header();
$today = date("Y-m-d");
$yesterday= date("Y-m-d",strtotime("-1 day",strtotime($today)));
print "\n\n";
print $today;

$last_article_yesterday_array = $wpdb->get_col( "SELECT ID FROM $wpdb->posts where post_type = 'post' and post_date like '$yesterday%' ORDER BY ID DESC LIMIT 1;" );
$last_article_yesterday = $last_article_yesterday_array['0'];
print "\n\n";
print $last_article_yesterday;
$posts_with_thumbs = $wpdb->get_col( "select post_id from $wpdb->postmeta where post_id > $last_article_yesterday and meta_key = '_thumbnail_id' order by post_id DESC LIMIT 200;");
$posts_with_thumbs_var = implode( ",", $posts_with_thumbs); 

print "\n\n";
print $posts_with_thumbs_var;
foreach( $wpdb->get_results("select * from $wpdb->posts where post_type = 'post' and ID > $last_article_yesterday and ID NOT IN ($posts_with_thumbs_var) ORDER BY ID ASC LIMIT 200;") as $key => $row)
{

	$post_id = $row->ID;
	$creacion = $row->post_date;
	$no_pic_post_date = date("Y-m-d H:i:s",strtotime("-5 minutes",strtotime($creacion)));
	$update_post = array(
	  'ID'             => $post_id,
	  'post_date'      => $no_pic_post_date, //The time post was made.
	  'post_date_gmt'  => $no_pic_post_date,
	  'post_status'    => 'publish' ////The time post was made, in GMT.
	);

print "\n\n";
print $post_id;
print "\n\n";
print $creacion;
print "\n\n";
print $no_pic_post_date;
	$update_post_results = wp_update_post($update_post);

	$remove_article_from_ultima_hora_result = $wpdb->get_results("DELETE from $wpdb->term_relationships where object_id = $post_id and term_taxonomy_id = 1 LIMIT 1;");
}
get_footer();
?>
