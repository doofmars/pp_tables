<?php

function formatPPDate($dateStr){
	if (!empty($dateStr) ){
		$date = DateTime::createFromFormat('d/m/Y', $dateStr);
		return $date->format('Y-m-d');	
	} else {
	return get_the_date("Y-m-d");
	}
}

function getPPRatingCount($postID){
	global $wpdb;
	$wpdb->get_results($wpdb->prepare("SELECT * FROM wp_YJYgB8jJ_pp_ratings WHERE post_id = %d", $postID));
	return $wpdb->num_rows;
}

function getPPPhilipsRating($postID){
	global $wpdb;
	return $wpdb->get_var($wpdb->prepare("SELECT rating FROM wp_YJYgB8jJ_pp_ratings WHERE post_id = %d and user_id = 1", $postID));
}

//Get the average rating
function getPPAverageRating($postID){      
	global $wpdb;  
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_YJYgB8jJ_pp_score WHERE post_id = %d AND counter > 0 ORDER BY rating", $postID ));
	if(!empty($results)){
		$score = round($results[0]->total/$results[0]->counter, 1);
		return $score;
	}
	return 0;
}

function get_relative_permalink( $url ) {
    return str_replace( home_url(), "", $url );
}

function getPPPlaytime(){
	if (has_tag('very-short')) {
		return 'Very Short';
	}
	else if (has_tag('short')) {
		return 'Short';
	}
	else if (has_tag('medium')) {
		return 'Medium';
	}
	else if (has_tag('long')) {
		return 'Long';
	}
	else if (has_tag('very-long') || has_tag('extra-long')) {
		return 'Extra Long';
	}
	else if (has_tag('never-ending')) {
		return 'Endless';
	} else {
		return '';
	}
}

function getPPGame() {
	if (in_category('half-life')) {
		return 'HL1';
	}
	if (in_category('half-life-2')) {
		return 'HL2';
	}
	if (in_category('hl2-ep1')) {
		return 'HL2:Ep1';
	}
	if (in_category('hl2-ep2')) {
		return 'HL2:Ep2';
	}
	if (in_category('black-mesa')) {
		return 'BM';
	}
	if (in_category('opposing-force')) {
		return 'OF';
	} else {
		return 'Not Set';
	};
}

function getPPType() {
	if (in_category('hl-maps')) {
		return 'Map';
	}
	if (in_category('hl-mods')) {
		return 'Mod';
	}
	if (in_category('bm-maps')) {
		return 'Map';
	}
	if (in_category('bm-mods')) {
		return 'Mod';
	}
	if (in_category('hl2-maps')) {
		return 'Map';
	}
	if (in_category('hl2-mods')) {
		return 'Mod';
	}
	if (in_category('hl2-ep1-maps')) {
		return 'Map';
	}
	if (in_category('hl2-ep1-mods')) {
		return 'Mod';
	}
	if (in_category('hl2-ep2-maps')) {
		return 'Map';
	}
	if (in_category('hl2-ep2-mods')) {
		return 'Mod';
	}
	if (in_category('of-mods')) {
		return 'Mod';
	} 
	if (in_category('of-maps')) {
		return 'Map';
	} else {
		return 'NC';
	}
}

//convert tags to string
function getPPTags(){
	$posttags = get_the_tags();
	$tags = "";
	if ($posttags) {
	  foreach($posttags as $tag) {
		$tags .= $tag->name . ' '; 
	  }
	}
	return $tags;
}

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
		delete_option("pp_table_update_date");
	} else {
			return "<b>$table_name already exists</b>";
	}
}


/* Register main update function */
function pp_tables_update_data($debug = false ) {
    global $wpdb;

	//Create query
    $query = 'cat=81&showposts=1000&nopaging=true';
	$count = 1;
	
	query_posts($query);
	while (have_posts()) : the_post(); 				

		pp_tables_update_row($wpdb, $debug);

		if(!$debug){
			echo ".";
			if ($count % 250 == 0){
				echo "<br>\n";
			}		
		}
		$count++;
		ob_flush();
        flush();
	endwhile;
    
	//Store Last update into WP-Options with the given format
	update_option("pp_table_update_date", date("g:ia l jS F Y"));
	return $count;
}

/*
	Function to hook to the save_post Wordpress hook
*/
function pp_table_update_post(){
	//We dont want to update if we dont have the update_after_post set to true or the post is not in category 81
	if (esc_attr( get_option('update_after_post')) != "true" || !in_category(81)) {
		return;
	}
	global $wpdb;
	pp_tables_update_row($wpdb);	
	}

/*
	Function to update a single row in the consolidation table
*/
function pp_tables_update_row($wpdb, $debug){
	$table_name = $wpdb->prefix . 'consolidation';	
	if($debug){
		echo "<p>";
		echo "Adding post " . get_the_ID();
		echo "</p>";
	}
	$post_row = array( 
		'PostID' => get_the_ID(), 
		'Name' => get_the_title(), 
		'URL' =>  get_relative_permalink(get_permalink()), 
		'Posted' => get_the_date("Y-m-d"), 
		'Released' => formatPPDate(get_post_meta(get_the_ID(), "ReleaseDate", true)), 
        'Downloads' => ppd_totalDownloadsTable(), 
		'Recs' => getPPRatingCount(get_the_ID()), 
		'AvRec' => getPPAverageRating(get_the_ID()), 
		'PhillipSays' => getPPPhilipsRating(get_the_ID()), 
		'Comments' => get_comments_number(), 
		'FileSizeT' => get_post_meta(get_the_ID(), "filesize", true), 
		'Playtime' => getPPPlaytime(), 
		'Game' => getPPGame(), 
		'Type' => getPPType(), 
		'Tags' => getPPTags(), 
	); 
	if($debug){
		echo "<p>";
		print_r($post_row);
		echo "</p>";
	}
	$wpdb->replace($table_name, $post_row);
}

//Wordpress hooks
add_action( 'save_post', 'pp_table_update_post' );

?>