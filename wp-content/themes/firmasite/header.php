<?php
/**
 * @package firmasite
 */

global $firmasite_settings;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
	<?php if ( !isset($firmasite_settings["no-responsive"]) || empty($firmasite_settings["no-responsive"]) ) { ?>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php } ?>
   <title>
		<?php
        if ( defined( 'WPSEO_VERSION' ) ) {
            // WordPress SEO is activated
                wp_title();  
        } else {       
            // WordPress SEO is not activated
            wp_title( '&#124;', true, 'right' );
        } ?>
    </title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
 
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<a href="#primary" class="sr-only"><?php _e("Skip to content", "firmasite" );?></a>

<div id="page" class="hfeed site <?php echo $firmasite_settings["layout_page_class"]; ?> <?php echo $firmasite_settings["style"].'-theme'; ?>">

	<?php do_action( 'before_header' ); ?>
    
	<header id="masthead" class="site-header <?php echo $firmasite_settings["layout_container_class"]; ?>" role="banner">
    
       <?php do_action( 'open_header' ); ?>
       
	   	<div id="logo" class="pull-left">         
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home" id="logo-link" class="logo" data-section="body">
            	<?php if (isset($firmasite_settings["logo"]) && !empty($firmasite_settings["logo"])) {?>
                <img src="<?php echo $firmasite_settings["logo"];?>" alt="<?php bloginfo( 'description' ); ?>" title="<?php bloginfo( 'name' ); ?>" id="logo-img" />
                 <?php } else {?>
                <span class="label label-<?php echo $firmasite_settings["color-logo-text"];?> logo-text pull-left"><?php bloginfo( 'name' ); ?></span>
                <?php }?>
            </a>
           <?php if (get_bloginfo( 'description' )) {?>
           <?php } ?>
        </div>
		
        <?php do_action( 'logo_side_before' ); ?>
        
        <div id="logo-side" class="pull-right">
	       <?php do_action( 'logo_side_open' ); ?>

	       <div id="site-description" class="no-margin-bot text-right text-muted hidden-xs hidden-sm"><?php bloginfo( 'description' ); ?></div>

	       <?php do_action( 'logo_side_close' ); ?>           
    	</div>
        
        <?php do_action( 'logo_side_after' ); ?>
        
        <div id="navbar-splitter" class="clearfix"></div>
        
		<?php if (has_nav_menu('main_menu')) : 
               switch ($firmasite_settings["menu-style"]) {
                    case "simple":
               ?>
                      <div class="hidden-lg">
                          <a class="navbar-toggle" data-toggle="collapse" data-target=".main-menu-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                          </a>
                      </div>                 
                      <nav id="nav-main" class="collapse navbar-collapse main-menu-collapse" role="navigation">
                        <?php  wp_nav_menu(array('theme_location' => 'main_menu', 'menu_class' => 'nav nav-pills')); ?>
                      </nav>
               <?php
                    break;
                   
                    case "default":
                    case "alternative":
                    default:
               ?>
                <nav id="mainmenu" role="navigation" class="site-navigation main-navigation navbar <?php if ((isset($firmasite_settings["alternative"]) && !empty($firmasite_settings["alternative"])) || "alternative" == $firmasite_settings["menu-style"]){ echo " navbar-inverse";} else { echo " navbar-default"; } ?>">          
                  <div class="navbar-header">
                      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".main-menu-collapse">
                        <span class="sr-only"><?php _e("Toggle navigation", "firmasite" );?></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                      </button>
                  </div>                
                  <div id="nav-main" class="collapse navbar-collapse main-menu-collapse" role="navigation">
                    <?php  wp_nav_menu(array('theme_location' => 'main_menu', 'menu_class' => 'nav navbar-nav')); ?>
                  </div>
                </nav>    <!-- .site-navigation .main-navigation --> 
               <?php 
                    break;
               }
		endif; ?>
       
		<?php do_action( 'close_header' ); ?>
       
	</header><!-- #masthead .site-header -->
    
	<?php do_action( 'after_header' ); ?>
    
	<div id="main" class="site-main <?php echo $firmasite_settings["layout_container_class"]; ?>">
        <div class="row">
        <?php do_action( 'before_content' ); ?>    
