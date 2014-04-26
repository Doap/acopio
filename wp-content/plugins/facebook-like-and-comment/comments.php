<?php
global $facebook_like_and_comment;
$urlthis=$facebook_like_and_comment->getUrlThis();
if($facebook_like_and_comment->facebook_comment_dark_color)$use_dark=' data-colorscheme="dark"';else $use_dark='';
?>
<div class="comments_form" name="comment" id="comment" >
<div style="display:block;margin-left:auto;margin-right:auto;padding:20px 5px 20px 5px" id="respond">
<div style="position:absolute;z-index:-1000;font-size: 0px;">
<?php //place comment cache for seo sake
global $wpdb;
$cache_dir=WP_CONTENT_DIR."/fb-comment-cache";
if(!is_dir($cache_dir))mkdir($cache_dir,0755);
$cache_file=$cache_dir."/".$wpdb->prefix.get_the_ID();
$cache_life = '86400'; //caching time, in seconds, 1 day
$commentfb="https://graph.facebook.com/comments/?ids=$urlthis";
if (!file_exists($cache_file) or (time() - filemtime($cache_file) >= $cache_life)){
	$data = json_decode(file_get_contents($commentfb),true);
	$cache_comment='';	
	foreach($data as $obj){
		$cmt=$obj['data'];
		foreach($cmt as $cm){
			$cache_comment.="<div class='fbcomment'>from: ".$cm['from']['name']."<br>";
			$cache_comment.="message: ".$cm['message']."<br>";
			if(isset($cm['comments'])){
				$cache_comment.=$cm['comments']['count']." reply:<br>";
				foreach($cm['comments']['data']as $reply){
					$cache_comment.="<div class='fbreply'>from: ".$reply['from']['name']."<br>";
					$cache_comment.="message: ".$reply['message']."</div>";		
				}
			}
			$cache_comment.='</div>';	
		}
	}	
    file_put_contents($cache_file,$cache_comment);
	echo $cache_comment;
}else{
    readfile($cache_file);
}
?>
</div>
<div class="fb-comments" data-href="<?php echo $urlthis; ?>" data-num-posts="<?php echo $facebook_like_and_comment->facebook_like_comment_num;?>" data-width="<?php echo $facebook_like_and_comment->facebook_like_comment_width;?>"<?php echo $use_dark;?>></div>
</div></div>