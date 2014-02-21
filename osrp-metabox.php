<?php
//Post meta box
add_action( 'add_meta_boxes', 'osrp_meta_box_add' );
function osrp_meta_box_add()
{
	add_meta_box( 'osrp-duplicator-box', 'Duplicate post to remote site', 'osrp_meta_box_cb', 'post', 'normal', 'high' );
}

function osrp_meta_box_cb( $post )
{
	$values = get_post_custom( $post->ID );
	//$text = isset( $values['my_meta_box_text'] ) ? esc_attr( $values['my_meta_box_text'][0] ) : '';
	//$selected = isset( $values['my_meta_box_select'] ) ? esc_attr( $values['my_meta_box_select'][0] ) : '';
	//$check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'][0] ) : '';

	$selected_osrp_mb_postStatus = isset( $values['osrp_mb_postStatus'] ) ? esc_attr( $values['osrp_mb_postStatus'][0] ) : '';
	$selected_osrp_mb_postWhen = isset( $values['osrp_mb_postWhen'] ) ? esc_attr( $values['osrp_mb_postWhen'][0] ) : '';
	$check_osrp_mb_postView = isset( $values['osrp_mb_postView'] ) ? esc_attr( $values['osrp_mb_postView'][0] ) : '';
	$text_osrp_mb_postWhenSched = isset( $values['osrp_mb_postWhenSched'] ) ? esc_attr( $values['osrp_mb_postWhenSched'][0] ) : '';
	wp_nonce_field( 'osrp_mb_nonce', 'meta_box_nonce' ); ?>
	
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('css/osrp-styles.css', __FILE__); ?>">
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css">
	<script type="text/javascript" src="/wp-includes/js/jquery/ui/jquery.ui.datepicker.min.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$( "#osrp_mb_postWhenSched" ).datepicker();
		});
	</script>

	<table id="osrp_table">
			<tr valign="top">
				<td>
					<select name="cars" multiple>
						<?php foreach ( get_remote_sites() as $remoteSite ) : ?>				
							  <option value="<?php echo $remoteSite->ID; ?>">
							  	<?php echo $remoteSite->site_address . " " . (check_push_status( $remoteSite->ID, $post->ID ) ? "(pushed)" : ""); ?>
							  </option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<label><input type="radio" name="osrp_mb_postStatus" value="publish" <?php selected( $selected_osrp_mb_postStatus, 'publish' ); ?> />Publish</label> <label><input type="radio" name="osrp_mb_postStatus" value="draft" <?php selected( $selected_osrp_mb_postStatus, 'draft' ); ?> />Create only</label><br/>
					<label><input type="radio" name="osrp_mb_postWhen" value="now" <?php selected( $selected_osrp_mb_postWhen, 'now' ); ?> />Release now</label> <label><input type="radio" name="osrp_mb_postWhen" value="schedule" <?php selected( $selected_osrp_mb_postWhen, 'scheduled' ); ?> />Schedule</label><br/>
					<input type="text" name="osrp_mb_postWhenSched" value="<?php echo $text_osrp_mb_postWhenSched; ?>" id="osrp_mb_postWhenSched" placeholder="mm/dd/yyyy" /><br/>
					<label><input type="checkbox" name="osrp_mb_postView" <?php checked( $check_osrp_mb_postView, 'on' ); ?> /> View</label><br/><br/>
				</td>
			</tr>
			<tr>
				<td colspan="2" ><input type="button" name="osrp_duplicate" id="osrp_duplicate" value="Duplicate" /></td>
			</tr>
		</table>

	<!-- <p>
		<label for="my_meta_box_text">Text Label</label>
		<input type="text" name="my_meta_box_text" id="my_meta_box_text" value="<?php echo $text; ?>" />
	</p>
	
	<p>
		<label for="my_meta_box_select">Color</label>
		<select name="my_meta_box_select" id="my_meta_box_select">
			<option value="red" <?php //selected( $selected, 'red' ); ?>>Red</option>
			<option value="blue" <?php //selected( $selected, 'blue' ); ?>>Blue</option>
		</select>
	</p>
	<p>
		<input type="checkbox" name="my_meta_box_check" id="my_meta_box_check" <?php //checked( $check, 'on' ); ?> />
		<label for="my_meta_box_check">Don't Check This.</label>
	</p> -->
	<?php	
}

add_action( 'save_post', 'osrp_meta_box_save' );
function osrp_meta_box_save( $post_id )
{
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'osrp_mb_nonce' ) ) return;
	
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	// now we can actually save the data
	$allowed = array( 
		'a' => array( // on allow a tags
			'href' => array() // and those anchords can only have href attribute
		)
	);

	// Probably a good idea to make sure your data is set
	if( isset( $_POST['osrp_mb_postWhenSched'] ) )
		update_post_meta( $post_id, 'osrp_mb_postWhenSched', wp_kses( $_POST['osrp_mb_postWhenSched'], $allowed ) );
	
	// Probably a good idea to make sure your data is set
	// if( isset( $_POST['my_meta_box_text'] ) )
	// 	update_post_meta( $post_id, 'my_meta_box_text', wp_kses( $_POST['my_meta_box_text'], $allowed ) );
		
	// if( isset( $_POST['my_meta_box_select'] ) )
	// 	update_post_meta( $post_id, 'my_meta_box_select', esc_attr( $_POST['my_meta_box_select'] ) );
		
	// This is purely my personal preference for saving checkboxes
	// $chk = ( isset( $_POST['my_meta_box_check'] ) && $_POST['my_meta_box_check'] ) ? 'on' : 'off';
	// update_post_meta( $post_id, 'my_meta_box_check', $chk );
}

function get_remote_sites() {
	global $wpdb;

	$tbl = $wpdb->prefix . "osrp_remote_sites";
	return $wpdb->get_results("SELECT * FROM $tbl");
}

function check_push_status( $site_id, $post_id ) {
	global $wpdb;

	$tbl = $wpdb->prefix . "osrp_distributed_posts";
	$result = $wpdb->get_row("SELECT * FROM $tbl WHERE original_post_ID='$post_id' AND remote_site_ID='$site_id'");
	return $result;
}
?>