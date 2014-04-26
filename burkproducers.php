<?php
    header('Content-Type: application/csv charset=UTF-8');
    // tell the browser we want to save it instead of displaying it
//    header('Content-Disposition: attachement; filename="'.$filename.'"');
    header('Content-Disposition: attachement;filename="export.csv";');

define('WP_USE_THEMES', true);
/** Loads the WordPress Environment and Template */
//require_once( '/var/www/html/wp-blog-header.php' );
require_once( '/var/www/html/laprensa/shawn/wp-load.php' );
require_once('/var/www/html/laprensa/shawn/wp-config.php');
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals();
// $wp->send_headers();
//require_once( '/var/www/html/laprensa/shawn/wp-admin/includes/image.php' );
//switch_to_blog('1');
//get_header();
function substring_index($subject, $delim, $count){
    if($count < 0){
        return implode($delim, array_slice(explode($delim, $subject), $count));
    }else{
        return implode($delim, array_slice(explode($delim, $subject), 0, $count));
    }
}

//date stuff
date_default_timezone_set('America/Managua');
$b = time();
$month = date("m", $b);
$monthspelled = date("M", $b);
$day = date("D", $b);
$numday = date("d", $b);
$year = date("Y", $b);
$customer_string = 'a:1:{s:8:\"customer\";b:1;}';
$producers = $wpdb->get_col( "SELECT distinct user_id FROM $wpdb->usermeta where meta_key = 'wp_capabilities' and meta_value = '$customer_string' ORDER BY ABS(user_id) ASC;" );
$metavals4 = implode( ",", $producers);
//$last_imported = 10351;
//print "\n Comenzando Importacion <br>\n\n" . PHP_EOL;
//print "\n <br>Producers = $metavals4 <br>\n\n\n\n" . PHP_EOL;

