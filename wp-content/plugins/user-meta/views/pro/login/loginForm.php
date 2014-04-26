<?php
global $userMeta;
// Expected: $config, $loginTitle, $disableAjax, $methodName

$uniqueID = isset( $config['unique_id'] ) ? $config['unique_id'] : rand(0,99);

$onSubmit = $disableAjax ? null : "onsubmit=\"umLogin(this); return false;\"";

if( isset( $config['before_form'] ) )
    $html .= $config['before_form'];

$formID     = !empty( $config['form_id'] ) ? $config['form_id'] : "um_login_form$uniqueID";
$formClass  = isset( $config['form_class'] ) ? $config['form_class'] : 'um_login_form';

$html = "<form id=\"$formID\" class=\"$formClass\" method=\"post\" $onSubmit >";

$html .= $userMeta->createInput( 'user_login', 'text', array(
    'value'         => isset( $_REQUEST['user_login'] ) ? stripslashes( $_REQUEST['user_login'] ) : '',
    'label'         => !empty( $config['login_label'] ) ? $config['login_label'] : $loginTitle,
    'id'            => !empty( $config['login_id'] ) ? $config['login_id'] : 'user_login' . $uniqueID,
    'class'         => !empty( $config['login_class'] ) ? $config['login_class'] : 'um_login_field um_input',
    'label_class'   => !empty( $config['login_label_class'] ) ? $config['login_label_class'] : 'pf_label',
    'enclose'       => 'p',
) );

$html .= $userMeta->createInput( 'user_pass', 'password', array(
    'label'         => !empty( $config['pass_label'] ) ? $config['pass_label'] : __( 'Password', $userMeta->name ), 
    'id'            => !empty( $config['pass_id'] ) ? $config['pass_id'] : 'user_pass' . $uniqueID,
    'class'         => !empty( $config['pass_class'] ) ? $config['pass_class'] : 'um_pass_field um_input',
    'label_class'   => !empty( $config['pass_label_class'] ) ? $config['pass_label_class'] : 'pf_label',
    'enclose'       => 'p',
) );            

$html .= $userMeta->createInput( 'remember', 'checkbox', array(    
    'value'         => isset( $_REQUEST['remember'] ) ? true : false,
    'label'         => !empty( $config['remember_label'] ) ? $config['remember_label'] : __( 'Remember Me', $userMeta->name ),
    'id'            => !empty( $config['remember_id'] ) ? $config['remember_id'] : 'remember' . $uniqueID,
    'class'         => !empty( $config['remember_class'] ) ? $config['remember_class'] : 'um_remember_field',
    'enclose'       => 'p',
) );    

//$html .= "<input type='hidden' name='action' value='um_login' />";
//$html .= "<input type='hidden' name='action_type' value='login' />";


$html .= $userMeta->methodPack( $methodName );

if( !empty( $_REQUEST['redirect_to'] ) ){
    $html .= $userMeta->createInput( 'redirect_to', 'hidden', array(    
        'value'     => $_REQUEST['redirect_to']
    ) ); 
}    

if( isset( $config['before_button'] ) )
    $html .= $config['before_button'];
    
$html .= $userMeta->createInput( 'login', 'submit', array(
    'value'     => !empty( $config['button_value'] ) ? $config['button_value'] : __( 'Login', $userMeta->name ),
    'id'        => !empty( $config['input_id'] ) ? $config['input_id'] : 'um_login_button' . $uniqueID,
    'class'     => !empty( $config['button_class'] ) ? $config['button_class'] : 'um_login_button',
    'enclose'   => 'p',
) );

if( isset( $config['after_button'] ) )
    $html .= $config['after_button'];

$html .= "</form>"; 

if( isset( $config['after_form'] ) )
    $html .= $config['after_form'];

?>