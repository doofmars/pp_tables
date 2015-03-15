<?php
require_once( '../../../../wp-load.php' );
global $wpdb;

function getReviewed($id) {
	global $wpdb;
	$user = wp_get_current_user();
   //Check if user has reviewed this 
	if( is_user_logged_in() ){
		$table_name = $wpdb->prefix . 'pp_ratings';
		$getReviews = $wpdb->get_row($wpdb->prepare("SELECT comment_id from $table_name WHERE post_id = %d AND user_id = %d AND next_rating = 0", $id, $user->ID));
		if($wpdb->num_rows > 0){
			return '<span class="hint--top hint--rounded hint--bounce hint--gstar5hint" data-hint="Yes, you have reviewed this release"><span class="greenish">R:Yes <i class="fa fa-check"></i></span></span>';
		} else { 
			return '<span class="hint--top hint--rounded hint--bounce hint--gstar1hint" data-hint="No, you have not reviewed this release"><span class="reddish">R:No <i class="fa fa-times"></i></span></span>';
		}
	} else {
		return '<span class="hint--top hint--rounded hint--bounce hint--greyhint" data-hint="Login to see if you have reviewed this release"><span class="small">Login</span></span>';
	}
}

$table_name = $wpdb->prefix . 'consolidation';
if (isset($_GET['game'])) {
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE Game = %s", $_GET['game']));
} else {
	$results = $wpdb->get_results("SELECT * FROM $table_name ");
}
header('Content-Type: application/json');

$first = true;

echo "{ \"data\" : [\n";
foreach ( $results as $result ) 
{	
	if ($result->Posted != "0000-00-00") {
		if ($first) {
			$first = false;
		} else {
			echo ",\n";
		}
		echo "  [\n";
		echo '    ' . json_encode('<a href="' . get_site_url() .  $result->URL . '">' . $result->Name . '</a>'). ",\n";
		if (isset($_GET['getReviewed'])) {
			echo '    ' . json_encode(getReviewed($result->PostID)) . ",\n";
		}
		echo '    "' . date_format(date_create($result->Posted),"d M Y") . "\",\n";
		echo '    "' . date_format(date_create($result->Released),"d M Y") . "\",\n";
		echo '    ' . $result->Downloads . ",\n";
		echo '    ' . $result->Recs . ",\n";
		echo '    ' . json_encode(getRatingColorTable($result->AvRec) . "<span class=\"" . getRatingColor($result->AvRec) . "\">AR" . $result->AvRec . "</span></span>") . ",\n";
		echo '    ' . json_encode(getPhillipSaysTable($result->PhillipSays)) . ",\n";
		echo '    ' . $result->FileSizeT . ",\n";
		echo '    ' . json_encode(getPlaytimeTable($result->Playtime)) . ",\n";
		if (!isset($_GET['game'])) {
			echo '    "' . $result->Game . "\",\n";
		}
		echo '    "' . $result->Type . "\",\n";
		echo '    "' . $result->Tags . "\"\n";
		echo "  ]";
	}
}
echo "\n]}";
?>