<?php
global $userMeta;
// Expected: $config, $error

$html = null;

$formID     = !empty( $config['form_id'] ) ? $config['form_id'] : "um_resetpassword_form";
$formClass  = !empty( $config['form_class'] ) ? $config['form_class'] : '';

if( isset( $config['before_form'] ) )
    $html .= $config['before_form'];

$html .= "<form id=\"$formID\" class=\"$formClass\" method=\"post\" >";

if( isset( $config['heading'] ) ){
    if( !empty( $config['heading'] ) )
        $html .= "<h2>" . $config['heading'] . "</h2>";
}else
    $html .= "<h2>" . __( 'Reset Password', $userMeta->name ) . "</h2>";
    

if( isset( $config['intro_text'] ) ){
    if( !empty( $config['intro_text'] ) )
        $html .= "<p>" . $config['intro_text'] . "</p>";
}else
    $html .= "<p>" . __( 'Enter your new password below.', $userMeta->name ) . "</p>";


if( is_wp_error( $error ) )
    $html .= $userMeta->showError( $error->get_error_message(), false );

$html .= $userMeta->createInput( 'pass1', 'password', array(
    'label'         => !empty( $config['pass1_label'] ) ? $config['pass1_label'] : __( 'New password', $userMeta->name ),
    'id'            => !empty( $config['pass1_id'] ) ? $config['pass1_id'] : 'um_pass1',
    'class'         => !empty( $config['pass1_class'] ) ? $config['pass1_class'] . ' ' : '' . 'um_input pass_strength validate[required]',
    'label_class'   => !empty( $config['pass1_label_class'] ) ? $config['pass1_label_class'] : 'pf_label',
    'autocomplete'  => 'off',
    'enclose'       => 'p',
) );

$html .= $userMeta->createInput( 'pass2', 'password', array(
    'label'         => !empty( $config['pass2_label'] ) ? $config['pass2_label'] : __( 'Confirm new password', $userMeta->name ),
    'id'            => !empty( $config['pass2_id'] ) ? $config['pass2_id'] : 'um_pass2',
    'class'         => !empty( $config['pass2_class'] ) ? $config['pass2_class'] . ' ' : '' . 'um_input validate[required,equals[um_pass1]]',
    'label_class'   => !empty( $config['pass2_label_class'] ) ? $config['pass2_label_class'] : 'pf_label',
    'autocomplete'  => 'off',
    'enclose'       => 'p',
) );

$html .= $userMeta->nonceField();

if( isset( $config['before_button'] ) )
    $html .= $config['before_button'];

$html .= $userMeta->createInput( 'login', 'submit', array(
    'value'     => !empty( $config['button_value'] ) ? $config['button_value'] : __( 'Reset Password', $userMeta->name ),
    'id'        => !empty( $config['input_id'] ) ? $config['input_id'] : 'um_resetpassword_button',
    'class'     => !empty( $config['button_class'] ) ? $config['button_class'] : '',
    'enclose'   => 'p',
) );

if( isset( $config['after_button'] ) )
    $html .= $config['after_button'];

$html .= "</form>";

if( isset( $config['after_form'] ) )
    $html .= $config['after_form'];


$html .= "<script type=\"text/javascript\">";
    $html .= "jQuery(document).ready(function(){";
        $html .= "jQuery(\"#$formID\").validationEngine();";
        $html .= "jQuery(\".pass_strength\").password_strength();";
    $html .= "});";
$html .= "</script>";

?>