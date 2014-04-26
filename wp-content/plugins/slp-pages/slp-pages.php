<?php
/**
 * Plugin Name: Store Locator Plus : Store Pages
 * Plugin URI: http://www.charlestonsw.com/product/store-locator-plus-store-pages/
 * Description: A premium add-on pack for Store Locator Plus that creates custom pages for your locations.
 * Version: 3.9.2
 * Author: Charleston Software Associates
 * Author URI: http://charlestonsw.com/
 * Requires at least: 3.3
 * Test up to : 3.5.1
 *
 * Text Domain: csa-slp-pages
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// No SLP? Get out...
//
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( !function_exists('is_plugin_active') ||  !is_plugin_active( 'store-locator-le/store-locator-le.php')) {
    return;
}

/**
 * The Tagalong Add-On Pack for Store Locator Plus.
 *
 * @package StoreLocatorPlus\Pages
 * @author Lance Cleveland <lance@charlestonsw.com>
 * @copyright 2012-2013 Charleston Software Associates, LLC
 */
class SLPPages {

    private $adminMode = false;
    private $currentLocationData    = null;
    private $dir;
    private $metadata = null;
    private $settingsSlug = 'slp_storepages';
    private $slug = null;
    private $url;

    /**
     * Public Properties
     */
    public $plugin      = null;
    public $plugin_path = null;
    public $Settings    = null;

    /**
     * Stores the values for the current Store Page.
     * 
     * @var array an array of page values
     */
    private $currentPage = null;

    /**
     * Stores the value of options this plugin supports.
     * 
     * @var array $options named array, key = name of option, value = current setting
     */
    public $options     = array(
        'default_comments'                  => '1',
        'default_trackbacks'                => '1',
        'pages_replace_websites'            => '0',
        'page_template'                     => '',
        'permalink_starts_with'             => 'store-page',
        'prevent_new_window'                => '0',
        );


    /**
     * Constructor
     */
    function __construct() {
        $this->url  = plugins_url('',__FILE__);
        $this->dir  = plugin_dir_path(__FILE__);
        $this->slug = plugin_basename(__FILE__);

        // Init options not setup in the public define.
        $this->options['page_template'] = $this->create_DefaultPageTemplate();

        // SLP Actions & Filters
        //
        add_action('slp_admin_menu_starting'        ,array($this,'admin_menu'               )   );
        add_action('slp_init_starting'              ,array($this,'handle_slp_init'          )   );
        add_action('slp_init_complete'              ,array($this,'action_SLPInitComplete'   )   );
        add_filter('slp_storepage_features'         ,array($this,'modify_storepage_features')   );
    }

    //====================================================
    // WordPress Admin Actions
    //====================================================

    /**
     * WordPress admin_init hook for Tagalong.
     */
    function admin_init(){

        // WordPress Update Checker - if this plugin is active
        //
        if (is_plugin_active($this->slug)) {
            $this->metadata = get_plugin_data(__FILE__, false, false);
            $this->Updates = new SLPlus_Updates(
                    $this->metadata['Version'],
                    $this->plugin->updater_url,
                    $this->slug
                    );
        }

        // Register the admin stylesheet
        //
        wp_register_style(
            $this->settingsSlug.'_style',
            $this->url . '/admin.css'
            );

        // SLP Action Hooks & Filters (admin UI only)
        //
        add_action('slp_deletelocation_starting'        ,array($this,'handle_delete_locations'              )           );
        add_action('slp_manage_locations_action'        ,array($this,'handle_manage_locations_action'       )           );

        add_filter('slp_action_boxes'                   ,array($this,'manage_locations_actionbar'           )           );
        add_filter('slp_edit_location_data'             ,array($this,'SetLocationProperties'                )           );
        add_filter('slp_edit_location_right_column'     ,array($this,'add_edit_location_settings'           )           );
        add_filter('slp_manage_location_columns'        ,array($this,'add_manage_locations_columns'         )           );
        add_filter('slp_manage_locations_actionbuttons' ,array($this,'add_manage_locations_actionbuttons'   ),15,2      );
        add_filter('slp_column_data'                    ,array($this,'filter_AddFieldDataToManageLocations' ),90    ,3  );            
    }

