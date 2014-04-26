<?php
/*
Plugin Name: Doaps Facebook Like And Comment Solution
Plugin URI: http://www.sulhansetiawan.com/fblike-comment
Description: Facebook Like And Comment add like/recommend button, also send button, and optionally use facebook comment instead of wordpress comment for your wordpress site.
Author: Sulhan Setiawan
Author URI: http://www.sulhansetiawan.com
Version: 1.0.0
Tags: comment, facebook, social plugin
*/

class Facebook_Like_And_Comment {
	var $facebook_like_og_image;
	var $facebook_like_fb_admins;
	var $facebook_like_fb_app_id;
	var $facebook_like_like_width;
	var $facebook_like_like_use_recommend;
	var $facebook_like_comment_width;
	var $facebook_like_comment_num;
	var $facebook_like_dark_color;
	var $facebook_comment_dark_color;
	var $facebook_like_use_fb_comment;
	var $thumbnail_url;

	//constructor of class, PHP4 compatible construction for backward compatibility
	function Facebook_Like_And_Comment() {
	    $this->thumbnail_url=plugins_url('/thumb.php' , __FILE__)."?size=100&img=";
		add_action('admin_menu', array(&$this, 'on_admin_menu'));
		add_action('admin_init', array(&$this, 'fb_image_meta'));
		add_action('get_header', array(&$this, 'facebook_modify_html'));
		add_action('wp_head', array(&$this, 'facebook_like_add_meta'));
		add_action('get_footer', array(&$this, 'facebook_like_add_script'));
		add_action('init', array(&$this, 'facebook_comment_cek'));
		add_action('wp_ajax_fb_like_comment_update_opt', array(&$this,'fb_like_comment_update_opt'));
		add_action('wp_ajax_fb_like_comment_set_fbImage', array(&$this,'fb_like_comment_set_fbImage'));
		add_action('wp_ajax_fb_like_comment_remove_fbImage', array(&$this,'fb_like_comment_remove_fbImage'));

		add_filter('the_content',array(&$this, 'facebook_add_like'),10);
		add_filter('comments_template',array(&$this, 'facebook_comments_template'),10);
		add_filter('comments_number',array(&$this, 'facebook_comments_number'),10,2);	

		$this->facebook_like_og_image=get_option("facebook_like_og_image");
		$this->facebook_like_fb_admins=get_option("facebook_like_fb_admins");
		$this->facebook_like_fb_app_id=get_option("facebook_like_fb_app_id");
		$this->facebook_like_like_width=get_option("facebook_like_like_width");
		$this->facebook_like_like_use_recommend=get_option("facebook_like_like_use_recommend");
		$this->facebook_like_comment_width=get_option("facebook_like_comment_width");
		$this->facebook_like_comment_num=get_option("facebook_like_comment_num");
		$this->facebook_like_dark_color=get_option("facebook_like_dark_color");
		$this->facebook_comment_dark_color=get_option("facebook_comment_dark_color");
		$this->facebook_like_use_fb_comment=get_option("facebook_like_use_fb_comment");		
	}

	//extend the admin menu
	function on_admin_menu() {
		//add our own option page, you can also add it to different sections or use your own one
		$this->pagehook = add_options_page('Facebook Like & Comment Options', "FB Like&Comment", 'manage_options', 'facebook-like-and-comment-setting', array(&$this, 'on_show_page'));
		//register  callback gets call prior your own page gets rendered
		add_action('load-'.$this->pagehook, array(&$this, 'on_load_page'));
	}

	//will be executed if wordpress core detects this page has to be rendered
	function on_load_page() {
		//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		wp_enqueue_script('jquery-form');

		//add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore
		add_meta_box('facebook_like_and_comment_donate', 'Donate', array(&$this, 'on_donate_content'), $this->pagehook, 'normal', 'core');
		add_meta_box('facebook_like_and_comment_appid', 'Facebook ID', array(&$this, 'on_appid_content'), $this->pagehook, 'normal', 'core');
		add_meta_box('facebook_like_and_comment_defimg', 'Default Image', array(&$this, 'on_defimg_content'), $this->pagehook, 'normal', 'core');
		add_meta_box('facebook_like_and_comment_optlike', 'Like Button Option', array(&$this, 'on_optlike_content'), $this->pagehook, 'normal', 'core');
		add_meta_box('facebook_like_and_comment_optcomment', 'Comment Option', array(&$this, 'on_optcomment_content'), $this->pagehook, 'normal', 'core');
	}

