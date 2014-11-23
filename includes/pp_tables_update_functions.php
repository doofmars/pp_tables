<?php

function formatPPDate($dateStr){
	if (!empty($dateStr) ){
		$date = DateTime::createFromFormat('d/m/Y', $dateStr);
		return $date->format('Y-m-d');	
	} else {
	return $dateStr;
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
	else if (has_tag('very-long')) {
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
		return ' Mod';
	}
	if (in_category('of-mods')) {
		return 'Mod';
	} 
	if (in_category('of-maps')) {
		return 'Map';
	} else {
		return 'Unknown';
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
	} else {
			return "<b>$table_name already exists</b>";
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
	$count = 1;
	
	query_posts($query);
	while (have_posts()) : the_post(); 
		//echo "<p>";
				
		$test = array( 
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
		$wpdb->update($table_name, $test, array('PostID' => get_the_ID()));
		
		echo ".";
		if ($count % 250 == 0){
			echo "<br>\n";
		}
		$count++;
		//echo "</p>";
	endwhile;
    
}

?>