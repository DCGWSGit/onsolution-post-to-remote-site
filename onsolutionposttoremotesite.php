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
    add_submenu_page( 'osrp-configuration-page', 'List of Remote Sites', 'List of Remote Sites', 'manage_options', 'osrp-configuration-page', 'osrp_menu_page' );
    add_submenu_page( 'osrp-configuration-page', 'Add New', 'Add New', 'manage_options', 'osrp-add-new', 'osrp_add_new' );
}


//for configuration page
function osrp_menu_page(){
    $html = '<div class="wrap">
				<h2>List of Remote Sites <a href="" class="add-new-h2">Add New Site</a></h2>

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
			        	width:350px;
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
                            		<th style="text-align:center;">Frequency of Posts</th>
                            		<th style="text-align:center;">Last scheduled date</th>
                            	</tr>
							</thead>

							<tfoot>
                            	<tr>
                            		<th class="domainname">Domain name</th>
                            		<th class="description">Description</th>
                            		<th>Username</th>
                            		<th>Password</th>
                            		<th style="text-align:center;">Frequency of Posts</th>
                            		<th style="text-align:center;">Last scheduled date</th>
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
                            		<td>•••••••</td>
                            		<td style="text-align:center;">150</td>
                            		<td style="text-align:center;">2014/01/22 3:03:57 AM Published</td>
                            	</tr>
							</tbody>
                    </table>
				</div>
			 </div>

			 <div class="postbox" style="width: 800px;">
					<h3 class="hndle"><span>List of word substitutions</span></h3>

					<table class="wp-list-table widefat fixed pages" cellspacing="0">
                            <thead>
                            	<tr>
                            		<th class="domainname">Domain name</th>
                            		<th>Original Word</th>
                            		<th>Replaced Word</th>
                            	</tr>
							</thead>

							<tfoot>
                            	<tr>
                            		<th class="domainname">Domain name</th>
                            		<th>Original Word</th>
                            		<th>Replaced Word</th>
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
                            		<td>original word</td>
                            		<td>test word</td>
                            	</tr>
							</tbody>
                    </table>
				</div>
			 </div>
			
			 ';

	echo  $html;
}


//Add new
function osrp_add_new(){
	 $html = '<div class="wrap">
				<h2>Add New Remote Site</h2>
				
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
			        	width:350px;
			        }
			        .postbox input[type="text"],.postbox input[type="password"]{
			        	width:250px;
			        }
			        .postbox input[type="button"]{
			        	width:150px;
			        }
			        .postbox textarea{
			        	width:450px;
			        	height:100px;
			        }
			    </style>
				 <div class="postbox" style="width: 800px;">
				 <form>
					<table class="wp-list-table widefat fixed pages" cellspacing="0">

							<tbody>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"><strong>Site Name:</strong></td>
                            		<td><input type="text" name="domain"/></td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"><strong>Username:</strong></td>
                            		<td><input type="text" name="username"/></td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"><strong>Password:</strong></td>
                            		<td><input type="password" name="password"/></td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"><strong>Description:</strong></td>
                            		<td><textarea></textarea></td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"><strong>Frequency of Post:</strong></td>
                            		<td><input type="text" name="frequency"/></td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"><strong>Schedule:</strong></td>
                            		<td><input type="text" name="frequency"/></td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"></td>
                            		<td><input type="button" name="test" id="test" class="button button-primary" value="Test Connection">  <input type="button" name="connect" id="connect" class="button button-primary" value="Connect">   <input type="button" name="connect" id="connect" class="button button-primary" value="Download"></td>
                            	</tr>
							</tbody>
                    </table>
                 </form>
				</div>
			 </div>
			 </div>';

	echo  $html;
}