=== Facebook Like And Comment ===
Contributors: sulhansetiawan
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZU6G6L7UVAE9C
Tags: comment, comments, facebook, facebook like, facebook comment, social plugin
Tested up to: 3.3.1
Stable tag: trunk

Add like/recommend button, also send button, and optionally use facebook comment instead of wordpress comment for your wordpress site.

== Description ==

Facebook Like And Comment add like/recommend button, also send button, and optionally use facebook comment instead of wordpress comment for your wordpress site.

Each post or page will has Like/Recommend  button bellow post content.
When visitor click on this button, the specified image will be shown on visitor's facebook wall, the title of the post and its excerpt.

If you want, you may use facebook comment instead of standard wordpress comment.
Every time a new facebook comment added, you will be notified through admin email. So you can soon approve or ban the comment.

The facebook comment is actually an iframe. So, search engine will not crawl the comment inside it. To overcome this, the plugin will read it and cache it, then include it inside each post behind the facebook comment, so search engine will crawl it but will not affect the appearance. This is for the SEO sake.

For more details, please visit [Facebook Like And Comment](http://www.sulhansetiawan.com/fblike-comment)

== Installation ==

1. Extract 'facebook-like-and-comment.zip' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Fill all required option through 'FB Like&Comment' setting.
4. Ensure that '/wp-content/' directory is writable, or make a directory inside it named 'fb-comment-cache' and make it writable.
5. Enjoy the plugin :D

== Screenshots ==

1. Like/Recommend and Send button below post content
2. Original Wordpress comment will be replaced by Facebook comment

== Changelog ==

= 1.0.0 =
* Using Class instead of regular function.
* Better UI.
* Modifying html tag in a better way.

= 0.0.2 =
* Little fix.

= 0.0.1 =
* Little fix.

= 0.0.0 =
* First release.
