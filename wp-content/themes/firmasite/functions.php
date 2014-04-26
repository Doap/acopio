<?php
/* You should use child-themes for customizing FirmaSite theme. 
 * With child-theme usage, you can easily keep FirmaSite up-to-date
 * Detailed info and example child-theme: 
 * http://theme.firmasite.com/child-theme/
 */













/* DONT REMOVE THOSE LINES BELOW */

// If you define this as false, powered by WordPress icon in bottom will not show
if ( !defined('FIRMASITE_POWEREDBY') )
	define('FIRMASITE_POWEREDBY', true);

/*
 * You can define subset thats limiting google fonts
 * Default: 'latin-ext' means theme will only show fonts that have 'latin-ext' support and will load 'latin-ext' while calling that font.
 * You can use multi subsets with comma seperated. Example: define('FIRMASITE_SUBSETS', 'cyrillic,cyrillic-ext');
 * You dont need to define latin. For latin only you can define as empty: define('FIRMASITE_SUBSETS', '');
 */
if ( !defined('FIRMASITE_SUBSETS') )
	define('FIRMASITE_SUBSETS', 'latin-ext');


// If you define this as false, theme will remove showcase posts from home page loop
if ( !defined('FIRMASITE_SHOWCASE_POST') )
	define('FIRMASITE_SHOWCASE_POST', true);

// If you define this as true, PromotionBar will show at bottom while displaying in sidebar
if ( !defined('FIRMASITE_PROMOTIONBAR_BOTTOM') )
	define('FIRMASITE_PROMOTIONBAR_BOTTOM', true);

// If you define this as true, theme will not suggests plugins to theme users.
if ( !defined('FIRMASITE_DONT_SUGGEST_PLUGIN') )
	define('FIRMASITE_DONT_SUGGEST_PLUGIN', false);

// If you define this as true, theme will use cdn for bootstrap style, font-awesome icons and jQuery. 
// FirmaSite Theme Enhancer plugin have to activated for work.
if ( !defined('FIRMASITE_CDN') )
	define('FIRMASITE_CDN', false);
	
include ( get_template_directory() . '/functions/customizer-call.php');			// Customizer functions
require_once ( get_template_directory() . '/functions/init.php');	// Initial theme setup and constants

/* DONT REMOVE THOSE LINES ABOVE */

