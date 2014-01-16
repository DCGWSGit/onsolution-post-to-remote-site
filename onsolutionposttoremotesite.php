<?php
/*
*Plugin Name: OnSolution Remote Posting
*Plugin URI: http://dcgws.com
*Description: A plugin that helps to import the post to remote site.
*Version: 1.0 beta
*Author: The DCGWS Team
*Author URI: http://dcgws.com
***********************************************************************************************
*/

function activate(){
	// DB setup here
	
}

register_activation_hook( __FILE__, 'activate' );

function deactivate(){
	//DB Removal here
}

register_deactivation_hook( __FILE__, 'deactivate' );




Class remotePost{

}