<?php
/*
  Plugin Name: Doapified Supercharged Gravatars
  Plugin URI:
  Description: This plugin brings Hovercard popups for your commenters and adds some cool fetures using gravatar.
  Version: 0.6
  Author: DOAP
  Author URI: http://www.doap.com/
	Text Domain: extended-gravatar
  Domain Path: /languages/
 */

//delete_option('extended_gravatar_options');
load_plugin_textdomain('extended-gravatar', null, dirname(plugin_basename(__FILE__)) . '/languages/');

function extended_gravatar_get_options() {
    $options = array(
        'plugin_version' => '0.5',
        'hovercard_enable' => 'true',
        'invitation_enable' => 'true',
        'gravatar_invitation_message' => extended_gravatar_get_default_invitation_message(),
    );
    $save_options = get_option('extended_gravatar_options');
    if (!empty($save_options)) {
        foreach ((array)$save_options as $key => $option)
            $options[$key] = $option;
    }
    update_option('extended_gravatar_options', $options);
    return $options;
}

function extended_gravatar_head() {
    if (!is_singular())
        return;

    $plugin_url = plugins_url('', __FILE__);
    ?>
    <link rel="stylesheet" type="text/css" id="gravatar-card-css" href="<?php echo $plugin_url; ?>/css/hovercard.css" />
    <link rel="stylesheet" type="text/css" id="gravatar-card-services-css" href="<?php echo $plugin_url; ?>/css/services.css" />
    <script type="text/javascript" language="javascript">
        var extended_gravatar_url = '<?php echo $plugin_url; ?>';
    </script>
    <?php
}

function extended_gravatar_scripts() {
    if (!is_singular())
        return;

    wp_enqueue_script('gprofiles', plugins_url('', __FILE__) . '/js/gprofiles.js', array('jquery'), 'e', true);
}

add_action('wp_head', 'extended_gravatar_head');
add_action('wp_enqueue_scripts', 'extended_gravatar_scripts');

/**
 * Adds a textbox to allow users to configure the invitation message
 * 
 * @since 0.5
 */
