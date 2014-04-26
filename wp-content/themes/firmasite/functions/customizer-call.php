<?php
/*
I was calling theme settings in functions.php and was calling settings as global variable in theme files. 
This was working properly except temporary changes was disappearing in theme customizer"s live edit when you change page with clicking links.
This file fixes that.
Read if you want more details: http://wordpress.stackexchange.com/questions/82723/theme-customizer-changes-are-dissappearing-when-change-page
*/

global $firmasite_settings;
$firmasite_settings = get_option( "firmasite_settings" ); // site options

do_action("firmasite_settings_open");

$defaults = array(
	"style" => "united",
	"layout" => "content-sidebar",
	"loop-style" => "loop-list",
	"color-logo-text" => "info",
	"color-tax" => "info",
	"menu-style" => "default",
	"loop_tile_row" => 3,
	"subsets" => FIRMASITE_SUBSETS
);

$firmasite_settings = wp_parse_args($firmasite_settings, $defaults);
$firmasite_settings["protocol"] = is_ssl() ? 'https' : 'http';

$lang = explode("-", get_bloginfo('language'));
$firmasite_settings["site_language"] = esc_attr($lang[0]);
$firmasite_settings["site_region"] = esc_attr($lang[1]);

switch ($firmasite_settings["layout"]) {
    case "sidebar-content":
		$firmasite_settings["layout_primary_class"] = "col-xs-12 col-md-8 pull-right";
 		$firmasite_settings["layout_primary_fullwidth_class"] = "col-xs-12 col-md-12";
		$firmasite_settings["layout_secondary_class"] = "col-xs-12 col-md-4";		
		$firmasite_settings["layout_container_class"] = "container";		
 		$firmasite_settings["layout_page_class"] = "site-sidebar-content";		
      break;
    case "only-content":
 		$firmasite_settings["layout_primary_class"] = "col-xs-12 col-md-12";
 		$firmasite_settings["layout_primary_fullwidth_class"] = "col-xs-12 col-md-12";
		$firmasite_settings["layout_secondary_class"] = "hide";		
		$firmasite_settings["layout_container_class"] = "container";		
 		$firmasite_settings["layout_page_class"] = "site-only-content";		
      break;
    case "only-content-long":
 		$firmasite_settings["layout_primary_class"] = "col-xs-12 col-md-12";
 		$firmasite_settings["layout_primary_fullwidth_class"] = "col-xs-12 col-md-12";
		$firmasite_settings["layout_secondary_class"] = "hide";		
		$firmasite_settings["layout_container_class"] = "container";		
 		$firmasite_settings["layout_page_class"] = "site-only-content-long";		
      break;
	default:
    case "content-sidebar":
 		$firmasite_settings["layout_primary_class"] = "col-xs-12 col-md-8";
 		$firmasite_settings["layout_primary_fullwidth_class"] = "col-xs-12 col-md-12";
		$firmasite_settings["layout_secondary_class"] = "col-xs-12 col-md-4";		
		$firmasite_settings["layout_container_class"] = "container";		
 		$firmasite_settings["layout_page_class"] = "site-content-sidebar";		
       break;	
}

$firmasite_settings["styles"] = apply_filters( 'firmasite_theme_styles', array(
	"default" => esc_attr__( 'Default', "firmasite" ),		//0
	"amelia" => esc_attr__( 'Amelia', "firmasite" ),		//1
	"cerulean" => esc_attr__( 'Cerulean', "firmasite" ),	//2
	"cosmo" => esc_attr__( 'Cosmo', "firmasite" ),			//3
	"cyborg" => esc_attr__( 'Cyborg', "firmasite" ),		//4
	"flatly" => esc_attr__( 'Flatly', "firmasite" ),		//13
	"journal" => esc_attr__( 'Journal', "firmasite" ),		//5
	"readable" => esc_attr__( 'Readable', "firmasite" ),	//6
	"simplex" => esc_attr__( 'Simplex', "firmasite" ),		//7
	"slate" => esc_attr__( 'Slate', "firmasite" ),			//8
	"spacelab" => esc_attr__( 'Spacelab', "firmasite" ),	//9
	"united" => esc_attr__( 'United', "firmasite" ),		//12
));
		
$firmasite_styles_url_default = get_template_directory_uri() . '/assets/themes/';
$firmasite_settings["styles_url"] = apply_filters( 'firmasite_theme_styles_url', array(		
	"default" => $firmasite_styles_url_default. "default",		//0
	"amelia" => $firmasite_styles_url_default. "amelia",		//1
	"cerulean" => $firmasite_styles_url_default. "cerulean",	//2
	"cosmo" => $firmasite_styles_url_default. "cosmo",			//3
	"cyborg" => $firmasite_styles_url_default. "cyborg",		//4
	"flatly" => $firmasite_styles_url_default. "flatly",		//13
	"journal" => $firmasite_styles_url_default. "journal",		//5
	"readable" => $firmasite_styles_url_default. "readable",	//6
	"simplex" => $firmasite_styles_url_default. "simplex",		//7
	"slate" => $firmasite_styles_url_default. "slate",			//8
	"spacelab" => $firmasite_styles_url_default. "spacelab",	//9
	"united" => $firmasite_styles_url_default. "united",		//12
));

do_action("firmasite_settings_close");

apply_filters( 'firmasite_settings_filter', $firmasite_settings );


