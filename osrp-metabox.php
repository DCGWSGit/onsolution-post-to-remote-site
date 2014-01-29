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
	$text = isset( $values['my_meta_box_text'] ) ? esc_attr( $values['my_meta_box_text'][0] ) : '';
	$selected = isset( $values['my_meta_box_select'] ) ? esc_attr( $values['my_meta_box_select'][0] ) : '';
	//$check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'][0] ) : '';
	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' ); ?>

	<table width="100%">
			<tr valign="top">
				<td>
					<select name="cars" multiple style="width:650px;">
						<!-- <optgroup label="Domain::Description::Status"> -->
						<?php foreach ( get_remote_sites() as $remoteSite ) : ?>				
							  <option value="<?php echo $remoteSite->ID; ?>">
							  	<?php echo $remoteSite->site_address . " " . (check_push_status( $remoteSite->ID, $post->ID ) ? "(pushed)" : "(pushed)"); ?>
							  </option>
						<?php endforeach; ?>
						<!-- </optgroup> -->
					</select>
				</td>
				<td>
					<label><input type="checkbox" name="my_meta_box_check1" id="my_meta_box_check1" <?php checked( $check, 'on' ); ?> /> Publish/Create only</label><br/>
					<label><input type="checkbox" name="my_meta_box_check2" id="my_meta_box_check2" <?php checked( $check, 'on' ); ?> /> Release now/schedule</label><br/>
					<label><input type="checkbox" name="my_meta_box_check3" id="my_meta_box_check3" <?php checked( $check, 'on' ); ?> /> View</label><br/><br/>
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
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
	
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	// now we can actually save the data
	$allowed = array( 
		'a' => array( // on allow a tags
			'href' => array() // and those anchords can only have href attribute
		)
	);
	
	// Probably a good idea to make sure your data is set
	if( isset( $_POST['my_meta_box_text'] ) )
		update_post_meta( $post_id, 'my_meta_box_text', wp_kses( $_POST['my_meta_box_text'], $allowed ) );
		
	if( isset( $_POST['my_meta_box_select'] ) )
		update_post_meta( $post_id, 'my_meta_box_select', esc_attr( $_POST['my_meta_box_select'] ) );
		
	// This is purely my personal preference for saving checkboxes
	$chk = ( isset( $_POST['my_meta_box_check'] ) && $_POST['my_meta_box_check'] ) ? 'on' : 'off';
	update_post_meta( $post_id, 'my_meta_box_check', $chk );
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