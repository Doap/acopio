<?php

/*
	Plugin Name: Diagnostic Tool
	Plugin URI: http://mywwwsupport.com/plugin
	Description: Find common Email, Network and DNS setup problems & provides altered file montioring
	Revision: $Revision$
	Author: IMRE Ltd
	Author URI: http://mywwwsupport.com
*/

function dtSubMenu() {
	echo '<h2 class="mwsheading">Diagnostic Tool by <a href="http://mywwwsupport.com">mywwwsupport.com</a></h2>';
	echo '<div class="mwssubmenudiv">';
	echo '<ul class="mwssubmenu">';
	echo '<a href="'.DTADMINPAGE.'overview"><li class="like-a-button">Overview</li></a>';
	echo '<a href="'.DTADMINPAGE.'/views/dtfilechecksumview.php"><li class="like-a-button">Altered Files</li></a>';
	echo '<a href="'.DTADMINPAGE.'/views/dtemailview.php"><li class="like-a-button">Email Testing</li></a>';
	echo '<a href="'.DTADMINPAGE.'/views/dtoutboundconnectionsview.php"><li class="like-a-button">Outgoing Connections</li></a>';
	echo '<a href="'.DTADMINPAGE.'/views/dtoutboundconnectionsuntrackedview.php"><li class="like-a-button">Untracked Transports</li></a>';
	echo '<a href="'.DTADMINPAGE.'/views/dtresolverview.php"><li class="like-a-button">DNS Resolver</li></a>';
	echo '<a href="'.DTADMINPAGE.'/views/dtcronview.php"><li class="like-a-button">Cron Overview</li></a>';
	echo '<a href="'.DTADMINPAGE.'/views/dtsettings.php"><li class="like-a-button">Settings</li></a>';
	echo '</ul>';
	echo '</div>';
}
