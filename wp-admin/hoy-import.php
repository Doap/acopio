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
//echo "davewashere";
//$articles_imported = $wpdb->get_results( "SELECT meta_value FROM $wpdb->postmeta where meta_key = 'idnoticia' ORDER BY meta_value ASC;", ARRAY_N );
//$articles_imported2 = $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta where meta_key = 'idnoticia' ORDER BY ABS(meta_value) DESC LIMIT 200;" );
$articles_imported2 = $wpdb->get_col( "SELECT distinct meta_value FROM $wpdb->postmeta where meta_key = 'idnoticia' ORDER BY ABS(meta_value) DESC;" );

//$metavals3= implode( ",", $articles_imported);
$metavals4= implode( ",", $articles_imported2);
$last_imported = $articles_imported2['0'];
//printf( $articles_imported, "\n" );
//printf( $articles_imported2, "\n" );

//$metavals=`/usr/sbin/metavals.sh`; 
//$metavals2= implode( ",", $metavals );
//printf( $metavals, "\n" );
//printf( $metavals2, "\n" );
//printf( $metavals3, "\n" );
print "\n Comenzando Importacion <br>\n\n" . PHP_EOL;
print "\n <br>articulos importado ya = $metavals4 <br>\n\n\n\n" . PHP_EOL;
print "\n <br>ultimo articulo importado = $last_imported <br>\n\n\n\n" . PHP_EOL;

