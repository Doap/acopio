<?php
/*
Plugin Name: Akismet Wedge Plugin
Plugin URI: http://www.BlogsEye.com/
Description: Globalizes the akismet api key so it can be used across blogs

Version: 0.9
Author: Keith P. Graham
Author URI: http://www.BlogsEye.com/

This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

$kpg_akwedge_semaphore=0;
if (function_exists('is_multisite') && is_multisite()) {
	global $blog_id;
	if (!empty($blog_id) && $blog_id!=1) {
		kpg_akwedge_global_setup();
		$wpcom_api_key=get_option('wordpress_api_key');
	}
}
function kpg_akwedge_global_setup() {
	global $blog_id;
	if ($blog_id==1) {
		return;
	}
	$ops=array('akismet_spam_count','akismet_show_user_comments_approved','akismet_available_servers','akismet_available_servers','akismet_discard_month','wordpress_api_key');
	foreach ($ops as $value) {
		add_filter('pre_update_option_'.$value,'kpg_akwedge_global_set',10,2);
		add_filter('add_option_'.$value,'kpg_akwedge_global_add',1,2);
		add_filter('delete_option_'.$value,'kpg_akwedge_global_delete');
		add_filter('pre_option_'.$value,'kpg_akwedge_global_get',1);	
	}
}
function kpg_akwedge_global_set($newvalue, $oldvalue) {
	if (!function_exists('switch_to_blog')) return $newvalue;
	global $kpg_akwedge_semaphore;
	if ($kpg_akwedge_semaphore) return $newvalue;
	$kpg_akwedge_semaphore++;
	$filt=current_filter();
	$f=substr($filt,strlen('pre_update_option_'));
	switch_to_blog(1);
	$ansa=update_option($f,$newvalue);
	restore_current_blog();
	$kpg_akwedge_semaphore--;
	return $oldvalue;  // returning the old value keeps the add from updating the current
}
function kpg_akwedge_global_add($option, $value) {
	if (!function_exists('switch_to_blog')) return false;
	global $kpg_akwedge_semaphore;
	if ($kpg_akwedge_semaphore) return false;
	$kpg_akwedge_semaphore++;
	$filt=current_filter();
	$f=substr($filt,strlen('add_option_'));
	switch_to_blog(1);
	$ansa=update_option($f,$value);
	restore_current_blog();
	$kpg_akwedge_semaphore--;
	return true; // functions.php ignores result anyway.
}
function kpg_akwedge_global_get($option) {
	if (!function_exists('switch_to_blog')) return false;
	global $kpg_akwedge_semaphore;
	if ($kpg_akwedge_semaphore) return false;
	$kpg_akwedge_semaphore++;
	$filt=current_filter();
	$f=substr($filt,strlen('pre_option_'));
	switch_to_blog(1);
	$ansa=get_option($f);
	restore_current_blog();
	$kpg_akwedge_semaphore--;
	return $ansa;
}
function kpg_akwedge_global_Delete($ops) {
	if (!function_exists('switch_to_blog')) return false;
	global $kpg_akwedge_semaphore;
	if ($kpg_akwedge_semaphore) return false;
	$kpg_akwedge_semaphore++;
	$filt=current_filter();
	$f=substr($filt,strlen('delete_option_'));
	switch_to_blog(1);
	$ansa=delete_option($ops);
	restore_current_blog();
	$kpg_akwedge_semaphore--;
	return $ansa;
}

// the rest of this is fluff
function kpg_akwedge_global_control()  {

if (!function_exists('is_multisite') || !is_multisite()) {
	echo "This plugin only works in MU installations";
	exit();
}
?>

<div class="wrap">
<h2>Akismet Wedge for MU</h2>
<h4>The Akismet Wedge for MU Plugin is working Correctly.</h4><div style="position:relative;float:right;width:35%;background-color:ivory;border:#333333 medium groove;padding:4px;margin-left:4px;">
    <p>This plugin is free and I expect nothing in return. If you would like to support my programming, you can buy my book of short stories.</p>
    <p>Some plugin authors ask for a donation. I ask you to spend a very small amount for something that you will enjoy. eBook versions for the Kindle and other book readers start at 99&cent;. The book is much better than you might think, and it has some very good science fiction writers saying some very nice things. <br/>
      <a target="_blank" href="http://www.blogseye.com/buy-the-book/">Error Message Eyes: A Programmer's Guide to the Digital Soul</a></p>
    <p>A link on your blog to one of my personal sites would also be appreciated.</p>
    <p><a target="_blank" href="http://www.WestNyackHoney.com">West Nyack Honey</a> (I keep bees and sell the honey)<br />
      <a target="_blank" href="http://www.cthreepo.com/blog">Wandering Blog</a> (My personal Blog) <br />
      <a target="_blank" href="http://www.cthreepo.com">Resources for Science Fiction</a> (Writing Science Fiction) <br />
      <a target="_blank" href="http://www.jt30.com">The JT30 Page</a> (Amplified Blues Harmonica) <br />
      <a target="_blank" href="http://www.harpamps.com">Harp Amps</a> (Vacuum Tube Amplifiers for Blues) <br />
      <a target="_blank" href="http://www.blogseye.com">Blog&apos;s Eye</a> (PHP coding) <br />
      <a target="_blank" href="http://www.cthreepo.com/bees">Bee Progress Beekeeping Blog</a> (My adventures as a new beekeeper) </p>
  </div>

<p>The Akismet Wedge for MU Plugin wedges between MU Blogs and Akismet providing the Akismet key and other data. MU blogs will be able to use the Akismet API key without having to configure each blog or edit any PHP files.</p>
<p>Note: the plugin must load before Akismet and most of the time this is not a problem. If you see the Akismet configuration warning on a blog dashboard, don't worry. The blog will still use Akismet correctly. </p>

</div>

<?php
}
function kpg_akwedge_global_init() {
   add_options_page('Akismet Wedge for MU', 'Akismet Wedge', 'manage_options','akismet_wedge','kpg_akwedge_global_control');
}
	add_action('admin_menu', 'kpg_akwedge_global_init');	



?>