    /**
     * WordPress admin_menu hook.
     */
    function admin_menu(){
        if (!$this->setPlugin()) {
            return '';
            }
        $this->adminMode = true;
        $slugPrefix = 'store-locator-plus_page_';

       // Admin Styles
        //
        add_action(
                'admin_print_styles-' . $slugPrefix .$this->settingsSlug,
                array($this,'enqueue_admin_stylesheet')
                );

        // Admin Actions
        //
        add_action('admin_init'                 ,array($this,'admin_init'                   )       );
        add_action('delete_post'                ,array($this,'handle_delete_storepage'      )       );
        add_filter('slp_menu_items'             ,array($this,'add_menu_items'               ),90    );
    }

/**
     * Init this plugin when WordPress init is called. (Singleton)
 * @static
 */
public static function init() {
        static $instance = false;
        if ( !$instance ) {
            load_plugin_textdomain( 'csa-slp-pages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
            $instance = new SLPPages;

            // Property inits (one time only please)
            //
            $instance->plugin_path = dirname( __FILE__ );
        }

        // Load up the options
        //
        $dbOptions = get_option($instance->settingsSlug.'-options');
        if (is_array($dbOptions)) {
            $instance->options = array_merge($instance->options,$dbOptions);
        }

        return $instance;
}

    //====================================================
    // Helpers
    //====================================================

    /**
     * Debug for action hooks.
     * 
     * @param type $tagname
     * @param type $parm1
     * @param type $parm2
     */
    function debug($tagname,$parm1=null,$parm2=null) {
        print "$tagname<br/>\n".
              "<pre>".print_r($parm1,true)."</pre>".
              "<pre>".print_r($parm2,true)."</pre>"
                ;
        die($this->slug . ' debug hooked.');
    }

    /**
     * Enqueue the style sheet when needed.
     */
    function enqueue_admin_stylesheet() {
        wp_enqueue_style($this->settingsSlug.'_style');
        wp_enqueue_style($this->plugin->AdminUI->styleHandle);
    }        

    /**
     * Set the plugin property to point to the primary plugin object.
     *
     * Returns false if we can't get to the main plugin object or
     * STORE PAGES IS NOT LICENSED
     *
     * @global wpCSL_plugin__slplus $slplus_plugin
     * @return boolean true if plugin property is valid
     */
    function setPlugin() {
        if (!isset($this->plugin) || ($this->plugin == null)) {
            global $slplus_plugin;
            $this->plugin = $slplus_plugin;
        }
        return (
            isset($this->plugin)    &&
            ($this->plugin != null)                
            );
    }


    //====================================================
    // Store Pages Custom Methods
    //====================================================

    function add_edit_location_settings($theHTML) {
        if (!$this->setPlugin()                         ) { return $theHTML;  }
        if (!isset($this->currentLocationData['sl_id']) ) { return $theHTML;  }
        if ($this->currentLocationData['sl_id'] < 0     ) { return $theHTML;  }

        $shortSPurl = preg_replace('/^.*?store_page=/','',$this->currentLocationData['sl_pages_url']);
        if (!empty($this->currentLocationData['sl_pages_url'])) {
            $theHTML .=
                '<div id="slp_pages_fields" class="slp_editform_section">'.
                    '<strong>Store Pages</strong><br/>'.
                    "<a name='store_page' href='".$this->currentLocationData['sl_pages_url']."' target='csa'>$shortSPurl</a>" .
                    '<small>'.__('Permalink','slp-pages').'</small>'.
                '</div>'
                ;
        }
        return $theHTML;
    }

    /**
     * Add a location action button.
     *
     * @param string $theHTML - the HTML of the original buttons in place
     * @param array $locationValues
     * @return string - the augmented HTML
     */
    function add_manage_locations_actionbuttons($theHTML,$locationValues) {
        if (!$this->setPlugin())                { return $theHTML;  }
        if (!isset($locationValues['sl_id']))   { return $theHTML;  }
        if ($locationValues['sl_id'] < 0)       { return $theHTML;  }

        // Set the URL
        //
        $shortSPurl = preg_replace('/^.*?store_page=/','',$locationValues['sl_pages_url']);
        $locationValues['sl_pages_url'] = "<a href='$locationValues[sl_pages_url]' target='cybersprocket'>$shortSPurl</a>";

        $pageClass = (($locationValues['sl_linked_postid']>0)?'haspage_icon' : 'createpage_icon');
        $pageURL  = preg_replace(
                        '/&createpage=/'.(isset($_GET['createpage'])?$_GET['createpage']:''),
                        '',
                        $_SERVER['REQUEST_URI']
                        ).
                     '&act=createpage'.
                     '&sl_id='.$locationValues['sl_id'].
                     '&slp_pageid='.$locationValues['sl_linked_postid'].
                     '#a'.$locationValues['sl_id']
                ;
        return $theHTML .
               "<a  class='action_icon $pageClass' ".
                    "alt='".__('create page','csa-slplus')."' ".
                    "title='".__('create page','csa-slplus')."' ".
                    "href='$pageURL'></a>"
                ;
    }

    /**
     * Add the Store Pages URL column.
     * 
     * @param array $theColumns - the array of column data/titles
     * @return array - modified columns array
     */
    function add_manage_locations_columns($theColumns) {
        if (!$this->setPlugin()) { return $theColumns; }
        return array_merge($theColumns,
                array(
                    'sl_pages_url'      => __('Pages URL'          ,'csa-slplus'),
                )
            );
    }

    /**
     * Add the Store Pages Menu Item
     *
     * @param type $menuItems
     * @return type
     */
    function add_menu_items($menuItems) {
        if (!$this->setPlugin()) { return $menuItems; }
        return array_merge(
                    $menuItems,
                    array(
                        array(
                        'label' => __('Store Pages',SLPLUS_PREFIX),
                        'slug'              => 'slp_storepages',
                        'class'             => $this,
                        'function'          => 'render_SettingsPage'
                        )
                    )
                );
    }

    /**
     * Create the default Store Page content.
     *
     * @return string - HTML content that is the WordPress page content.
     */
    function create_DefaultPageTemplate() {
        $content = 
            '<span class="storename">[storepage field="sl_store"]</span>'                           ."\n".
            '<img class="alignright size-full" title="[storepage field="sl_store"]" '               .
                'src="[storepage field="sl_image"]"'                                                .
             '/>'                                                                                   ."\n".
            '[storepage field="sl_address"]'                                                        ."\n".
            '[storepage field="sl_address2"]'                                                       ."\n".
            '[storepage field="sl_city"] [storepage field="sl_state"] [storepage field="sl_zip"] '  ."\n".
            '[storepage field="sl_country"]'                                                        ."\n".
            '<h1>Description</h1>'                                                                  ."\n".
            '<p>[storepage field="sl_description"]</p>'                                             ."\n".
            '<h1>Contact Info</h1>'                                                                 ."\n".
            'Phone: [storepage field="sl_phone"]'                                                   ."\n".
            'Fax: [storepage field="sl_fax"]'                                                       ."\n".
            '<a href="mailto:[storepage field="sl_email"]">[storepage field="sl_email"]</a>'        ."\n".
            '<a href="[storepage field="sl_url"]">[storepage field="sl_url"]</a>'                ."\n"
            ;

        return apply_filters('slp_pages_default_content',$content);
    }

    /**
     * Create a new store pages page.
     *
     * @global wpCSL_plugin__slplus $slplus_plugin
     * @global type $wpdb
     * @param type $locationID
     * @return type
     */
     function CreatePage($locationID=-1, $keepContent = false, $post_status = 'publish')  {
        if (!$this->setPlugin()) { return -1; }
        if ($locationID < 0)     { return -1; }

        // Get The Store Data
        //
        global $wpdb;
        if ($store=$wpdb->get_row('SELECT * FROM '.$wpdb->prefix."store_locator WHERE sl_id = $locationID", ARRAY_A)) {

            $slpStorePage = get_post($store['sl_linked_postid']);
            if (empty($slpStorePage->ID)) {
                $store['sl_linked_postid'] = -1;
            }

            // Update the row
            //
            $wpdb->update($wpdb->prefix."store_locator", $store, array('sl_id' => $locationID));

            // Prior Post Status
            // If new post, use 'draft' as status
            // otherwise keep the current publication state.
            //
            if ($post_status === 'prior') {
                $post_status =
                    (empty($slpStorePage->ID))      ?
                    'draft'                         :
                    $slpStorePage->post_status
                    ;
            }


            // Create the page
            //
            $slpNewListing = array(
                'ID'            => (($store['sl_linked_postid'] > 0)?$store['sl_linked_postid']:''),
                'comment_status'=> ($this->options['default_comments']  ?'open':'closed'),
                'ping_status'   => ($this->options['default_trackbacks']?'open':'closed'),
                'post_type'     => 'store_page',
                'post_status'   => $post_status,
                'post_title'    => $store['sl_store'],
                'post_content' =>
                    ($keepContent) ?
                        (empty($slpStorePage->ID) ?
                            '' 
                            : 
                            $slpStorePage->post_content
                        ):
                        $this->CreatePageContent($store)
                );

            // Apply Third Party Filters
            //
            $slpNewListing = apply_filters('slp_pages_insert_post',$slpNewListing);

            return wp_insert_post($slpNewListing);
         }
     }

     /**
      * Create the content for a Store Page.
      *
      * Creates the content for the page.  If plus pack is installed
      * it uses the plus template file, otherwise we use the hard-coded
      * layout.
      *
      * @param type $store
      * @return string
      */
    function CreatePageContent($store) {
         return apply_filters('slp_pages_content',$this->options['page_template']);
     }

     /**
      * Load up the current location data named array when given a store page ID
      * 
      * @global type $wpdb
      * @param type $storepageID
      * @return type
      */
     function set_currentLocationData($storepageID=null) {
         if (!$this->setPlugin()    )   { return -1; }
         if ($storepageID === null  )   { return -1; }
         if (!is_numeric($storepageID)) { return -1; }


         global $wpdb;
         $this->currentLocationData = $wpdb->get_row(
                 $this->plugin->database['query']['selectall'].
                    'WHERE sl_linked_postid='.$storepageID
                 ,
                 ARRAY_A
                 );

         return $this->currentLocationData['sl_id'];
     }

    /**
     * Render the extra fields on the manage location table.
     *
     * SLP Filter: slp_column_data
     *
     * @param string $theData  - the option_value field data from the database
     * @param string $theField - the name of the field from the database (should be sl_option_value)
     * @param string $theLabel - the column label for this column (should be 'Categories')
     * @return string the modified data
     */
    function filter_AddFieldDataToManageLocations($theData,$theField,$theLabel) {
        if (
            ($theField === 'sl_pages_url')
           ) {
            $theData =($this->plugin->currentLocation->pages_url!='')?
                $this->create_ShortPageURL($this->plugin->currentLocation->pages_url) :
                "" ;
        }
        return $theData;
    }

    /**
     * Augment the script data passed to the csl.js script.
     */
    function filter_SLPScriptData($scriptData) {
        return array_merge($scriptData,
                array(
                    'use_pages_links'   => $this->options['pages_replace_websites'],
                    'use_same_window'   => $this->options['prevent_new_window']
                )
            );
    }

    /**
     * Create a short Store Page URL for use on manage locations interface.
     *
     * @param string $fullURL
     * @return string the short hyperlinked URL
     */
    function create_ShortPageURL($fullURL) {
        $pattern = '/^(.*?)=/';
        $shortURL = preg_replace($pattern,'',$fullURL);
        return "<a href='$fullURL' target='csa'>$shortURL</a>";
    }

     /**
      * When a store location is deleted delete the store page.
      * 
      * @param type $locationIDs
      * @return boolean
      */
     function handle_delete_locations($locationIDs) {
         if (!$this->setPlugin()) { return true; }

         global $wpdb;
         foreach ($locationIDs as $locationID) {
             $storepageID = $wpdb->get_var(
                     sprintf($this->plugin->database['query']['selectthis'] ,'sl_linked_postid' ).
                     sprintf($this->plugin->database['query']['whereslid']  ,$locationID        )
                     );
             if (is_numeric($storepageID)) {
                wp_delete_post($storepageID);
             }
         }
     }

     /**
      * Set the link in store locations table to null when a store page is permanently deleted.
      *
      * @global type $wpdb
      * @param type $pageID
      * @return boolean
      */
     function handle_delete_storepage($pageID) {
         if (!$this->setPlugin()) { return true; }
         global $wpdb;
         if ($wpdb->get_var($wpdb->prepare(sprintf('SELECT sl_linked_postid'.$this->plugin->database['query']['fromslp'].' WHERE sl_linked_postid = %d',$pageID)))) {
             return $wpdb->query($wpdb->prepare(sprintf('UPDATE'.$this->plugin->database['table'].' SET sl_linked_postid = NULL WHERE sl_linked_postid = %d',$pageID)));
         }
        return true;
     }

     /**
      * Handle actions from the Manage Locations interface.
      */
     function handle_manage_locations_action() {

        // Create Store Page(s)
        //
        if ($_REQUEST['act'] == 'createpage') {
            if (isset($_REQUEST['sl_id'])) {
                if (!is_array($_REQUEST['sl_id'])) {
                    $theLocations = array($_REQUEST['sl_id']);
                } else {
                    $theLocations = $_REQUEST['sl_id'];
                }

                global $wpdb;
                foreach ($theLocations as $thisLocation) {
                    $slpNewPostID = $this->CreatePage($thisLocation);
                    if ($slpNewPostID >= 0) {
                        $slpNewPostURL = get_permalink($slpNewPostID);
                        $wpdb->query("UPDATE ".$wpdb->prefix."store_locator ".
                                        "SET sl_linked_postid=$slpNewPostID, ".
                                        "sl_pages_url='$slpNewPostURL' ".
                                        "WHERE sl_id=$thisLocation"
                                        );
                        print "<div class='updated settings-error'>" .
                                ( (isset($_REQUEST['slp_pageid']) && ($slpNewPostID != $_REQUEST['slp_pageid']))?'Created new ':'Updated ').
                                " store page #<a href='$slpNewPostURL'>$slpNewPostID</a>" .
                                " for location # $thisLocation" .
                                "</div>\n";
                    } else {
                        print "<div class='updated settings-error'>Could NOT create page" .
                                " for location # $thisLocation" .
                                "</div>\n";
                    }
                }
            }
        } //--- Create Page Action
     }

     /**
      * Things we do when a new permalink has been set.
      *
      * Start by re-registering our post type with the new permalink info.
      *
      */
     function handle_new_permalink($newVal) {
        register_post_type('store_page',array('rewrite' => array('slug'=>$newVal)));
        flush_rewrite_rules();
     }

     /**
      * Run this every time SLP init finishes
      */
     function handle_slp_init() {
         if (!$this->setPlugin()) { return; }

        // WordPress Hooks
        add_shortcode('storepage',array($this,'handle_storepage_shortcode'));
        add_action('the_post',array($this,'handle_post_processing'));

        // SLP Hooks
        //
        add_filter('slp_storepage_attributes'   ,array($this,'modify_storepage_attributes'  )       );
     }

     /**
      * Run this when the SLP Init has completed.
      */
     function action_SLPInitComplete() {
        if (!$this->setPlugin()) { return; }
        add_filter('slp_script_data',array($this,'filter_SLPScriptData'));
        $this->plugin->register_addon($this->slug);
     }

     /**
      * Things we do ONE TIME when a post is being processed.
      */
     function handle_post_processing() {
        $this->currentPage['ID'] = get_the_ID();
        $this->set_currentLocationData($this->currentPage['ID']);
     }

     /**
      * Manage the storepage shortcode
      *
      * @param array $attributes named array of attributes set in shortcode
      * @param string $content the existing content that we will modify
      * @return string the modified HTML content
      */
     function handle_storepage_shortcode($attributes, $content = null) {
        if (!$this->setPlugin()) { return $content; }

        // Filter attributes to get only approved attributes
        // also initialize those that are approved
        //
        $attributes =
            shortcode_atts(
                array(
                    'field' => null,
                ),
                $attributes
               );
        if ($attributes['field']===null) { return ''; }


        // Set the content
        //
        $content = $content .
                $this->currentLocationData[$attributes['field']]
                ;

        return $content;
     }

    /**
     * Set the options from the incoming REQUEST
     *
     * @param mixed $val - the value of a form var
     * @param string $key - the key for that form var
     */
    function setOptions($val,$key) {
        $simpleKey = preg_replace('/^'.$this->settingsSlug.'\-/','',$key);
        if ($simpleKey !== $key){

            // Special Actions
            switch ($simpleKey) {
                case 'permalink_starts_with':
                    if ($this->options[$simpleKey] !== $val) {
                        $this->handle_new_permalink($val);
                    }
                    break;
                case 'page_template':
                    if ($this->options[$simpleKey] !== $val) {
                        $val = stripslashes($val);
                    }
                    break;
            }

            // Now set the value...
            $this->options[$simpleKey] = $val;
        }
     }         

     /**
      * Add Stor Pages action buttons to the action bar
      *
      * @param array $actionBoxes - the existing action boxes, 'A'.. each named array element is an array of HTML strings
      * @return string
      */
     function manage_locations_actionbar($actionBoxes) {
            if (!$this->setPlugin()) { return $actionBoxes; }
            $actionBoxes['C'][] =
                    '<p class="centerbutton">' .
                        "<a class='like-a-button' href='#' "            .
                                "onclick=\"doAction('createpage','"     .
                                    __('Create Pages?',SLPLUS_PREFIX)   .
                                    "')\" name='createpage_selected'>"  .
                                    __('Create Pages', SLPLUS_PREFIX)   .
                         '</a>'                                         .
                    '</p>'
            ;
            return $actionBoxes;
     }

     /**
      * Modify the default store pages attributes.
      *
      * Basically turns on/off store pages.
      *
      * @param type $attributes
      * @return type
      */
     function modify_storepage_attributes($attributes) {
        if (!$this->setPlugin()) { return $attributes; }
        return array_merge(
                $attributes,
                array(
                    'public'    => true,
                    'rewrite'   =>
                        array(
                            'slug' => $this->options['permalink_starts_with']
                        )
                )
                );
     }

     /**
      * Modify the default store pages features.
      *
      * @param type $attributes
      * @return type
      */
     function modify_storepage_features($features) {
        if (!$this->setPlugin()) { return $features; }
        return array_merge(
                $features,
                array(
                )
                );
     }

     // Render the settings page
     //
     function render_SettingsPage() {
        if (!$this->setPlugin()) { return __('Store Pages has not been activated.','csa-slp-pages'); }

        // If we are updating settings...
        //
        if (isset($_REQUEST['action']) && ($_REQUEST['action']==='update')) {
            $this->updateSettings();
        }

        // Setup and render settings page
        //
        $this->Settings = new wpCSL_settings__slplus(
            array(
                    'no_license'        => true,
                    'prefix'            => $this->settingsSlug,
                    'css_prefix'        => $this->plugin->prefix,
                    'url'               => $this->plugin->url,
                    'name'              => $this->plugin->name . ' - Store Pages',
                    'plugin_url'        => $this->plugin->plugin_url,
                    'render_csl_blocks' => true,
                    'form_action'       => admin_url().'admin.php?page='.$this->settingsSlug
                )
         );

        //-------------------------
        // Navbar Section
        //-------------------------
        $this->Settings->add_section(
            array(
                'name'          => 'Navigation',
                'div_id'        => 'slplus_navbar',
                'description'   => $this->plugin->AdminUI->create_Navbar(),
                'is_topmenu'    => true,
                'auto'          => false,
                'headerbar'     => false
            )
        );

        //-------------------------
        // General Settings
        //-------------------------
        $sectName = __('General Settings','csa-slp-pages');
        $this->Settings->add_section(
            array(
                    'name'          => $sectName,
                    'description'   => 'As of Store Locator Plus and Store Pages v3.9, the Store Pages license key is no longer needed.',
                    'auto'          => true
                )
         );

        // Checkboxes
        //
        $this->Settings->add_checkbox(
                $sectName,
                __('Pages Replace Websites', 'csa-slp-pages'),
                'pages_replace_websites',
                __('Use the Store Pages local URL in place of the website URL on the map results list.', 'csa-slp-pages'),
                $this->options['pages_replace_websites']
                );
        $this->Settings->add_checkbox(
                $sectName,
                __('Prevent New Window', 'csa-slp-pages'),
                'prevent_new_window',
                __('Prevent Store Pages web links from opening in a new window.', 'csa-slp-pages'),
                $this->options['prevent_new_window']
                );

        // Sliders
        //
        $this->Settings->add_slider(
                $sectName,
                __('Default Comments', 'csa-slp-pages'),
                'default_comments',
                __('Should comments be on or off by default when a new store page is created?', 'csa-slp-pages'),
                $this->options['default_comments']
                );
        $this->Settings->add_slider(
                $sectName,
                __('Default Trackbacks', 'csa-slp-pages'),
                'default_trackbacks',
                __('Should pingbacks/trackbacks be on or off by default when a new store page is created?', 'csa-slp-pages'),
                $this->options['default_trackbacks']
                );

        // Input Boxes
        //
        $this->Settings->add_input(
                $sectName,
                __('Permalink Starts With','csa-slp-pages'),
                'permalink_starts_with',
                __('Set the middle part of the store page URLs, defaults to "store_page".','csa-slp-pages'),
                $this->options['permalink_starts_with']
                );

        // Text Boxes
        //
        $theDescription = __('The HTML that is used to create new store pages.','csa-slp-pages')            ;
        $theDescription .=
            '<script language="JavaScript">'.
            'function resetPageTemplate() {'.
            "if (confirm('Reset?')) {".
            "jQuery('textarea[name=\"slp_storepages-page_template\"]').html('".esc_js($this->create_DefaultPageTemplate())."');".
            '}'.
            'return false;'.
            '}'.
            '</script>'
            ;

        $theDescription .=
            '<div class="form_entry">'.
                '<label for="csl-slplus-reset_results_string">&nbsp;</label>'.
                '<span name="csl-slplus-reset_results_string" style="cursor: pointer; text-decoration: underline; " onclick="resetPageTemplate();">'.
                    __('Reset to default.','csa-slp-pages') .
                '</span>' .
            '</div>'
            ;

        $this->Settings->add_textbox(
                $sectName,
                __('Page Template','csa-slp-pages'),
                'page_template',
                $theDescription,
                $this->options['page_template']
                );

        //------------------------------------------
        // RENDER
        //------------------------------------------
        $this->Settings->render_settings_page();
     }

    /**
     * Grab a copy of the incoming location data.
     *
     * @param type $locationData
     * @return type
     */
    function SetLocationProperties($locationData) {
        $this->currentLocationData = $locationData;
        return $locationData;
    }

    /**
     * Update Store Pages settings
     */
    function updateSettings() {
       if (!isset($_REQUEST['page']) || ($_REQUEST['page']!=$this->settingsSlug)) { return; }
       if (!isset($_REQUEST['_wpnonce'])) { return; }

       // Initialize inputs to '' if not set
       //
       $BoxesToHit = array(
           'default_comments',
           'default_trackbacks',
           'pages_replace_websites',
           'page_template',
           'permalink_starts_with',
           'prevent_new_window',
           );
       foreach ($BoxesToHit as $BoxName) {
           if (!isset($_REQUEST[$this->settingsSlug.'-'.$BoxName])) {
               $_REQUEST[$this->settingsSlug.'-'.$BoxName] = '';
           }
       }

       // Go update the local options setting
       //
       array_walk($_REQUEST,array($this,'setOptions'));

       update_option($this->settingsSlug.'-options', $this->options);
    }
}

// Start it up...
//
class SLPPages_Error extends WP_Error {}
add_action('init',array('SLPPages','init'));