	//executed to show the plugins complete admin page
	function on_show_page() {
		if (!current_user_can('manage_options')){
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		//we need the global screen column value to beable to have a sidebar in WordPress 2.8
		global $screen_layout_columns;
		
		?>
		<div id="facebook-like-and-comment" class="wrap">
			<style>.leftlabel{display:block;width:150px;float:left} .righvalue{width:350px;} .checkwidth{width:25px;}</style>
			<?php screen_icon('options-general'); ?>
			<h2>Facebook Like and Comment</h2>
			<p>Thank you for using this plugin. If you have any question about this plugin, please visit <a href="http://www.sulhansetiawan.com/fblike-comment">here</a></p>
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" onsubmit='return postFbLikeCmtOpt(event)' >
				<?php wp_nonce_field('facebook-like-and-comment'); ?>
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
				<input type="hidden" name="action" value="save_facebook-like-and-comment" />
			</form>
			<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
				<div id="side-info-column" class="inner-sidebar">
					<?php /*do_meta_boxes($this->pagehook, 'side', $data);*/ ?>
				</div>
				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<?php do_meta_boxes($this->pagehook, 'normal', $data); ?>
					</div>
				</div>
				<br class="clear"/>
			</div>	
		</div>
		<script type="text/javascript">
			//<![CDATA[		
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			var waitImg="<img src='/wp-admin/images/loading.gif'/>";
			var options = { 
				beforeSubmit:	showWait,
				success:		showResponse,
				url:			ajaxurl
			};		 
			// bind to the form's submit event 
			jQuery('#form_fb_app_id').submit(function(){ 
				jQuery(this).ajaxSubmit(options); 	
				return false; 
			});
			jQuery('#form_fb_optlike').submit(function(){ 
				jQuery(this).ajaxSubmit(options); 	
				return false; 
			});
			jQuery('#form_fb_optcomment').submit(function(){ 
				jQuery(this).ajaxSubmit(options); 	
				return false; 
			});
			options.success=refreshImage;
			jQuery('#form_fb_defimg').submit(function(){ 
				jQuery(this).ajaxSubmit(options); 	
				return false; 
			});
			// pre-submit callback 
			function showWait(formData, jqForm, options){ 
				jqForm.children('#board').html("<div class='updated fade'>please wait... "+waitImg+"</div>");				
				//var queryString = jQuery.param(formData);
				//alert('About to submit: \n\n' + queryString);
				return true; 
			}
			// post-submit callback 
			function showResponse(responseText, statusText, xhr, $form){
				$form.children('#board').html(responseText);
			}
			function refreshImage(responseText, statusText, xhr, $form){
				$form.children('#board').html(responseText);
				var imgx=jQuery('#fb_og_image').val();
				if(imgx=='')jQuery('#fbImage-image').html('');
				else jQuery('#fbImage-image').html("<img src='<?php echo $this->thumbnail_url;?>"+imgx+"'>");
			}
			
			function postFbLikeCmtOpt(e){
				e.returnValue = false;
				var data={
						action:'fb_like_comment_update_opt',
						fb_og_image: jQuery('#fb_og_image').val(),
						fb_admins: jQuery('#fb_admins').val(),
						fb_app_id: jQuery('#fb_app_id').val(),
						fb_like_width: jQuery('#fb_like_width').val(),
						fb_comment_width: jQuery('#fb_comment_width').val(),
						fb_comment_num: jQuery('#fb_comment_num').val()
					};
				if(jQuery("#use_fb_recommend").attr('checked'))data["use_fb_recommend"]='';
				if(jQuery("#fb_dark_color").attr('checked'))data["fb_dark_color"]='';
				if(jQuery("#use_fb_comment").attr('checked'))data["use_fb_comment"]='';
				if(jQuery("#fb_comment_dark_color").attr('checked'))data["fb_comment_dark_color"]='';

				jQuery("#MessageBoard").html("<div class='updated fade'>please wait... "+waitImg+"</div>");
				jQuery.post(ajaxurl, data, function(response){	
					jQuery("#MessageBoard").html(response);
					if(response=="<?php echo "<div class='updated fade'>".__('Options Updated')."</div>"; ?>"){
						if(jQuery('#fb_og_image').val()!='')
							jQuery("#fbImage-image").html("<img src='<?php echo $this->thumbnail_url;?>"+jQuery('#fb_og_image').val()+"'>");
						else jQuery("#fbImage-image").html('');
					}
				});
				return false;
			}
			//]]>
		</script><?php
	}
	function on_donate_content(){ ?>
		<p>If you like this plugin, then you may give a litle donation. But you don't have to do this. Please feel free to use this plugin freely.</p>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="ZU6G6L7UVAE9C">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
		<?php
	}
	private function openForm($formId){ ?>
		<form action='#' method='post' id='<?php echo $formId;?>'>
		<input type='hidden' name='action' value='fb_like_comment_update_opt'/> <?php	
	}
	private function makeForm($labelName,$type,$name_id,$valueVar,$cekLabel='.'){// style='display:block;width:525px;'
		$output="<div><span class='leftlabel'>$labelName</span><input type='$type' ";
		if($type=='checkbox'){
			$output.="value='' class='checkwidth' ";
			if($valueVar)$output.="checked='true' ";
		}else $output.="value='$valueVar' class='righvalue' ";//size='100' 
		$output.="id='$name_id' name='$name_id' />";
		if($type=='checkbox')$output.="$cekLabel";
		echo $output."<br></div>\n";
	}
	private function closeForm(){ ?>
		<p><input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
		<input type="reset" value="Reset" class="button-primary" name="Reset"/></p>
		<div id='board' name='board'></div>
		</form><?php
	}	
	function on_appid_content(){
		$this->openForm('form_fb_app_id');?>
		<p>If you want to be able to moderate the facebook comment, then you must specify the admin for your site by entering your Facebook ID here. You may enter more than one ID separated by comma.</p>
		Your facebook account ID
		<?php $this->makeForm('fb:admins','text','fb_admins',$this->facebook_like_fb_admins);?>
		<p>If you don't know your Facebook ID, you can click the login button below</p>
		<iframe src="http://fb.sulhansetiawan.com/getid.php" width='400px' height='130px'></iframe><hr>
		Your facebook applicaion ID
		<?php $this->makeForm('fb:app_id','text','fb_app_id',$this->facebook_like_fb_app_id);
		$this->closeForm();
	}
	function on_defimg_content(){
		$this->openForm('form_fb_defimg');?>
		Specify the image to be shown on visitor wall when they click Like/Recommend button<br>
		This image will be used if the post/page doesn't specify its image. To specify image for individual post, add a fbImage postmeta with image url for its value.
		<?php $this->makeForm('og:image','text','fb_og_image',$this->facebook_like_og_image);
		echo "<div id='fbImage-image' name='fbImage-image'>";
		if(!empty($this->facebook_like_og_image)){
			echo "<img src='".$this->thumbnail_url.$this->facebook_like_og_image."'>";
		}
		echo "</div>";
		$this->closeForm();
	}
	function on_optlike_content(){
		$this->openForm('form_fb_optlike');?>
		Specify the width of Like plugin on your site
		<?php $this->makeForm('like width','text','fb_like_width',$this->facebook_like_like_width);?><hr>
		If checked, Recommend button will be used instead of Like button
		<?php $this->makeForm('like action','checkbox','use_fb_recommend',$this->facebook_like_like_use_recommend,'recommend');?><br>
		If checked, the Like plugin will use dark color
		<?php $this->makeForm('color scheme ','checkbox','fb_dark_color',$this->facebook_like_dark_color,'dark color');
		$this->closeForm();
	}
	function on_optcomment_content(){
		$this->openForm('form_fb_optcomment');?>
		If checked, your site will use the facebook comment plugin instead of wordpress comment
		<?php $this->makeForm('comment','checkbox','use_fb_comment',$this->facebook_like_use_fb_comment,'use facebook');?><hr>
		Specify the width of facebook comment plugin on your site
		<?php $this->makeForm('comment width','text','fb_comment_width',$this->facebook_like_comment_width);?><hr>
		Specify the number of visible comment
		<?php $this->makeForm('comment number','text','fb_comment_num',$this->facebook_like_comment_num);?><hr>
		If checked, the facebook comment plugin will use dark color
		<?php $this->makeForm('color scheme ','checkbox','fb_comment_dark_color',$facebook_comment_dark_color,'dark color');
		$this->closeForm();
	}
	function getUrlThis(){
		global $q_config;
		if($q_config){
			$lg=$q_config['default_language'];
			$urlthis=qtrans_convertURL(qtrans_realURL(),$lg);
		}else $urlthis=get_permalink($post->ID);	
		return $urlthis;
	}	
	function header_callback($buffer)
	{
		$mcnt=preg_match_all('@<html[\/\!]*?[^<>]*?>@si',$buffer,$match);
		$match=$match[0];
		$match0=$match;
		for($i=0;$i<$mcnt;$i++)$match0[$i]='@'.preg_quote($match0[$i],'@').'@si';
		for($i=0;$i<$mcnt;$i++){
			$match[$i]=substr($match[$i],0,strlen($match[$i])-1);
			$patrn1='xmlns="http://www.w3.org/1999/xhtml"';
			$patrn2='xmlns:og="http://ogp.me/ns#"';
			$patrn3='xmlns:fb="https://www.facebook.com/2008/fbml"';
			if(preg_match('@'.$patrn1.'@si',$match[$i])==0)$match[$i].=" ".$patrn1;
			if(preg_match('@'.$patrn2.'@si',$match[$i])==0)$match[$i].=" ".$patrn2;
			if(preg_match('@'.$patrn3.'@si',$match[$i])==0)$match[$i].=" ".$patrn3;
			$match[$i].='>';
		}
		return (preg_replace($match0, $match, $buffer));
	}
	//action to take
	function facebook_modify_html(){
		//if(is_single()||is_page())
		ob_start(array(&$this,"header_callback"));
	}
	function facebook_add_like($content){
		$cnt=$content;
		if(is_single()||is_page()){
			if($this->facebook_like_like_use_recommend)$reco=' data-action="recommend"';else $reco='';
			if($this->facebook_like_dark_color)$use_dark=' data-colorscheme="dark"';else $use_dark='';
			$cnt.='<div style="display:block;margin-left:auto;margin-right:auto;padding:20px 5px 20px 5px"><div class="fb-like" data-href="'.$this->getUrlThis().'" data-send="true" data-width="'.
			$this->facebook_like_like_width.'" data-show-faces="true"'.$reco.$use_dark.'></div></div>';
		}
		return $cnt;
	}
	function facebook_comments_number($output, $number){
		if($this->facebook_like_use_fb_comment)return '<iframe src="http://www.facebook.com/plugins/comments.php?href='.$this->getUrlThis().'&permalink=1" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:130px; height:16px;" allowTransparency="true"></iframe>';
		else return $output;
		//return '<fb:comments-count href='.get_permalink( $post->ID ).'></fb:comments-count> comments';
	}
	/**
	 * Generates an excerpt from the content, if needed.
	 *
	 * @param int|object $post_or_id can be the post ID, or the actual $post object itself
	 * @param string $excerpt_more the text that is applied to the end of the excerpt if we algorithically snip it
	 * @return string the snipped excerpt or the manual excerpt if it exists         
	 */
	function zg_trim_excerpt__($post_or_id, $excerpt_more = ' [...]') {
		if ( is_object( $post_or_id ) ) $postObj = $post_or_id;
		else $postObj = get_post($post_or_id);

		$raw_excerpt = $text = $postObj->post_excerpt;
		if ( '' == $text ) {
			$text = $postObj->post_content;

			$text = strip_shortcodes( $text );

			$text = apply_filters('the_content', $text);
			$text = str_replace(']]>', ']]&gt;', $text);
			$text = strip_tags($text);
			$excerpt_length = apply_filters('excerpt_length', 55);

			// don't automatically assume we will be using the global "read more" link provided by the theme
			// $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
			$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
			if ( count($words) > $excerpt_length ) {
				array_pop($words);
				$text = implode(' ', $words);
				$text = $text . $excerpt_more;
			} else {
				$text = implode(' ', $words);
			}
		}
		return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
	}

	function facebook_like_add_meta(){
		ob_end_flush();
		if(is_single()||is_page()):
	?>
	<meta property="og:title" content="<?php echo wp_title('&laquo;', false, 'right')." ".get_option('blogname');?>" />
	<meta property="og:type" content="activity" />
	<meta property="og:url" content="<?php echo $this->getUrlThis(); ?>"/>
	<?php
	$image_meta=get_post_meta(get_the_ID(),'fbImage',true);
	if(!empty($image_meta)){
		echo "<meta property='og:image' content='$image_meta' />\n";
	}elseif(!empty($this->facebook_like_og_image)){
		echo "<meta property='og:image' content='".$this->facebook_like_og_image."' />\n";
	}
	?>
	<meta property="og:site_name" content="<?php echo get_option('blogname');?>" />
	<meta property="og:description" content="<?php echo $this->zg_trim_excerpt__($post);?>" />
	<?php if(!empty($this->facebook_like_fb_admins)):?>
	<meta property="fb:admins" content="<?php echo $this->facebook_like_fb_admins;?>" />
		<?php endif;
		if(!empty($this->facebook_like_fb_app_id)):?>
	<meta property="fb:app_id" content="<?php echo $this->facebook_like_fb_app_id;?>" />
		<?php endif;
		endif;// end if single or page
	}

	function facebook_like_add_script(){
		if(is_single()||is_page()):
		wp_enqueue_script('jquery');
	?>
	<div id="fb-root"></div>
	<script>
	var notify_url='<?php echo get_option('siteurl');?>';
	window.fbAsyncInit=function() {
		FB.init({appId:'<?php echo $this->facebook_like_fb_app_id; ?>',status:true,cookie:true,xfbml:true,oauth:true});
		FB.Event.subscribe('comment.create',commentcreate);
		FB.Event.subscribe('comment.remove',commentremove);	
		function commentcreate(response){
			var data={
				fbaction:'createcomment',
				hrefcomment:response.href,
				fbcommentID:response.commentID};
			jQuery.post(notify_url,data);
		}
		function commentremove(response){
			var data={
				fbaction:'removecomment',
				hrefcomment:response.href,
				fbcommentID:response.commentID};
			jQuery.post(notify_url,data);
		}
	};
	(function(){
		var e = document.createElement('script'); e.async = true;
		e.src = document.location.protocol 
		+ '//connect.facebook.net/en_US/all.js';
		document.getElementById('fb-root').appendChild(e);
	}());
	</script>
	<?php endif;
	}
	function fb_image_meta(){
		add_meta_box('post-fbimage-div', __('Facebook Image for this Post'), array(&$this,'fb_image_post_metabox'), 'post', 'normal', 'low');
	}
	function fb_image_post_metabox($post) {
		$settingpage=get_admin_url()."options-general.php?page=facebook-like-and-comment-setting";
		$fbImage = get_post_meta($post->ID, 'fbImage', TRUE); ?>
<script type="text/javascript">
	//<![CDATA[
	var waitImg="<img src='<?php echo get_admin_url(); ?>images/loading.gif'/>";
	function setFbLikeCmtImg(e){
		e.returnValue = false;
		var image=jQuery.trim(jQuery('#fb_og_image').val());
		if(image==''){
			return(removeFbLikeCmtImg(e));
		}			
		var data={
				action:'fb_like_comment_set_fbImage',
				fb_og_image: image,
				fb_like_comment_post_id: jQuery('#fb_like_comment_post_id').val()
			};
		jQuery("#fbImage-ajax").html("<div class='updated fade'>please wait... "+waitImg+"</div>");
		jQuery.post(ajaxurl,data,function(response){	
			jQuery("#fbImage-ajax").html(response);
			hasImage=jQuery('#fbImage-name').html();
			//alert(hasImage);
			if(hasImage){
				//alert('has image');
				jQuery("#fbImage-name").html('Post image');
				jQuery("#fbImage-image").html("<img src='<?php echo $this->thumbnail_url;?>"+image+"'>");
				jQuery("#fb-remove-image").css('display','inline');
			}else{
				jQuery("#fbImage-inside").html("<table style='width:100%'><tr>"+
"<td style='width:120px'><div>"+
"	<p id='fbImage-name'>Post image</p>"+
"	<div id='fbImage-image'><img src='<?php echo $this->thumbnail_url;?>"+image+"'></div>"+
"</div></td>"+
"<td><div style='padding-bottom:10px'>"+
"	<a href='#' class='button' onclick='return setFbLikeCmtImg(event)'>Set Image</a>"+
"	<a href='#' class='button' onclick='return removeFbLikeCmtImg(event)' id='fb-remove-image'<?php echo $disp;?>>Remove Image</a></div>"+
"	<input style='width:100%' type='text' value='"+image+"' id='fb_og_image' name='fb_og_image'>"+
"</td></tr></table>");
			}
		});
		return false;
	}
	function removeFbLikeCmtImg(e){
		e.returnValue = false;
		var data={
				action:'fb_like_comment_remove_fbImage',
				fb_like_comment_post_id: jQuery('#fb_like_comment_post_id').val()
			};
		jQuery("#fbImage-ajax").html("<div class='updated fade'>please wait... "+waitImg+"</div>");
		jQuery.post(ajaxurl, data, function(response){	
			jQuery("#fbImage-ajax").html(response);
			if(response==''){
				var defaultimage=jQuery('#fb_like_comment_defaultImage').val();
				if(defaultimage==undefined||defaultimage==''){
					jQuery("#fbImage-inside").html("<table style='width:100%'><tr>"+
"<td style='width:20%'><a href='<?php echo $settingpage;?>' class='button'>Set default image</a></td>"+
"<td style='width:20%'><a href='#' class='button' onclick='return setFbLikeCmtImg(event)'>Set post image</a></td>"+
"<td><input style='width:100%' type='text' id='fb_og_image' name='fb_og_image'></td></tr></table>");
				}else{
					jQuery("#fbImage-name").html('Use default image');
					jQuery("#fbImage-image").html("<img src='<?php echo $this->thumbnail_url;?>"+defaultimage+"'>");
					jQuery("#fb_og_image").val('');
					jQuery("#fb-remove-image").css('display','none');
				}
			}
		});
		return false;
	}
	//]]>
</script>
<input type='hidden' value='<?php echo get_the_ID();?>' id='fb_like_comment_post_id' name='fb_like_comment_post_id'>
<div id='fbImage-ajax'></div><div id='fbImage-inside'>
<?php		if(empty($fbImage)&&empty($this->facebook_like_og_image)){ ?>
<table style='width:100%'><tr>
<td style='width:20%'><a href='<?php echo $settingpage;?>' class='button'>Set default image</a></td>
<td style='width:20%'><a href='#' class='button' onclick='return setFbLikeCmtImg(event)'>Set post image</a></td>
<td><input style='width:100%' type='text' id='fb_og_image' name='fb_og_image'></td>
</tr></table>
<?php		}else{
			if(!empty($fbImage)){
				$usedImage=$fbImage;
				$valueImage=$fbImage;
				$titleimage="Post image";
				$disp='';
			}else{
				$usedImage=$this->facebook_like_og_image;
				$valueImage='';
				$titleimage="Use default image";
				$disp=" style='display:none;'";
			}
			?>
<input type='hidden' value='<?php echo $this->facebook_like_og_image;?>' id='fb_like_comment_defaultImage'>
<table style='width:100%'><tr>
<td style='width:120px'><div>
	<p id='fbImage-name'><?php echo $titleimage;?></p>
	<div id='fbImage-image'><img src='<?php echo $this->thumbnail_url.$usedImage;?>'></div>
</div></td>
<td><div style='padding-bottom:10px'>
	<a href='#' class='button' onclick='return setFbLikeCmtImg(event)'>Set Image</a>
	<a href='#' class='button' onclick='return removeFbLikeCmtImg(event)' id='fb-remove-image'<?php echo $disp;?>>Remove Image</a></div>
	<input style='width:100%' type='text' value='<?php echo $valueImage;?>' id='fb_og_image' name='fb_og_image'>
</td></tr></table>
<?php	}
		echo "</div>";
	}

	function facebook_comment_cek(){
		$fbaction=$_POST['fbaction'];
		$to=get_option('admin_email');
		if(($fbaction=='createcomment'|$fbaction=='removecomment')&!empty($to)){
			if($fbaction=='createcomment')$subject="New Facebook Comment Added";
			else $subject="Facebook Comment Removed";
			$body="Facebook Comment for ".$_POST['hrefcomment'];
			if($fbaction=='createcomment')$body.=" added\n";
			else $body.=" removed\n";
			$body.="CommentID=".$_POST['fbcommentID'];
			$header="From: noreply@wordpress.com";
			wp_mail($to,$subject,$body,$header);
			die;
		}	
	}

	function facebook_comments_template($commenttemplate){
		if(!$this->facebook_like_use_fb_comment)return $commenttemplate;
		$rrr=dirname(__FILE__)."/comments.php";
		return $rrr;	
	}
	
	function fb_like_comment_update_opt(){
		if (!current_user_can('manage_options')){
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		if(isset($_POST['fb_og_image'])){
			$this->facebook_like_og_image=$_POST['fb_og_image'];
			update_option("facebook_like_og_image", $this->facebook_like_og_image);
		}
		if(isset($_POST['fb_admins'])){
			$this->facebook_like_fb_admins=$_POST['fb_admins'];
			update_option("facebook_like_fb_admins", $this->facebook_like_fb_admins);
			
			$this->facebook_like_fb_app_id=$_POST['fb_app_id'];
			update_option("facebook_like_fb_app_id", $this->facebook_like_fb_app_id);
		}
		if(isset($_POST['fb_like_width'])){
			$this->facebook_like_like_width=$_POST['fb_like_width'];
			update_option("facebook_like_like_width", $this->facebook_like_like_width);

			if(isset($_POST['use_fb_recommend']))$this->facebook_like_like_use_recommend=true;
			else $this->facebook_like_like_use_recommend=false;
			update_option("facebook_like_like_use_recommend", $this->facebook_like_like_use_recommend);

			if(isset($_POST['fb_dark_color']))$this->facebook_like_dark_color=true;
			else $this->facebook_like_dark_color=false;
			update_option("facebook_like_dark_color", $this->facebook_like_dark_color);
		}
		if(isset($_POST['fb_comment_width'])){
			$this->facebook_like_comment_width=$_POST['fb_comment_width'];
			update_option("facebook_like_comment_width", $this->facebook_like_comment_width);

			$this->facebook_like_comment_num=$_POST['fb_comment_num'];
			update_option("facebook_like_comment_num", $this->facebook_like_comment_num);

			if(isset($_POST['fb_comment_dark_color']))$this->facebook_comment_dark_color=true;
			else $this->facebook_comment_dark_color=false;
			update_option("facebook_comment_dark_color", $this->facebook_comment_dark_color);

			if(isset($_POST['use_fb_comment']))$this->facebook_like_use_fb_comment=true;
			else $this->facebook_like_use_fb_comment=false;
			update_option("facebook_like_use_fb_comment", $this->facebook_like_use_fb_comment);
		}
		echo "<div class='updated fade'>".__('Options Updated')."</div>";
		die;
	}
	function fb_like_comment_set_fbImage(){
		if (!current_user_can('manage_options')){
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		if(!empty($_POST['fb_og_image'])&&!empty($_POST['fb_like_comment_post_id'])){
			if(!update_post_meta($_POST['fb_like_comment_post_id'],'fbImage',$_POST['fb_og_image']))_e('Fail to set Facebook image.');
			die;
		}else wp_die(__('Fail to set Facebook image.'));
	}
	function fb_like_comment_remove_fbImage(){
		if (!current_user_can('manage_options')){
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		if(!empty($_POST['fb_like_comment_post_id'])){
			delete_post_meta($_POST['fb_like_comment_post_id'],'fbImage');
			die;
		}else wp_die(__('Fail to delete Facebook image.'));
	}
}

$facebook_like_and_comment = new Facebook_Like_And_Comment();
?>