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

date_default_timezone_set('America/Managua');
$b = time();
$month = date("m", $b);
$monthspelled = date("M", $b);
$day = date("D", $b);
$numday = date("d", $b);
$year = date("Y", $b);
//connect info for pgsql.
$pgsql_hostname = 'pgsql.doap.internal';
//$pgsql_hostname = 'pgsql.doap.com';
$pgsql_username = 'shawn';
$pgsql_password ='fr1ck0ff';
$pgsql_db = 'hoy';
$pgsql_port = '5432';
$limit = '200';
$pic_search_string = '"</p></div></div>'; 
/*** pgsql connector ***/
try {
    $db = new PDO("pgsql:dbname=$pgsql_db;host=$pgsql_hostname", "$pgsql_username", "$pgsql_password" );
    }
catch(PDOException $e)
    {
    echo $e->getMessage();
    }
$authors_imported2 = $wpdb->get_col( "SELECT distinct post_id FROM $wpdb->postmeta where meta_key = 'autor' ORDER BY ABS(post_id) DESC;" );
$authors_imported= implode( ",", $authors_imported2);
$last_imported = $authors_imported2['0'];
if (empty($last_imported)) 
	{
	$last_imported = 0;
	}
if (empty($authors_imported))
	{
	$authors_imported = 0;
	}
print "\n Comenzando Importacion <br>\n\n" . PHP_EOL;
print "\n <br>Autores importado ya = $authors_imported<br>\n\n\n\n" . PHP_EOL;
print "\n <br># post del utlimo autor importado = $last_imported <br>\n\n\n\n" . PHP_EOL;

//foreach( $wpdb->get_results("SELECT b.post_id, b.meta_value FROM $wpdb->postmeta b INNER JOIN $wpdb->posts f on f.ID = b.post_id WHERE b.meta_key = 'idnoticia' and b.post_id IN (SELECT ID FROM $wpdb->posts where ID > $last_imported and post_type = 'post' and post_status = 'publish' and ID NOT IN ($authors_imported)) ORDER BY ABS(b.post_id) ASC LIMIT $limit;") as $key => $row)
foreach( $wpdb->get_results("SELECT b.post_id, b.meta_value FROM $wpdb->postmeta b INNER JOIN $wpdb->posts f on f.ID = b.post_id WHERE b.meta_key = 'idnoticia' and b.post_id IN (SELECT ID FROM $wpdb->posts where ID > $last_imported and post_type = 'post' and post_status = 'publish' and ID NOT IN ($authors_imported)) ORDER BY ABS(b.post_id) ASC LIMIT $limit;") as $key => $row)
{

        $post_id = $row->post_id;
        $idnoticia= $row->meta_value;

//print "\n post_id= $post_id<br>\n\n" . PHP_EOL;
//print "\n idnoticia = $idnoticia<br>\n\n" . PHP_EOL;

try     {
        $dbautor = new PDO("pgsql:host=$pgsql_hostname;dbname=hoy", $pgsql_username, $pgsql_password);

	$sqlautor = "select distinct autor.autor from autor, credito WHERE autor.idautor = credito.idautor and credito.idnoticia =$idnoticia limit $limit";

        foreach ($dbautor->query($sqlautor) as $row)
                {
			$articleauthor= $row['autor'];
			if(!empty($articleauthor))
			{
				$author_results = add_post_meta( $post_id, 'autor', $articleauthor, false );
				print "\n idnoticia = $idnoticia and post_id = $post_id and autor = $articleauthor<br>\n$author_results\n" . PHP_EOL;
			}
		}
	        $dbautor = null;
        }
        catch(PDOException $e)
        {
        }
}

$intros_imported2 = $wpdb->get_col( "SELECT distinct post_id FROM $wpdb->postmeta where meta_key = 'intro' ORDER BY ABS(post_id) DESC;" );
$intros_imported= implode( ",", $intros_imported2);
$last_intro_imported = $intros_imported2['0'];
if (empty($last_intro_imported)) 
	{
	$last_intro_imported = 0;
	}
if (empty($intros_imported))
	{
	$intros_imported = 0;
	}

print "\n <br>Intros importado ya = $intros_imported<br>\n\n\n\n" . PHP_EOL;
print "\n <br># post del utlimo autor importado = $last_intro_imported <br>\n\n\n\n" . PHP_EOL;

foreach( $wpdb->get_results("SELECT b.post_id, b.meta_value FROM $wpdb->postmeta b INNER JOIN $wpdb->posts f on f.ID = b.post_id WHERE b.meta_key = 'idnoticia' and b.post_id IN (SELECT ID FROM $wpdb->posts where ID > $last_intro_imported and post_type = 'post' and post_status = 'publish' and ID NOT IN ($intros_imported)) ORDER BY ABS(b.post_id) ASC LIMIT $limit;") as $key => $introrow)
{
$k++;
        $post_id = $introrow->post_id;
        $idnoticia= $introrow->meta_value;

//print "\n post_id= $post_id<br>\n\n" . PHP_EOL;
//print "\n idnoticia = $idnoticia<br>\n\n" . PHP_EOL;
/* Need to query for Author(s) info and add to postmeta */
try     {
        $dbintro= new PDO("pgsql:dbname=$pgsql_db;host=$pgsql_hostname", "$pgsql_username", "$pgsql_password" );

	$sqlintro = "select intro from noticia WHERE idnoticia = $idnoticia limit 1;";

        foreach ($dbintro->query($sqlintro) as $row)
                {
			$articleintro= $row['intro'];
			//print "\n intro = $articleintro <br>\n\n" . PHP_EOL;
			if(!empty($articleintro))
			{
				$intro_results = add_post_meta( $post_id, 'intro', $articleintro, true );
				print PHP_EOL . "\n idnoticia = $idnoticia and post_id = $post_id and intro = $articleintro <br>\n" . PHP_EOL . "intro_meta_id = $intro_results\n" . PHP_EOL;
			}
		}
	        $dbintro= null;
        }
        catch(PDOException $e)
        {
        }
print $k;
}




print "\n Importacion Completa! \n\n";
get_footer();
//echo '<meta http-equiv="refresh" content="60;http://hoy.doap.com/hoy-import.php" />';
echo '<meta http-equiv="refresh" content="30;http://hoy.doap.com/wp-admin/one-time-hoy-changes.php" />';
echo '<meta http-equiv="refresh" content="30"/>';
//<meta http-equiv="refresh" content="30=http://hoy.doap.com/wp-admin/one-time-hoy-changes.php"/>
?>