function extended_gravatar_options() {

    $options = extended_gravatar_get_options();
    print '<pre dir="ltr">';
//print_r($options);
    print '</pre>';
//die;

    if (isset($_POST['update_options'])) {

        $options['hovercard_enable'] = isset($_POST['hovercard_enable']) ? $_POST['hovercard_enable'] : 'false';
        $options['invitation_enable'] = isset($_POST['invitation_enable']) ? $_POST['invitation_enable'] : 'false';
        $options['gravatar_invitation_message'] = isset($_POST['gravatar_invitation_message']) ? stripcslashes($_POST['gravatar_invitation_message']) : '';

        update_option('extended_gravatar_options', $options);
        ?>
        <div class="updated">
            <p><strong><?php _e("Settings Saved.", "extended-gravatar"); ?></strong></p>
        </div>
        <?php
    }
    $options = extended_gravatar_get_options();
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2><?php _e('Enhanced Gravatar Options', 'extended-gravatar'); ?></h2>
        <form id="eg_options" class="validate" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" >

            <h3><?php _e('Gravatar Hovercards', 'extended-gravatar'); ?></h3>
            <p>
                <label><input name="hovercard_enable" value="true" type="checkbox" <?php if ($options['hovercard_enable'] == 'true')
        echo ' checked="checked" '; ?>/> <?php _e('Enable Gravatar Hovercards for avatars.', 'extended-gravatar'); ?></label> <br />
            </p>
            <h3><?php _e('Gravatar Invitation', 'extended-gravatar'); ?></h3>
            <p>
                <label><input name="invitation_enable" value="true" type="checkbox" <?php if ($options['invitation_enable'] == 'true')
        echo ' checked="checked" '; ?>/> <?php _e('Enable Gravatar invitation.', 'extended-gravatar'); ?></label> <br />
            </p>
            <p>
                <h4><?php _e('Customize the invitation message', 'extended-gravatar'); ?></h4>
                <textarea id="gravatar_invitation_message" name="gravatar_invitation_message" rows="10" cols="50" class="large-text"><?php echo esc_textarea($options['gravatar_invitation_message']); ?></textarea>
                <br />
                <label for="gravatar_invitation_message"><span class="description"><?php _e('Why not send your commenters a personalized message? You can use placeholders like COMMENTER_NAME, COMMENTER_EMAIL, COMMENTER_URL, SITE_URL, and POST_NAME. Make sure to include GRAVATAR_URL somewhere in the message!', 'extended-gravatar'); ?></span></label>
            </p>
            <div class="submit">
                <input class="button-primary" type="submit" name="update_options" value="<?php _e('Save Changes') ?>" />
            </div>
						<hr />
            <div>
                <h4><?php _e('My other plugins for wordpress:', 'extended-gravatar'); ?></h4>
                <ul>
										<li><b>- <?php _e('Google Transliteration ', 'extended-gravatar'); ?></b>
											(<a href="http://wordpress.org/extend/plugins/google-transliteration/"><?php _e('Download', 'extended-gravatar'); ?></a> | 
											<a href="<?php _e('http://www.moallemi.ir/en/blog/2009/10/10/google-transliteration-for-wordpress/', 'extended-gravatar'); ?>"><?php _e('More Information', 'extended-gravatar'); ?></a>)
										</li>
                    <li><b>- <?php _e('Google Reader Stats ', 'extended-gravatar'); ?></b>
        							(<a href="http://wordpress.org/extend/plugins/google-reader-stats/"><?php _e('Download', 'extended-gravatar'); ?></a> | 
                        <a href="<?php _e('http://www.moallemi.ir/en/blog/2010/06/03/google-reader-stats-for-wordpress/', 'extended-gravatar'); ?>"><?php _e('More Information', 'extended-gravatar'); ?></a>)
                    </li>
										<li><b>- <?php _e('Likekhor ', 'extended-gravatar'); ?></b>
											(<a href="http://wordpress.org/extend/plugins/wp-likekhor/"><?php _e('Download', 'extended-gravatar'); ?></a> | 
											<a href="<?php _e('http://www.moallemi.ir/blog/1389/04/30/%D9%85%D8%B9%D8%B1%D9%81%DB%8C-%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%84%D8%A7%DB%8C%DA%A9-%D8%AE%D9%88%D8%B1-%D9%88%D8%B1%D8%AF%D9%BE%D8%B1%D8%B3/', 'google-reader-stats'); ?>"><?php _e('More Information', 'extended-gravatar'); ?></a>)
										</li>
                    <li><b>- <?php _e('Advanced User Agent Displayer ', 'extended-gravatar'); ?></b>
        							(<a href="http://wordpress.org/extend/plugins/advanced-user-agent-displayer/"><?php _e('Download', 'extended-gravatar'); ?></a> | 
                        <a href="<?php _e('http://www.moallemi.ir/en/blog/2009/09/20/advanced-user-agent-displayer/', 'extended-gravatar'); ?>"><?php _e('More Information', 'extended-gravatar'); ?></a>)
                    </li>
                    <li><b>- <?php _e('Behnevis Transliteration ', 'extended-gravatar'); ?></b> 
        							(<a href="http://wordpress.org/extend/plugins/behnevis-transliteration/"><?php _e('Download', 'extended-gravatar'); ?></a> | 
                        <a href="http://www.moallemi.ir/blog/1388/07/25/%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%86%D9%88%DB%8C%D8%B3%D9%87-%DA%AF%D8%B1%D8%AF%D8%A7%D9%86-%D8%A8%D9%87%D9%86%D9%88%DB%8C%D8%B3-%D8%A8%D8%B1%D8%A7%DB%8C-%D9%88%D8%B1%D8%AF%D9%BE%D8%B1%D8%B3/"><?php _e('More Information', 'extended-gravatar'); ?></a> )
                    </li>
                    <li><b>- <?php _e('Comments On Feed ', 'extended-gravatar'); ?></b> 
        							(<a href="http://wordpress.org/extend/plugins/comments-on-feed/"><?php _e('Download', 'extended-gravatar'); ?></a> | 
                        <a href="<?php _e('http://www.moallemi.ir/en/blog/2009/12/18/comments-on-feed-for-wordpress/', 'extended-gravatar'); ?>"><?php _e('More Information', 'extended-gravatar'); ?></a>)
                    </li>
                    <li><b>- <?php _e('Feed Delay ', 'extended-gravatar'); ?></b> 
        							(<a href="http://wordpress.org/extend/plugins/feed-delay/"><?php _e('Download', 'extended-gravatar'); ?></a> | 
                        <a href="<?php _e('http://www.moallemi.ir/en/blog/2010/02/25/feed-delay-for-wordpress/', 'extended-gravatar'); ?>"><?php _e('More Information', 'extended-gravatar'); ?></a>)
                    </li>
                    <li><b>- <?php _e('Contact Commenter ', 'extended-gravatar'); ?></b> 
        							(<a href="http://wordpress.org/extend/plugins/contact-commenter/"><?php _e('Download', 'extended-gravatar'); ?></a> | 
                        <a href="<?php _e('http://www.moallemi.ir/blog/1388/12/27/%d9%87%d8%af%db%8c%d9%87-%da%a9%d8%a7%d9%88%d8%b4%da%af%d8%b1-%d9%85%d9%86%d8%a7%d8%b3%d8%a8%d8%aa-%d8%b3%d8%a7%d9%84-%d9%86%d9%88-%d9%88%d8%b1%d8%af%d9%be%d8%b1%d8%b3/', 'extended-gravatar'); ?>"><?php _e('More Information', 'extended-gravatar'); ?></a>)
                    </li>
                </ul>
            </div>
        </form>
    </div>
    <?php
}

