<?php

if( !class_exists( 'umExecutionPageController' ) ) :
class umExecutionPageController {

    function __construct(){
        global $userMeta;
                    
        add_action( 'user_meta_load_admin_pages',   array( $this, 'loadExecutionPage' ) );
        add_filter( 'wp_list_pages_excludes',       array( $this, 'excludeExecutionPage' ), 50 );
        
        add_action( 'wp',                           array( $this, 'executionPage' ) );
        
        add_filter( 'logout_url',                   array( $this, 'logoutUrl' ), 10, 2 ); 
        add_filter( 'lostpassword_url',             array( $this, 'lostpasswordUrl' ), 10, 2 ); 
    }
    
    function loadExecutionPage(){
        global $userMeta;
        
        $userMeta->getExecutionPage( 'page_id' );
    }
    
    function excludeExecutionPage( $ids ){
        global $userMeta;
        
        $pageID = $userMeta->getExecutionPage( 'page_id' );
        if( is_array( $ids ) )
            array_push( $ids, $pageID );
        else
            $ids = array( $pageID );
        
        return $ids;
    }
    
    function executionPage(){
        global $userMeta, $post;
        
        if( ! is_page() ) return;
        
        $pageName = $userMeta->getExecutionPage( 'page_name' );
        if( $pageName <> $post->post_name ) return;
            
        $userMeta->enqueueScripts( array(
            'plugin-framework', 
            'user-meta',           
            'validationEngine',
            'password_strength',
        ) );                      
        $userMeta->runLocalization(); 

        $action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
        switch ($action) {
            
            case 'logout':
            	check_admin_referer('log-out');
                    
                $user = wp_get_current_user();
                if( empty( $user->ID ) )
                    return false;
                               
            	wp_logout();
                
                $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
                if( $userMeta->isFilterEnable( 'logout_redirect' ) )
                    $redirect_to = apply_filters('logout_redirect', $redirect_to, $redirect_to, $user);            
                
                if( !$redirect_to ){
                    $login = $userMeta->getSettings( 'login' );
                    $redirect_to = get_permalink( @$login[ 'login_page' ] );
                    if( !$redirect_to )
                        $redirect_to = home_url();
                }
                    
            	wp_redirect( $redirect_to );
            	exit();                
            break;  

            case 'email_verification' :
            case 'ev' :
                $config = $userMeta->getExecutionPageConfig( 'email_verification' );
                $post->post_title   = isset( $config['title'] ) ? $config['title'] : '';
                $post->post_content = $userMeta->emailVerification( $config );
            break;
        
            case 'resetpass' :
            case 'rp' :
                $config = $userMeta->getExecutionPageConfig( 'resetpass' );
                $post->post_title   = isset( $config['title'] ) ? $config['title'] : '';
                $post->post_content = $userMeta->resetPassword( $config );
            break; 

            default:
                $config = $userMeta->getExecutionPageConfig( 'lostpassword' );
                $config[ 'only_lost_pass_form' ] = true;
                $post->post_title   = isset( $config['title'] ) ? $config['title'] : '';
                $post->post_content = $userMeta->lostPasswordForm( $config ); 
            break;     
        }  
    }
    
    function logoutUrl( $logout_url, $redirect ){
        global $userMeta;
        
        $pageID = $userMeta->getExecutionPage( 'page_id' );
        
        if( !empty($pageID) ){       
            //$redirect   = $_SERVER['REQUEST_URI'];
            $redirect = $userMeta->getRedirectionUrl( $redirect, 'logout' );
        	$args = array( 'action' => 'logout' );               
        	if ( !empty($redirect) ) 
        		$args['redirect_to'] = urlencode( $redirect );       
            
            $url = get_permalink( $pageID );
            $url = add_query_arg( $args, $url);
            $logout_url = wp_nonce_url( $url, 'log-out' );
        }
     
        return $logout_url;                                    	        
    }
    
    function lostpasswordUrl( $lostpassword_url, $redirect ){
        global $userMeta;
        
        $pageID = $userMeta->getExecutionPage( 'page_id' );
        
        if( !empty($pageID) ){                     
        	$args = array( 'action' => 'lostpassword' );
        	if ( !empty($redirect) ) 
        		$args['redirect_to'] = $redirect;
        	
            $lostpassword_url = add_query_arg( $args, get_permalink( $pageID ) );  
        }            
       
        return $lostpassword_url;
    }
    
}
endif;