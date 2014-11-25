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
	$updateTime = get_option("pp_table_update_date", "Not yet" );
	echo "<p><b>Last updated: $updateTime</b></p>";
	
	if ($_POST['action'] == 'update_data') {
		$posts = pp_tables_update_data();
		echo "<p>$posts entry's Updated</p>";
	}	
	if ($_POST['action'] == 'update_data_debug') {
		$posts = pp_tables_update_data(true);
		echo "<p>$posts entries Updated</p>";
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
			 <input type="hidden" name="action" value="update_data_debug" />
			<input type="submit" class="button-primary" value="Update consolidation table with output" />
		  </form></p>';
	echo '<p><form method="post" action="">
			 <input type="hidden" name="action" value="check_table" />
			<input type="submit" class="button-primary" value="Rebuild table" />
		  </form></p>';
	echo '</div>';
}
?>