function extended_gravatar_admin_menu() {
    add_options_page(__('Extended Gravatar', 'extended-gravatar'), __('Extended Gravatar', 'extended-gravatar'), 10, 'extended-gravatar-options', 'extended_gravatar_options');
}

add_action('admin_menu', 'extended_gravatar_admin_menu');

/**
 * Handle when new comments are created.
 * We have to hook into wp_insert_comment too because it doesn't call transition_comment_status :( 
 *
 * @since 0.5
 * @param mixed $id
 * @param mixed $comment
 */
function extended_gravatar_insert_comment($id, $comment) {

    $comment_status = $comment->comment_approved;

    // We only send emails for approved comments
    if (empty($comment_status) || !in_array($comment_status, array(1, '1', 'approved')))
        return;

    extended_gravatar_notify_commenter($comment->comment_author_email, $comment);
}

add_action('wp_insert_comment', 'extended_gravatar_insert_comment', 10, 2);

/**
 * Handle when new comments are updated or approved.
 * 
 * @since 0.5
 * @param mixed $new_status
 * @param mixed $old_status
 * @param mixed $comment
 */
function extended_gravatar_transition_comment($new_status, $old_status, $comment) {

		

    // We only send emails for approved comments
    if ('approved' != $new_status || 'approved' == $old_status)
        return;

    // Only send emails for comments less than a week old
    //if (get_comment_date('U', $comment->comment_ID) < strtotime(apply_filters('extended_gravatar_invitation_time_limit', '-1 week')))
    //    return;

		
    extended_gravatar_notify_commenter($comment->comment_author_email, $comment);
}

add_action('transition_comment_status', 'extended_gravatar_transition_comment', 10, 3);

/**
 * Send gravatar invitation to commenters if enabled, if they don't have a gravatar and we haven't notified them already.
 * 
 * @since 0.5
 * @param mixed $email
 * @param mixed $comment
 */