//$wp_potential_posts = $wpdb->get_col( "SELECT distinct post_id FROM $wpdb->postmeta where meta_key = 'idnoticia' and meta_value IN ($idnoticias_with_aside) ORDER BY ABS(post_id) ASC limit $limit;" );
//$wp_pot_posts_ids = implode( ",", $wp_potential_posts);
//print "\n <br>wp_pot_posts_ids = $wp_pot_posts_ids<br>\n\n\n\n" . PHP_EOL;
//$wp_posts_to_modify = $wpdb->get_col( "SELECT ID FROM $wpdb->posts where ID IN ($wp_pot_posts_ids) and post_type = 'post' and post_status = 'publish' ORDER BY ABS(ID) ASC limit $limit;" );
//print "\n <br>post_id[0] = $wp_posts_to_modify[0]<br>\n\n\n\n" . PHP_EOL;
$producer = array();
$i=0;
foreach ($producers as $row)
	{
	date_default_timezone_set('America/Managua');
//print "\n <br>I = $i<br>\n\n\n\n" . PHP_EOL;
	$user_id = $row;
$first_name =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'first_name';" );
$last_name =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'last_name';" );
$nickname =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'nickname';" );
$description =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'description';" );
$productor_telefono =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_telefono';" );
$productor_sexo =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_sexo';" );
$productor_organico =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_organico';" );
$productor_convencional =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_convencional';" );
$productor_mango =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_mango';" );
$productor_pina =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_pina';" );
$productor_pitaya =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_pitaya';" );
$productor_banano =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_banano';" );
$productor_pit_ha =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_pit_ha';" );
$productor_pina_ha =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_pina_ha';" );
$productor_man_ha =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_man_ha';" );
$productor_manzana_total =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_manzana_total';" );
$productor_superfic_ha =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_superfic_ha';" );
$productor_edad =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_edad';" );
$productor_estado_civil =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_estado_civil';" );
$productor_acopio =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_acopio';" );
$productor_activo =  $wpdb->get_var( "SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $user_id AND meta_key = 'productor_activo';" );
$user_login =  $wpdb->get_var( "SELECT user_login FROM $wpdb->users where ID = $user_id;" );
$company =  $wpdb->get_var( "SELECT company FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$tax_id_number =  $wpdb->get_var( "SELECT tax_id_number FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$company_id =  $wpdb->get_var( "SELECT company_id FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$street =  $wpdb->get_var( "SELECT street FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$street_2 =  $wpdb->get_var( "SELECT street_2 FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$city =  $wpdb->get_var( "SELECT city FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$city_id =  $wpdb->get_var( "SELECT city_id FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$region =  $wpdb->get_var( "SELECT region FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$region_id =  $wpdb->get_var( "SELECT region_id FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$postcode =  $wpdb->get_var( "SELECT postcode FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$country_id =  $wpdb->get_var( "SELECT country_id FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$telephone_1 =  $wpdb->get_var( "SELECT telephone_1 FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$telephone_2 =  $wpdb->get_var( "SELECT telephone_2 FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$fax =  $wpdb->get_var( "SELECT fax FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );
$email =  $wpdb->get_var( "SELECT email FROM wp_tcp_addresses WHERE customer_id = $user_id AND default_billing = 'Y';" );

	$producer[$row]  = array( "user_id" => $row,
"user_login" => $user_login,
"first_name" => $first_name,
"last_name" => $last_name,
"nickname" => $nickname,
"description" => $description,
"productor_telefono" => $productor_telefono,
"productor_sexo" => $productor_sexo,
"productor_organico" => $productor_organico,
"productor_convencional" => $productor_convencional,
"productor_mango" => $productor_mango,
"productor_pina" => $productor_pina,
"productor_pitaya" => $productor_pitaya,
"productor_banano" => $productor_banano,
"productor_pit_ha" => $productor_pit_ha,
"productor_pina_ha" => $productor_pina_ha,
"productor_man_ha" => $productor_man_ha,
"productor_manzana_total" => $productor_manzana_total,
"productor_superfic_ha" => $productor_superfic_ha,
"productor_edad" => $productor_edad,
"productor_estado_civil" => $productor_estado_civil,
"productor_acopio" => $productor_acopio,
"productor_activo" => $productor_activo,
"company" => $company,
"tax_id_number" => $tax_id_number,
"company_id" => $company_id,
"street" => $street,
"street_2" => $street_2,
"city" => $city,
"city_id" => $city_id,
"region" => $region,
"region_id" => $region_id,
"postcode" => $postcode,
"country_id" => $country_id,
"telephone_1" => $telephone_1,
"telephone_2" => $telephone_2,
"fax" => $fax,
"email" => $email
				);
			
	//$articletext =  $wpdb->get_var( "SELECT post_content FROM $wpdb->posts where ID = $post_id;" );
	//$articledate = $wpdb->get_var( "SELECT post_date FROM $wpdb->posts where ID = $post_id;" );
	//$articletitle =  $wpdb->get_var( "SELECT post_title FROM $wpdb->posts where ID = $post_id;" );
	//$articlelink =  $wpdb->get_var( "SELECT guid FROM $wpdb->posts where ID = $post_id;" );
//print "<hr>";
//print '<div>';
//print "\n <br>user_id = $user_id<br>\n\n\n\n" . PHP_EOL;
$i++;
	}
//print_r($producer);
//print_r($producers);
//var_dump($producer);
//print "\n Importacion Completa! \n\n";

function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
    // open raw memory as file so no temp files needed, you might run out of memory though
    $f = fopen('php://memory', 'w'); 
    // loop over the input array
$arr = array_pop( $array);
 $temp = array();
 foreach( $arr as $key => $data ) {
   $temp[] = $key;
 }
$csv = implode( ',', $temp ) . "\n";
    fputcsv($f, $temp, $delimiter); 
    foreach ($array as $line) { 
        // generate csv lines from the inner arrays
        fputcsv($f, $line, $delimiter); 
    }
    // rewrind the "file" with the csv lines
    fseek($f, 0);
    // tell the browser it's going to be a csv file
//    header('Content-Type: application/csv charset=UTF-8');
    // tell the browser we want to save it instead of displaying it
//    header('Content-Disposition: attachement; filename="'.$filename.'"');
//    header('Content-Disposition: attachement;filename="'.$filename.'";');
    // make php send the generated csv lines to the browser
    fpassthru($f);
}
array_to_csv_download($producer);
//get_footer();
//echo '<meta http-equiv="refresh" content="60;http://hoy.doap.com/hoy-import.php" />';
//<meta http-equiv="refresh" content="30=http://hoy.doap.com/hoy-import.php">
?>
