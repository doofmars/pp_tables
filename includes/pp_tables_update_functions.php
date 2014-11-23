<?php

/** Function to check and create the consolation table */
function pp_tables_check_table(){
	global $wpdb;
	
    $table_name = $wpdb->prefix . 'consolidation';

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != strtolower($table_name)) {
		$sql = "CREATE TABLE $table_name (
			  PostID int(11) NOT NULL,
			  Name text COLLATE utf8_unicode_ci NOT NULL,
			  URL text COLLATE utf8_unicode_ci NOT NULL,
			  Posted date NOT NULL,
			  Released date NOT NULL,
			  Downloads int(11) NOT NULL,
			  Recs int(11) NOT NULL,
			  AvRec int(11) NOT NULL,
			  PhillipSays int(11) NOT NULL,
			  Comments int(11) NOT NULL,
			  FileSizeT decimal(20,2) NOT NULL,
			  Playtime text COLLATE utf8_unicode_ci NOT NULL,
			  Game text COLLATE utf8_unicode_ci NOT NULL,
			  Type text COLLATE utf8_unicode_ci NOT NULL,
			  Tags text COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (PostID))ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		print_r(dbDelta( $sql ));    
	} else {
		echo "<b>$table_name already exists</b>";
	}
}

/** Register main update function */
function pp_tables_update_data() {
    global $wpdb;
	$table_name = $wpdb->prefix . 'consolidation';

	//Cat: All Games
    $catID = 81;
	//Create query
    $query = 'cat='.$catID.'&showposts=1000&nopaging=true';
	
	$welcome_text = "none";
	
	query_posts($query);
	while (have_posts()) : the_post(); 
		echo "<p>";
		$test = array( 
				'PostID' => get_the_ID(), 
				'Name' => get_the_title(), 
				'URL' =>  get_permalink(), 
				'Posted' => get_the_time(), 
				'Released' => get_post_meta(get_the_ID(), "ReleaseDate", true), 
				'Downloads' => ppd_totalDownloadsTable(), 
				'Recs' => 0, 
				'AvRec' => 0, 
				'PhillipSays' => 0, 
				'Comments' => get_comments_number(), 
				'FileSizeT' => get_post_meta(get_the_ID(), "filesize", true), 
				'Playtime' => "TODO", 
				'Game' => "TODO", 
				'Type' => "TODO", 
				'Tags' => "TODO", 
			); 
		//$wpdb->insert($table_name, $test);
		print_r(get_the_title());
		echo "</p>";
	endwhile;
    
}

?>