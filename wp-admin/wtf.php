<?php
define('WP_USE_THEMES', true);
/** Loads the WordPress Environment and Template */
//require_once( '/var/www/html/wp-blog-header.php' );
require_once( '/var/www/html/wp-load.php' );
require_once('/var/www/html/wp-config.php');
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
require_once( '/var/www/html/wp-admin/includes/image.php' );
switch_to_blog('51');
get_header();
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
$lastBuildDate = $day . ', ' . $numday . ' ' . $monthspelled . ' ' . $year . ' 00:00:00 +0600';
//$idedicion = '2013-07-28';
$lastBuildDate = 'Wed, 02 Oct 2002 15:00:00 +0200';
//if (!isset($_GET['seccion'])) { $seccion = '*'; } else { $seccion = $_GET['seccion']; }
if (isset($_GET['seccion']) && ($_GET['seccion'] || '')) { $seccion = $_GET['seccion']; }
if (isset($_GET['edicion'])) { $idedicion = $_GET['edicion']; } else { $idedicion = date("Y-m-d"); }

					
$attach_id = 178834;	
						
$attachment_guid_array = $wpdb->get_col( "SELECT guid FROM $wpdb->posts where ID = $attach_id LIMIT 1;" );
$attachment_guid = $attachment_guid_array['0'];
$attachment_site = substring_index($attachment_guid, '/wp-content', 1);
$attachment_file = substring_index($attachment_guid, '/', -1);
$sourcefiles = substring_index($attachment_guid, $attachment_site, -1 );
$sourcefiles = substring_index($sourcefiles, '.' , 1 );
$sourcefiles = '/var/www/html' . $sourcefiles . '*';
$destination_path = substring_index($attachment_guid, $attachment_site, -1 );
$destination_path = substring_index($destination_path, $attachment_file, 1 );
$destination_path = 's3://s3.hoy.com.ni' . $destination_path;
$s3command = '/usr/bin/s3cmd put --config=/var/www/html/.s3cfg --no-check-md5 --skip-existing --no-encrypt -P -H --recursive --add-header="Expires:`date -u +"%a, %d %b %Y %H:%M:%S GMT" --date "next Year"`" --add-header="Cache-Control:max-age=31536000, public" "' . $sourcefiles . '" ' . $destination_path;
//$s3result = exec($s3command);
print PHP_EOL . $s3command . PHP_EOL;
get_footer();
//echo '<meta http-equiv="refresh" content="60;http://hoy.doap.com/hoy-import.php" />';
//<meta http-equiv="refresh" content="30=http://hoy.doap.com/hoy-import.php">
?>
