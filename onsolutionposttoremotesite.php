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
			site_address tinytext NOT NULL,
			description varchar(500) NOT NULL,	
			username varchar(255) NOT NULL,
			password varchar(255) NOT NULL,
			frequency varchar(255) NOT NULL,
			schedule varchar(255) NOT NULL,			
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
			remote_site_ID INT(11) NOT NULL,
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

//post metabox
include "osrp-metabox.php";

// adding menu on the wordpress dashboard
add_action( 'admin_menu', 'osrp_fn_menu_page' );

function osrp_fn_menu_page(){
    add_menu_page( 'OnSolution Configuration Page', 'OnSolution', 'manage_options', 'osrp-list-of-remote-sites', 'osrp_menu_page','',6); 
    add_submenu_page( 'osrp-list-of-remote-sites', 'List of Remote Sites', 'List of Remote Sites', 'manage_options', 'osrp-list-of-remote-sites', 'osrp_menu_page' );
    add_submenu_page( 'osrp-list-of-remote-sites', 'Add New', 'Add New', 'manage_options', 'osrp-add-new', 'osrp_add_new' );
}


//for configuration page
function osrp_menu_page(){
	global $wpdb;

	$table = $wpdb->prefix."osrp_remote_sites";
	$table2 = $wpdb->prefix."osrp_word_replacements";

	$sql = $wpdb->get_results("SELECT * FROM $table");
	$sql2 = $wpdb->get_results("SELECT * FROM $table2");

    $html = '<div class="wrap">
				<h2>List of Remote Sites <a href="admin.php?page=osrp-add-new" class="add-new-h2">Add New Site</a></h2>

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
			        #wpfooter{
				        display:none;
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

							<tbody>';

							if(count($sql) > 0){
								foreach ($sql as $sites) {

										$html .= '<tr>
				                            		<td class="domainname">
				                            			<strong>'.$sites->site_address.'</strong>
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
				                            		<td class="description">'.$sites->description.'</td>
				                            		<td>'.$sites->username.'</td>
				                            		<td>'.str_repeat('•', strlen(base64_decode($sites->password))).'</td>
				                            		<td style="text-align:center;">'.$sites->frequency.'</td>
				                            		<td style="text-align:center;">'.$sites->schedule.'</td>
				                            	</tr>';
								}
						    }
						    else{
						    	$html .= '<tr><td colspan="6" style="text-align:center;"><strong>No Site Found</strong></td></tr>';
						    }

	$html   .=				'</tbody>
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

							<tbody>';

							if(count($sql2) > 0){
								foreach ($sql2 as $words) {

									$sitename = $wpdb->get_row("SELECT site_address FROM $table WHERE ID = $words->site_ID ");

		                        $html .= '<tr>
		                            		<td class="domainname">
		                            				<strong>'.$sitename->site_address.'</strong>
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
		                            		<td>'.$words->original.'</td>
		                            		<td>'.$words->replacement.'</td>
		                            	</tr>';
                            	}
                            }
                            else{
						    	$html .= '<tr><td colspan="3" style="text-align:center;"><strong>No word replacements found</strong></td></tr>';
						    }


	$html .= 				'</tbody>
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
			        #wpfooter{
				        display:none;
				    }
			    </style>
				 <div class="postbox" style="width: 800px;">
				 <form method="post">
					<table class="wp-list-table widefat fixed pages" cellspacing="0">
					    <input type="hidden" name="addnew" value="true"/>
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
                            		<td><textarea name="description"></textarea></td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"><strong>Frequency of Post:</strong></td>
                            		<td><input type="text" name="frequency"/></td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"><strong>Schedule:</strong></td>
                            		<td><input type="text" name="schedule"/></td>
                            	</tr>
                            	<tr>
                            		<td colspan="2">
                            			<h3 class="hndle"><span>List of word substitutions</span></h3>
                            			<table class="wp-list-table widefat fixed pages" cellspacing="0">
					                            <thead>
					                            	<tr>
					                            		<th>Original Word</th>
					                            		<th>Replaced Word</th>
					                            		<th></th>
					                            	</tr>
												</thead>

												<tfoot>
					                            	<tr>
					                            		<th>Original Word</th>
					                            		<th>Replaced Word</th>
					                            		<th></th>
					                            	</tr>
												</tfoot>

												<tbody class="word-subs">
															<tr>
							                            		<td><input type="text" name="original-word" style="width:200px"/></td>
							                            		<td><input type="text" name="replace-word"  style="width:200px"/></td>
							                            		<td><input type="button" class="add-word button button-primary" value="add"/></td>
							                            	</tr>
							                    </tbody>
					                    </table>
                            		</td>
                            	</tr>
                            	<tr>
                            	    <td style="width:100px;vertical-align: middle;"></td>
                            		<td><input type="submit" name="test" id="test" class="button button-primary" value="Save">  <input type="button" name="connect" id="connect" class="button button-primary" value="Connect">   <input type="button" name="connect" id="connect" class="button button-primary" value="Download"></td>
                            	</tr>
							</tbody>
                    </table>
                 </form>
                 <div class="clear"></div>
				</div>
			 </div>
			 </div>
			 <div class="clear"></div>

			 <script type="text/javascript">
			 	jQuery(document).ready(function($){
			 		$(".add-word").click(function(){
			 				var orig = $("input[name=original-word]").val();
			 				var replaced = $("input[name=replace-word]").val();
			 				
			 				var tbs   = "<tr>";
			 					tbs  += "<td><input type=hidden name=original-words[] style=width:200px value="+orig+" >"+orig+"</td>";
			 					tbs  += "<td><input type=hidden name=replace-words[] style=width:200px value="+replaced+" >"+replaced+"</td>";
			 				    tbs	 += "</tr>";

			 				$(".word-subs").prepend(tbs);
			 				$("input[name=original-word]").val("");
			 				$("input[name=replace-word]").val("")
			 		});
			 	});
			 </script>


			 <div class="clear"></div>
			 ';

	if($_POST['addnew'] == 'true'){
		 $html .= save_remote();
	}

	echo  $html;
}


function save_remote(){
	global $wpdb;

	$domain = $_POST['domain'];
	$description = $_POST['description'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$description = $_POST['description'];
	$frequency   = $_POST['frequency'];
	$scheduled   = $_POST['schedule'];


	$table = $wpdb->prefix."osrp_remote_sites";

	$values = array(
			'site_address' => $domain,
			'description'  => $description,
			'username'	   => $username,
			'password'     => base64_encode($password),
			'frequency'    => $frequency,
			'schedule'     => $scheduled
		);

	$format = array('%s','%s');

	$sql = $wpdb->insert($table, $values, $format);

	if($sql){
		$id = $wpdb->insert_id;
		$orig = $_POST['original-words'];
		$rep  = $_POST['replace-words'];

		$wordtable = $wpdb->prefix."osrp_word_replacements";

		foreach ($orig as $key => $value) {
				
				$name = array(
							'site_ID'      => $id,
							'original'     => $value,
							'replacement'  => $rep[$key]
						);

				$sqls = $wpdb->insert($wordtable, $name, $format);
		}

		if($sqls){
			$html = '<div id="message" class="updated"><p>Site Saved</p></div>';

			return $html;
		}
	}
}	