function extended_gravatar_notify_commenter($email, $comment) {

    // Check that it's a comment and that we have an email address
    if (!in_array($comment->comment_type, array('', 'comment')) || !$email)
        return;

    $post = get_post($comment->comment_post_ID);

		
    if (!extended_gravatar_email_has_gravatar($email) && !extended_gravatar_have_notified_commenter($email)) {

        if (is_multisite())
            $sitename = get_current_site()->site_name;
        else
            $sitename = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $subject = sprintf(__('[%s] Gravatar Invitation', 'extended-gravatar'), $sitename);
        $subject = apply_filters('extended_gravatar_invitation_subject', $subject, $comment);

        $options = extended_gravatar_get_options();
        $message = stripslashes($options['gravatar_invitation_message']);

        if ($message == false)
            $message = extended_gravatar_get_default_invitation_message();

        // Just in case we're missing the signup URL
        if (strpos($message, 'GRAVATAR_URL') === false)
            $message .= "\n\n" . __('Sign up now: ', 'extended-gravatar') . 'GRAVATAR_URL';

        // TODO: Need a better way to handle these for i18n since this does not translate well.
        $message = str_replace('SITE_NAME', $sitename, $message);
        $message = str_replace('POST_NAME', $post->post_title, $message);
        $message = str_replace('COMMENTER_NAME', $comment->comment_author, $message);
        $message = str_replace('COMMENTER_EMAIL', $email, $message);
        $message = str_replace('COMMENTER_URL', $comment->comment_author_url, $message);
        $message = str_replace('GRAVATAR_URL', 'http://www.gravatar.com/signup', $message);

        // Grab author of the post
        $post_author = get_userdata($post->post_author);

        // Set From header to SITE_NAME
        $wp_email = 'wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));

        // If the post author has a valid email, set the reply to the email 'from' them.
        $reply_name = !empty($post_author->user_email) ? $post_author->display_name : $sitename;
        $reply_email = !empty($post_author->user_email) ? $post_author->user_email : get_option('admin_email');

        $message_headers = array(
            'from' => sprintf('From: "%1$s" <%2$s>', $sitename, $wp_email),
            'type' => sprintf('Content-Type: %1$s; charset="%2$s"', 'text/plain', get_option('blog_charset')),
            'replyto' => sprintf('Reply-To: %1$s <%2$s>', $reply_name, $reply_email),
        );

        // Pass through filters
        $message = apply_filters('extended_gravatar_invitation_message', $message, $comment);
        $message_headers = apply_filters('extended_gravatar_invitation_message_headers', $message_headers, $comment);
        $message_headers = implode("\n", $message_headers);

        wp_mail($email, $subject, $message, $message_headers);

        extended_gravatar_set_notified_commenter($email, $comment);
    }
}

/**
 * Mark the commenter as notified.
 * 
 * @since 0.5
 * @param mixed $email
 */
function extended_gravatar_set_notified_commenter($email, $comment) {
    update_metadata('comment', $comment->comment_ID, extended_gravatar_get_notify_key($email), 1);
}

/**
 * Check to see if we've notified the commenter already.
 * 
 * @since 0.5
 * @param mixed $email
 * @return bool
 */
function extended_gravatar_have_notified_commenter($email) {
    global $wpdb;
    $table = _get_meta_table('comment');
    return $wpdb->get_var($wpdb->prepare("SELECT meta_id FROM {$table} WHERE meta_key = %s LIMIT 1", extended_gravatar_get_notify_key($email)));
}

/**
 * Build the key we use to store comment notifications.
 * 
 * @since 0.5
 * @param mixed $email
 * @return string
 */
function extended_gravatar_get_notify_key($email) {
    return sprintf('gravatar_invite_%s', md5(strtolower($email)));
}

/**
 * The default invitation message
 * 
 * @since 0.5
 * @return string
 */
function extended_gravatar_get_default_invitation_message() {
    return stripslashes(__('Hi COMMENTER_NAME!

Thanks for your comment on "POST_NAME"!

I noticed that you didn\'t have your own picture or profile next to your comment. Why not set one up using Gravatar? Click the link below to get started:

GRAVATAR_URL

*What\'s a Gravatar?* 
Your Gravatar (a Globally Recognized Avatar) is an image that follows you from site to site appearing beside your name when you do things like comment or post on a blog. Avatars help identify your posts on blogs and web forums, so why not on any site?

Thanks for visiting and come back soon!

-- The Team @ SITE_NAME', 'extended-gravatar'));
}

/**
 * Checks to see if a given email has an associated gravatar.
 * 
 * @since 0.5
 * @param mixed $email
 * @return bool
 */
function extended_gravatar_email_has_gravatar($email) {
    if (empty($email))
        return false;

    $email_hash = md5(strtolower($email));

    if (is_ssl())
        $host = 'https://secure.gravatar.com';
    else
        $host = sprintf("http://%d.gravatar.com", ( hexdec($email_hash[0]) % 2));

    $url = sprintf('%s/avatar/%s?d=404', $host, $email_hash);
    $request = new WP_Http();
    $result = $request->request($url, array('method' => 'GET'));

    // If gravatar returns a 404, email doesn't have a gravatar attached
    if (is_array($result) && isset($result['response']['code']) && $result['response']['code'] == 404)
        return false;

    // For all other cases, let's assume we do
    return true;
}

__('This plugin brings Hovercard popups for your commenters and adds some cool fetures using gravatar.', 'extended-gravatar');
__('Reza Moallemi', 'extended-gravatar');

?>
