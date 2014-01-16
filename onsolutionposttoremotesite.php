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
	global $wpdb;
 	
 	//Table for remote sites
	$osrp_remote_sites = $wpdb->prefix . 'osrp_remote_sites';
	$tbl_remote_sites = "CREATE TABLE " . $osrp_remote_sites . " (
			ID INT(11) NOT NULL AUTO_INCREMENT,
			site_name mediumtext NOT NULL,
			site_address tinytext NOT NULL,				
			UNIQUE KEY id (ID)
		)";

	$wpdb->query($tbl_remote_sites);

	//Table for word replacements
	$osrp_word_replacements = $wpdb->prefix . 'osrp_word_replacements';
	$tbl_word_replacements = "CREATE TABLE " . $osrp_word_replacements . " (
			ID INT(11) NOT NULL AUTO_INCREMENT,
			site_ID INT(11) NOT NULL,
			search mediumtext NOT NULL,
			replace mediumtext NOT NULL,				
			UNIQUE KEY id (ID)
		)";

	$wpdb->query($tbl_word_replacements);

	//Table for the list of posts that have been distributed
	$osrp_distributed_posts = $wpdb->prefix . 'osrp_distributed_posts';
	$tbl_distributed_posts = "CREATE TABLE " . $osrp_distributed_posts . " (
			ID INT(11) NOT NULL AUTO_INCREMENT,
			original_post_ID INT(11) NOT NULL,
			new_post_ID INT(11) NOT NULL,
			date_pushed mediumtext NOT NULL,
			date_release tinytext NOT NULL,				
			UNIQUE KEY id (ID)
		)";

	$wpdb->query($tbl_distributed_posts);

	//Table for photo substitutions
	$osrp_photo_substitutions = $wpdb->prefix . 'osrp_photo_substitutions';
	$tbl_photo_substitutions = "CREATE TABLE " . $osrp_photo_substitutions . " (
			ID INT(11) NOT NULL AUTO_INCREMENT,
			name varchar(255) DEFAULT NULL,
			path_name tinytext NOT NULL,				
			UNIQUE KEY id (ID)
		)";

	$wpdb->query($tbl_photo_substitutions);
	
}

register_activation_hook( __FILE__, 'activate' );

function deactivate(){
	//DB Removal here
	global $wpdb;//required global declaration of WP variable

	//Table for remote sites
	$osrp_remote_sites = $wpdb->prefix . 'osrp_remote_sites';
	$tbl_remote_sites = "DROP TABLE ".  $osrp_remote_sites;
	$wpdb->query($tbl_remote_sites);

	//Table for word replacements
	$osrp_word_replacements = $wpdb->prefix . 'osrp_word_replacements';
	$tbl_word_replacements = "DROP TABLE ".  $osrp_word_replacements;
	$wpdb->query($tbl_word_replacements);

	//Table for the list of posts that have been distributed
	$osrp_distributed_posts = $wpdb->prefix . 'osrp_distributed_posts';
	$tbl_distributed_posts = "DROP TABLE ".  $osrp_distributed_posts;
	$wpdb->query($tbl_distributed_posts);

	//Table for photo substitutions
	$osrp_photo_substitutions = $wpdb->prefix . 'osrp_photo_substitutions';
	$tbl_photo_substitutions = "DROP TABLE ".  $osrp_photo_substitutions;
	$wpdb->query($tbl_photo_substitutions);
}

register_deactivation_hook( __FILE__, 'deactivate' );

Class remotePost {

}