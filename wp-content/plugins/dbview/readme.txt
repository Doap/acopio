=== dbview ===
Contributors: john ackers
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=john%2eackers%40ymail%2ecom&lc=GB&item_name=John%20Ackers&currency_code=GBP&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: ajax, admin, database, SQL, mysql, table
Requires at least: 3.0.1
Tested up to: 3.3.1
Stable tag: trunk

== Description ==

Presents the results a database SQL query in a table. The query can be saved as a 
named view which can then be embedded as a table in any post using the shortcode 
[dbview name=name-of-view]. Views can be created and edited in the admin pages. 

= Features =

* Easy to deploy (assuming that you can write an SQL query!).
* The contents of each column can be manipulated using PHP snippets. This functionality allows the introduction 
of permalinks, images and other customisations. 
* Column sorting. Table navigation/paging supported e.g. [dbview name=addressBook pagesize=20]. 
* Results tables are AJAX loaded. Management interface is AJAX driven.  
* Each view is stored in a single serialized object in the wp_options table.
* Tables are not styled; this is left to the theme.

= Limitations =

* The data in the results table(s) cannot be edited. 

= Security =

When the plugin is activated, administrators are given the capability to 'manage DB views'.
Any other wp user with a different role that needs to create/edit views [must be granted that capability](http://codex.wordpress.org/Roles_and_Capabilities).
Only a view that is explicitly checked as public will be visible to non administrators and the public.

== Installation ==

1. Follow the [standard installation procedure for WordPress plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).
2. Refresh (F5) any existing pages in browser so latest javascript loaded.
3. Manually decativate and reactivate if any predefined views are missing.

There are no configurable options.

Ten or more predefined views that navigate wp_posts, wp_postmeta, wp_options, 
wp_users and wp_usermeta are loaded and reloaded each time the plugin is activated. 
These views can be modified and deleted.

== Frequently Asked Questions ==

Why are changes to the 'public' setting of a view not immediately effective?

Because the properties of each dbview are stored in the wp_options table which is cached for each session.

== Screenshots ==

1. screenshot-1.png - the admin screen showing initial 'views' which can be modified or deleted. Reactivate the plugin if these aren't visible. 
2. screenshot-2.png - the admin screen showing an arbitrary view 'signatures so far'.
3. screenshot-3.png - the admin screen showing one view containing links to other views.

== Changelog ==

= 0.5.2 =

* sorting by column
* orphaned PHP snippets displayed in extra columns in table

= 0.5.1 =

* bug fix, last page of results wasn't shown 

= 0.5.0 =

* table scrolling supported.

= 0.4.5 =

* list tables when using table prefix other than 'wp_'  [see post](http://wordpress.org/support/topic/plugin-dbview-another-table-doesnt-exist).

= 0.4.4 =

* remove superflous character encoding/decoding so umlauts etc handled properly [see post](http://wordpress.org/support/topic/plugin-dbview-charset-encoding-encodehtmlentities-is-broken-by-using-utf-8).

= 0.4.3 =

* even when magic quotes is off, stripslashes from textarea input (because wp always adds them).
* warn administrators when they are looking at a page with a dbview that is not public.

= 0.4.2 =

* Rows founds, rows affected shown.
* Index related warnings fixed. 

= 0.4.1 =

* Preconfigured views extended and linked together to allow wpdb tables to be navigated.
* Handle links with containing SQL query

= 0.4.0 =

* Public flag added to each view.
* 'List views' now show PHP snippets count and SQL statements containing are encoded.
* Change button legends
* Text moved into PHP class to support translation
* Bug fix, make ?page=dbview&name=myview works so allow sharing of tables
* Bug fix, correct loading.gif URL when table loading on public pages

= 0.3.1 =

* Preload 'list views' and 'show table status' as views.
* Allow unsaved queries to be executed
* Put back top line of file containing Plugin Name !!!

= 0.3.0 =

* Unserialize objects and display using print_r()
* Bug fix: Accidental double serialization of DBView objects stopped. Old objects still loadable.

= 0.2.3 =

* Correct the saved successfully message.

= 0.2.2 =

* Header cell editing improved.

= 0.2 =

* Fix bugs to correct views on public pages.

= 0.1 =

* First version.
