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
			original mediumtext NOT NULL,
			replacement mediumtext NOT NULL,				
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



// adding menu on the wordpress dashboard
add_action( 'admin_menu', 'osrp_fn_menu_page' );

function osrp_fn_menu_page(){
    add_menu_page( 'OnSolution Configuration Page', 'OnSolution', 'manage_options', 'osrp-configuration-page', 'osrp_menu_page','',6); 
    $submenu['osrp-configuration-page'] = array();
    add_submenu_page( 'osrp-configuration-page', 'List of word substitutions', 'List of word substitutions', 'manage_options', 'osrp-list-of-word-subsitutions', 'osrp_list_word_subsitutions' );
}


//for configuration page
function osrp_menu_page(){
    $html = '<div class="wrap">
				<h2>OnSolution Configuration <a href="" class="add-new-h2">Add New Site</a></h2>

				<div class="postbox"></div>
				<style type="text/css">
			        .postbox h3.hndle {
			            font-size: 15px;
			            font-weight: bold;
			            padding: 7px 10px;
			            margin: 0;
			            line-height: 1;
			        }
			        .postbox .domainname{
			        	width:250px;
			        }
			        .postbox .description{
			        	width:360px;
			        }
			    </style>
				<div class="postbox">
					<h3 class="hndle"><span>Site Information</span></h3>

					<table class="wp-list-table widefat fixed pages" cellspacing="0">
                            <thead>
                            	<tr>
                            		<th class="domainname">Domain name</th>
                            		<th class="description">Description</th>
                            		<th>Username</th>
                            		<th>Password</th>
                            		<th>Frequency of Posts</th>
                            		<th>Last scheduled date</th>
                            	</tr>
							</thead>

							<tfoot>
                            	<tr>
                            		<th class="domainname">Domain name</th>
                            		<th class="description">Description</th>
                            		<th>Username</th>
                            		<th>Password</th>
                            		<th>Frequency of Posts</th>
                            		<th>Last scheduled date</th>
                            	</tr>
							</tfoot>

							<tbody>
                            	<tr>
                            		<td class="domainname">
                            			<strong>www.localhost.com</strong>
                            			<div class="row-actions">
                            				<span class="edit">
                            					<a href="" title="Edit this item">Edit</a> 
                            					| 
                            				</span>
                            				<span class="trash">
                            				    <a class="submitdelete" title="Move this item to the Trash" href="#">Trash</a> 
                            				</span>
                            			</div>
                            		</td>
                            		<td class="description">Welcome to WordPress. This is your first post. Edit or delete it, then start blogging!</td>
                            		<td>admin</td>
                            		<td>ca7a2f04ff3c9469a9be02aa6df100756fde57bc</td>
                            		<td>150</td>
                            		<td>2014/01/22 3:03:57 AM Published</td>
                            	</tr>
							</tbody>
                    </table>
				</div>
			 </div>';

	echo  $html;
}

//List of word subsitutions page
function osrp_list_word_subsitutions(){
	 $html = '<div class="wrap">
				<h2>OnSolution List of Remote Sites<a href="" class="add-new-h2">Add New Site</a></h2>
				
			 </div>';

	echo  $html;
}