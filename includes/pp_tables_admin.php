<?php
/** Add page link init function. */
add_action( 'admin_menu', 'pp_tables_menu_link' );

/** Register menu_page */
function pp_tables_menu_link() {
	add_options_page( 'PP Tables', 'PP Tables', 'manage_options', 'pp_table_menu_key', 'pp_tables_menu' );
	add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
	//register the settings
	register_setting( 'pp_table_settings', 'update_after_post' );
}

function pp_tables_menu() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<h2>PP Tables</h2>';
	echo '<p>Plugin to gather up the information for each post, which take a lot of time, and cache them into a consolidation table.</p>';
	$updateTime = get_option("pp_table_update_date", "Not yet" );
	echo "<p><b>Last updated: $updateTime</b></p>";
	
	if ($_POST['action'] == 'update_data') {
		$posts = pp_tables_update_data();
		echo "<p>$posts entities Updated</p>";
	}	
	if ($_POST['action'] == 'update_data_debug') {
		$posts = pp_tables_update_data(true);
		echo "<p>$posts entities Updated</p>";
	}
	?>
	
	<table>
		<tr>
			<td>
				<p>
					<form method="post" action="">
						<input type="hidden" name="action" value="update_data" />
						<input type="submit" class="button-primary" value="Update consolidation table" />
					</form>
				</p>
			</td>
			<td>
				<p>
					<form method="post" action="">
						<input type="hidden" name="action" value="update_data_debug" />
						<input type="submit" class="button-primary" value="Update consolidation table with output" />
					</form>
				</p>
			</td>
			<td>
				<p>
					<form method="post" action="">
						<input type="hidden" name="action" value="check_table" />
						<input type="submit" class="button-primary" value="Rebuild table" />
					</form>
				</p>
			</td>
		</tr>
	</table>
	
	<form method="post" action="options.php">
    <?php settings_fields( 'pp_table_settings' ); ?>
    <?php do_settings_sections( 'pp_table_settings' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Updating Behavior</th>
        <td>
        	<input type="checkbox" <?php if (esc_attr( get_option('update_after_post'))) { echo 'checked="checked"'; } ?>  value="true" name="update_after_post">
        	Update consolidation table after each wp-post update.
        </td>
        </tr>
    </table>
    
    <?php submit_button(); ?>
    <?php
	echo '</div>';
}
?>