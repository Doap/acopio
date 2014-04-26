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

print "\n\n today";
print $today;

$last_article_yesterday_array = $wpdb->get_col( "SELECT ID FROM $wpdb->posts where post_type = 'post' and post_date like '$yesterday%' ORDER BY ID DESC LIMIT 1;" );
$last_article_yesterday = $last_article_yesterday_array['0'];

print "\n\n last article yesterday";
print $last_article_yesterday;

$posts_with_thumbs = $wpdb->get_col( "select post_id from $wpdb->postmeta where post_id > $last_article_yesterday and meta_key = '_thumbnail_id' order by post_id DESC LIMIT 200;");
$posts_with_thumbs_var = implode( ",", $posts_with_thumbs); 

print "\n\n posts with thumbs";
print $posts_with_thumbs_var;

$noticia_articles = $wpdb->get_col("select object_id from $wpdb->term_relationships where term_taxonomy_id = 15 and object_id > $last_article_yesterday and object_id IN ($posts_with_thumbs_var) ORDER BY object_id DESC LIMIT 5;");
$mundo_articles = $wpdb->get_col("select object_id from $wpdb->term_relationships where term_taxonomy_id = 11 and object_id > $last_article_yesterday and object_id IN ($posts_with_thumbs_var) ORDER BY object_id DESC LIMIT 5;");
$deportes_articles = $wpdb->get_col("select object_id from $wpdb->term_relationships where term_taxonomy_id = 14 and object_id > $last_article_yesterday and object_id IN ($posts_with_thumbs_var) ORDER BY object_id DESC LIMIT 5;");
$estrellas_articles = $wpdb->get_col("select object_id from $wpdb->term_relationships where term_taxonomy_id = 16 and object_id > $last_article_yesterday and object_id IN ($posts_with_thumbs_var) ORDER BY object_id DESC LIMIT 5;");
$noticia_post_id = $noticia_articles['1']; 
$mundo_post_id = $mundo_articles['1']; 
$deportes_post_id = $deportes_articles['1'];
$estrellas_post_id = $estrellas_articles['1'];
$noticia_term_results = wp_set_post_terms( $notica_post_id, 1, 'category', 1 );
$mundo_term_results = wp_set_post_terms( $mundo_post_id, 1, 'category', 1 );
$deportes_term_results = wp_set_post_terms( $deportes_post_id, 1, 'category', 1 );
$estrellas_term_results = wp_set_post_terms( $estrellas_post_id, 1, 'category', 1 );

print "\n\n noticia_post_id";
print $noticia_post_id;

print "\n\n noticia_term_results ";
print $noticia_term_results ;

print "\n\n mundo_post_id";
print $_post_id;

print "\n\n mundo_term_results ";
print $mundo_term_results ;

print "\n\n deportes_post_id";
print $deportes_post_id;

print "\n\n deportes_term_results ";
print $deportes_term_results ;

print "\n\n estrellas_post_id";
print $estrellas_post_id;

print "\n\n estrellas_term_results ";
print $estrellas_term_results ;
get_footer();
?>
