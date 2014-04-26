<?php

if (!function_exists('synved_shortcode_init_skin_slickpanel'))
{

function synved_shortcode_enqueue_scripts_skin_slickpanel()
{
	$uri = synved_shortcode_path_uri('addons/' . basename(dirname(__FILE__)));

	wp_register_style('synved-shortcode-skin-slickpanel-layout', $uri . '/style/layout.css', array('synved-shortcode-layout'), '1.0');
	wp_register_style('synved-shortcode-skin-slickpanel-jquery-ui', $uri . '/style/jquery-ui.css', array('synved-shortcode-jquery-ui'), '1.0');

	//wp_register_script('synved-shortcode-skin-slickpanel-custom', $uri . '/script/custom.js', array('synved-shortcode-custom'), '1.0.0');

	if (synved_option_get('synved_shortcode', 'custom_skin') == 'slickpanel')
	{
		wp_enqueue_style('synved-shortcode-skin-slickpanel-layout');
		wp_enqueue_style('synved-shortcode-skin-slickpanel-jquery-ui');
	}

	//wp_enqueue_script('synved-shortcode-skin-slickpanel-custom');

}

function synved_shortcode_init_skin_slickpanel()
{
	add_action('wp_enqueue_scripts', 'synved_shortcode_enqueue_scripts_skin_slickpanel');
	add_action('admin_enqueue_scripts', 'synved_shortcode_enqueue_scripts_skin_slickpanel');
}

add_action('init', 'synved_shortcode_init_skin_slickpanel');

}

?>