try	{
	$dbp = new PDO("pgsql:host=$pgsql_hostname;dbname=hoy", $pgsql_username, $pgsql_password);
	//if (isset($_GET['seccion'])) { 
	//$sql = "SELECT noticia.idnoticia, seccion.seccion, noticia.creacion as noticiacreacion, edicion.creacion, noticia.hora, noticia.ultimahora, seccion.seccion, noticia.claves, edicion.portada, edicion, noticia, uri, noticia.resumen, noticia.texto FROM noticia LEFT JOIN seccion USING(idseccion) LEFT JOIN edicion USING(idedicion) LEFT JOIN rating USING (idnoticia) WHERE (leido IS NOT NULL OR leido <> 0) AND idseccion = (SELECT idseccion FROM seccion WHERE seccion = '$seccion' ORDER BY idseccion LIMIT 1) ORDER BY idnoticia DESC LIMIT 5";
	//} else {
	//$sql = "select  noticia.idnoticia, noticia.creacion, seccion.seccion, seccion.idseccion, noticia.noticia, noticia.resumen, noticia.texto, noticia.claves from noticia, seccion WHERE seccion.idseccion = noticia.idseccion AND noticia.noticia <> 'Titular por asignar' AND noticia.noticia <> 'ocotal' and noticia.creacion < '2012-05-30' and noticia.idnoticia NOT IN $metavals4 ORDER BY noticia.idnoticia asc limit 75"; 
	if ($metavals4)
		{ 
		$sql = "select  noticia.idnoticia, noticia.creacion, seccion.seccion, seccion.idseccion, noticia.noticia, noticia.resumen, noticia.texto, noticia.ultimahora, noticia.claves, noticia.intro from noticia, seccion WHERE seccion.idseccion = noticia.idseccion and noticia.idnoticia > $last_imported and noticia.idnoticia NOT IN ($metavals4) ORDER BY noticia.idnoticia asc limit $limit"; 
		//$sql = "select  noticia.idnoticia, noticia.creacion, seccion.seccion, seccion.idseccion, noticia.noticia, noticia.resumen, noticia.texto, noticia.claves from noticia, seccion WHERE seccion.idseccion = noticia.idseccion and noticia.idnoticia > $last_imported and noticia.idnoticia NOT IN ($metavals4) ORDER BY noticia.idnoticia asc limit $limit"; 
		//$sql = "select  noticia.idnoticia, noticia.creacion, seccion.seccion, seccion.idseccion, noticia.noticia, noticia.resumen, noticia.texto, noticia.claves from noticia, seccion WHERE seccion.idseccion = noticia.idseccion and noticia.noticia = 'Titular por asignar' and noticia.idnoticia < 21000 and noticia.idnoticia NOT IN ($metavals4) ORDER BY noticia.idnoticia asc limit $limit"; 
		//$sql = "select  noticia.idnoticia, noticia.creacion, seccion.seccion, seccion.idseccion, noticia.noticia, noticia.resumen, noticia.texto, noticia.claves from noticia, seccion WHERE seccion.idseccion = noticia.idseccion AND noticia.noticia <> 'Titular por asignar' and noticia.idnoticia > $last_imported and  noticia.idnoticia NOT IN ($metavals4) ORDER BY noticia.idnoticia asc limit $limit"; 
		}
	else
		{
		$sql = "select  noticia.idnoticia, noticia.creacion, seccion.seccion, seccion.idseccion, noticia.noticia, noticia.resumen, noticia.texto, noticia.claves, noticia.ultimahora, noticia.intro from noticia, seccion WHERE seccion.idseccion = noticia.idseccion ORDER BY noticia.idnoticia asc limit $limit"; 
		}

//print "\n $metavals4 \n";
//print "\n $sql \n";
//print "\n hello \n";

    	foreach ($dbp->query($sql) as $row)
        	{
		date_default_timezone_set('America/Managua');
		$string = $row['creacion'];
		$crdate = $string;
		$pos = strrpos( $string, '.');
		if ($pos !== false) {
		    $creacion = substr($string, 0, $pos );
		}
		else  {
		    $creacion = $string;
		}
		$idnoticia = $row['idnoticia'];
		$articledate = $creacion;
		$articledate = strtotime($articledate);
		$articlemonth = date("m", $articledate);
		$articleyear = date("Y", $articledate);
		$articleday = date("d", $articledate);
		$string = strtotime($creacion);
		$x=date("r", $string);
		$articletext= $row['texto'];
		$articleexerpt= $row['resumen'];
		$articlesection= $row['idseccion'];
		$articletags= $row['claves'];
		$articleultimahora = $row['ultimahora'];
		$articleintro= $row['intro'];

		if ( $articleultimahora == 'TRUE' )
		{
			$articlesection = '1,' . $articlesection;
		}

		//$articletext_clean= substring_index($articletext,  $pic_search_string, -1 );
		//if (isset($articletags) d$&& ($_GET['seccion'] || '')) { $seccion = $_GET['seccion']; }


print "\n idnoticia = $idnoticia \n\n";
print "\n creacion = $creacion \n\n";
print "\n article section = $articlesection \n\n";
//print "\n article text = $articletext \n\n";
print "\n article tags = $articletags \n\n";

		$permanoticia = $row['noticia'];
	$post_status = 'publish';
	if ($permanoticia == 'Titular por asignar' ) $permanoticia = $articleexerpt;
	if (strpos($articletext,'uerpo de la nota. Por completar') === 1) $post_status = 'draft';
	if ( strlen($articleexerpt) == 1) {
		$permanoticia = $articleexerpt . $permanoticia;
		$articleexerpt = '';
		$articletext= $articleexerpt . $articletext;
	}
		$permanoticia = ucfirst($permanoticia);


		//$permanoaccent = stripAccents($permanoticia);
		//$permalink = str_replace(' ','-',(strtolower(preg_replace("/[^A-Za-z0-9 ]/", '', $permanoaccent))));
		//if (strlen($permalink) > 60 ) {
		//$permalink=substr($permalink,0,60);
		//$permalink = substr($permalink, 0, strrpos( $permalink, '-') );
		//}
		$permalink = sanitize_title_with_dashes($permanoticia);
		if (strlen($permanoticia) > 60 ) {
		$permanoticia=substr($permanoticia,0,60);
		$permanoticia= substr($permanoticia, 0, strrpos( $permanoticia, ' ') );
		$permalink = sanitize_title_with_dashes($permanoticia);
		$permanoticia= $permanoticia . '...';
		}

		$post = array(
		  'comment_status' => 'open',
		  'ping_status'    => 'open', // 'closed' means pingbacks or trackbacks turned off
		  'post_author'    => '242',    //The user ID number of the author.
		  'post_content'   => $articletext, //The full text of the post.
		  'post_date'      => $creacion, //The time post was made.
		  'post_date_gmt'  => $creacion, //The time post was made, in GMT.
		  'post_excerpt'   => $articleexerpt, //For all your post excerpt needs.
		  'post_name'      => $permalink, // The name (slug) for your post
		//  'post_parent'    => [ <post ID> ] //Sets the parent of the new post.
		  'post_status'    => $post_status,    //Set the status of the new post.
		  'post_title'     => $permanoticia, //The title of your post.
	//	  'post_category'     => array( '1', '$articlesection', '3', '4'),  //The title of your post.
		  'post_type'      => 'post' //You may want to insert a regular post, page, link, a menu item or some custom post type
		//  'tags_input'     => $articletags //For tags.
		 // 'tax_input'      => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
		);  
//		$article_does_not_exist = 1;
//		$args = array( 'post_type' => 'post', 'post_title' => $permanoticia );
//
//		$articles = get_posts( $args );
//		if ( $articles ) 
//			{
//			foreach ( $articles as $article ) 
//				{
//			if ($creacion== $article->post_date)
//					{
//					$article_does_not_exist = -1;
//					}
//				}
//			}
//		if( $article_does_not_exist == 1 ) {



//		$article_does_not_exist = 1;
		//$args = array( 'post_type' => 'post', 'post_title' => $permanoticia );



		$article_exists = $wpdb->get_var( "SELECT * FROM $wpdb->postmeta where meta_key = 'idnoticia' and meta_value = $idnoticia limit 1;" );
//		printf( "article_exists = ", $article_exists, "\n" );

//		$article_exists = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta where meta_key = 'idnoticia';" );
//		printf( "article_exists = ", $article_exists, "\n" );

		if( $article_exists == null ) {
		//if( 1 == 1 ) {
		$post_id = wp_insert_post($post);
		print "\n post creado - # = $post_id \n\n";
		$post_cat = $articlesection;
		$post_term_results = wp_set_post_terms( $post_id, $post_cat, 'category' );
		$post_term_results = wp_set_post_terms( $post_id, $articletags );

/* This is where we're going to add custom fields to postmeta */

		$migrated_idnoticia = add_post_meta( $post_id, 'idnoticia', $idnoticia, true );
		if(!empty($articleintro))
		{
			$intro_results = add_post_meta( $post_id, 'intro', $articleintro, true );
		}
/* Need to query for Author(s) info and add to postmeta */
try     {
        $dbautor = new PDO("pgsql:host=$pgsql_hostname;dbname=hoy", $pgsql_username, $pgsql_password);

	$sqlautor = "select distinct autor.autor from autor, credito WHERE autor.idautor = credito.idautor and credito.idnoticia =$idnoticia limit $limit";

        foreach ($dbautor->query($sqlautor) as $row)
                {
			$articleauthor= $row['autor'];
			if(!empty($articleauthor))
			{
				$author_results = add_post_meta( $post_id, 'autor', $articleauthor, false );
			}
		}
	        $dbautor = null;
        }
        catch(PDOException $e)
        {
        }


		//1. get count of all image-na class in content
		$oldimagecount = substr_count($articletext, 'na-media');
		if ( $oldimagecount == 0 )
		{
			$article_has_no_image = 1;
		}
		else
		{
			$article_has_no_image = 0;
		}
		$oldimgcounter = $oldimagecount; 
		$image_replaced_text = $articletext;
		//2. start loop (get image ids)
		$i=1;
		while($i<=$oldimagecount)
			{
			$articleimgid = substring_index(substring_index($image_replaced_text,  ' image-', -$oldimgcounter ) ,  '"', 1 );
			try 	{
				$dbp3 = new PDO("pgsql:host=$pgsql_hostname;dbname=hoy", $pgsql_username, $pgsql_password);
				$sql3 = "SELECT imagen FROM imagen where idimagen = $articleimgid limit 1;";
				foreach ($dbp3->query($sql3) as $row3)
				        {
					 $pic = $row3['imagen'];
					}
				$dbp3 = null;
				}
				catch(PDOException $e)
				    {
				    }
			$pic_orig = $pic;
			$pic = str_replace(' ', '-', $pic);


			if ($articleimgid)
{

			$div_start = '<div class="na-media na';
			$div_end= '</div>';
			$div_pos_start = strpos($image_replaced_text, $div_start);
			$div_pos_end_1 = strpos($image_replaced_text, $div_end, $div_pos_start);
			$div_pos_end_2 = strpos($image_replaced_text, $div_end, ($div_pos_end_1 + 1 ));
			$div_pos_end = $div_pos_end_2 - $div_pos_start + strlen($div_end);
			$div = substr( $image_replaced_text, $div_pos_start, $div_pos_end );
			$div_url = substring_index(substring_index($div, 'src="', -1 ) ,  '"', 1 );
			$div_info= substring_index(substring_index($div, '<div class="info"><p>', -1 ) ,  '</p></div></div>', 1 );
			$count = 1;
			$image_replaced_text = str_replace($div, "", $image_replaced_text, $count);

			//$image_replaced_text= substring_index($image_replaced_text,  $pic_search_string, -$oldimgcounter );
			$oldimgcounter--;
			$upload_dir = wp_upload_dir($creacion);
			$imageurl = 'http://tbp-hoy.doap.com' . '/' . $pic; 
			$img_title = substring_index ($pic, '.', 1);	
			$img_title = preg_replace('/\.[^.]+$/', '', $img_title);

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
				$file = $imageurl;
				$file_headers = @get_headers($file);
				if($file_headers[0] == 'HTTP/1.1 404 Not Found')
				{
					$exists = false;
					$imageurl = 'http://hoy.doap.com/wp-content/uploads/sites/51/' . $articleyear . '/' . $articlemonth . '/' . $pic;
					$file = $imageurl;
					$file_headers = @get_headers($file);
					if($file_headers[0] == 'HTTP/1.1 404 Not Found')
					{
						$exists = false;
						$imageurl = 'http://hoy.doap.com/wp-content/uploads/sites/51/' . $articleyear . '/' . $articlemonth . '/1024x1024_' . $pic;
						$file = $imageurl;
						$file_headers = @get_headers($file);
						if($file_headers[0] == 'HTTP/1.1 404 Not Found')
						{
							$file = $div_url;
							$file_headers = @get_headers($file);
							if($file_headers[0] == 'HTTP/1.1 404 Not Found')
							{
								$imageurl = 'http://hoy.doap.com/wp-content/uploads/sites/51/2013/07/laprensa-logohoy.png';
							}
							else
							{
								$imageurl = $div_url;
							}
						}
					}
				}
				$file = $imageurl;
				$image_data = file_get_contents($imageurl);
				//$filename = basename($image_url);
				$filename = $pic;
				if(wp_mkdir_p($upload_dir['path']))
				    $file = $upload_dir['path'] . '/' . $filename;
				else
				    $file = $upload_dir['basedir'] . '/' . $filename;
				file_put_contents($file, $image_data);
	
				//$vardata = $pic . ' = pic ' . $imageurl . ' = imageurl ' . $filename . ' = filename ' . $file . ' = file ' . $creacion . ' = creacion ' . $crdate . ' = crdate ' . $x . ' = idnoticia ' . $articletags . ' = articletags ' . $articlesection . ' = articlesection' . $permalink . ' = permalink '. $articleimgid. ' = articleimgid ';
	
	
				//create post attachment
				$wp_filetype = wp_check_filetype($filename, null);
				//$wp_upload_dir = wp_upload_dir();
				$img_title = substring_index ($pic, '.', 1);	
				$img_title = preg_replace('/\.[^.]+$/', '', $img_title);
				//$get_img_title_var = get_page_by_title( $img_title);
				//$get_img_title_var = wp_get_attachment_link( '', '' , $img_title, false, false );  
				//$get_pic_title_var = get_page_by_title( $pic);
				$vardata = $artciles_imported . ' = articles_imported ' . $artciles_imported2 . ' (2) ' . $i . ' = i ' . $image_exists. ' = 1 if image exists ' . $get_img_title_var . ' = img_title_var ' . $get_pic_title_var . ' = pictitle ' . $pic . ' = pic variable    ' . $img_title . ' = image title   what follows is image_replaced_text variable :' .  $image_replaced_text;
	
				$attachment = array(
					'guid' => $upload_dir['url'] . '/' . $pic, 
		  			'post_author'    => '242',    //The user ID number of the author.
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
					$migrated_idimagen = add_post_meta( $attach_id, 'idimagen', $articleimgid, false );
					$migrated_idimagen = add_post_meta( $post_id, 'idimagen', $articleimgid, false );
	
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
$destination_path = 's3://s3.hoy.com.ni' . $destination_path;
$s3command = '/usr/bin/s3cmd put --config=/var/www/html/.s3cfg --no-check-md5 --skip-existing --no-encrypt -P -H --recursive --add-header="Expires:`date -u +"%a, %d %b %Y %H:%M:%S GMT" --date "next Year"`" --add-header="Cache-Control:max-age=31536000, public" ' . $sourcefiles . ' ' . $destination_path;
$s3result = exec($s3command);
print PHP_EOL . $s3result . PHP_EOL;

	if (strlen($div_info) > 3 && strlen($articleexerpt) < 5)
	{
	$articleexerpt = $div_info;
	}

					$update_post = array(
					  'ID'             => $post_id, 
					  'post_content'   => $image_replaced_text, //The full text of the post.
					  'post_exerpt'    => $articleexerpt, //The exerpt of the post.
					  'post_date'      => $creacion, //The time post was made.
					  'post_date_gmt'  => $creacion,
					  'post_status'    => $post_status ////The time post was made, in GMT.
					);  

					$update_post_results = wp_update_post($update_post);
					}


			}
			$i++;


	if ($i == 200) break;
}
if ( $article_has_no_image )
{

$no_pic_post_date = date("Y-m-d H:i:s",strtotime("-15 minutes",strtotime($creacion)));

					$update_post = array(
					  'ID'             => $post_id, 
					  'post_date'      => $no_pic_post_date, //The time post was made.
					  'post_date_gmt'  => $no_pic_post_date,
					  'post_status'    => $post_status ////The time post was made, in GMT.
					);  

					$update_post_results = wp_update_post($update_post);
}


			}
		}
    		$dbp = null;
		}
	}
	catch(PDOException $e)
    		{
    		}

print "\n Importacion Completa! \n\n";
get_footer();
//echo '<meta http-equiv="refresh" content="60;http://hoy.doap.com/hoy-import.php" />';
//<meta http-equiv="refresh" content="30=http://hoy.doap.com/hoy-import.php">
?>
