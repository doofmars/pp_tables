<?php
/** Add page link init function. */
add_action( 'admin_menu', 'pp_tables_menu_link' );

/** Register menu_page */
function pp_tables_menu_link() {
	add_options_page( 'PP Tables', 'PP Tables', 'manage_options', 'pp_table_menu_key', 'pp_tables_menu' );
}

/** Step 3. */
function pp_tables_menu() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<h2>PP Tables</h2>';
	echo '<p>Plugin to gather up the information for each post, which take a lot of time, and cache them into a consolidation table.</p>';
	
	if ($_POST['action'] == 'update_data') {
		pp_tables_update_data();
		echo '<p>Data Updated</p>';
	}
	if ($_POST['action'] == 'check_table') {
		pp_tables_check_table();
		echo '<p>Table Updated</p>';
	}
	echo '<p><form method="post" action="">
			 <input type="hidden" name="action" value="update_data" />
			<input type="submit" class="button-primary" value="Update consolidation table" />
		  </form></p>';
	echo '<p><form method="post" action="">
			 <input type="hidden" name="action" value="check_table" />
			<input type="submit" class="button-primary" value="Check table" />
		  </form></p>';
	echo '</div>';
}
?>