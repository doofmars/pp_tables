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
	echo '<p>Here is where the form would go if I actually had options.</p>';
	echo '</div>';
}
?>