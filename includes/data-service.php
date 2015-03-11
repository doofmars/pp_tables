<?php
require_once( '../../../../wp-load.php' );
global $wpdb;
$table_name = $wpdb->prefix . 'consolidation';
$results = $wpdb->get_results("SELECT * FROM $table_name ");
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
		echo "    \"<a href='" . get_site_url() .  $result->URL . "'>" . $result->Name . "</a>\",\n";
		echo '    "' . get_current_user_id() . "\",\n";
		echo '    "' . $result->Posted . "\",\n";
		echo '    "' . $result->Released . "\",\n";
		echo '    "' . $result->Downloads . "\",\n";
		echo '    "' . $result->Recs . "\",\n";
		echo '    ' . json_encode(getRatingColorTable($result->AvRec) . "<span class=\"" . getRatingColor($result->AvRec) . "\">AR" . $result->AvRec . "</span></span>") . ",\n";
		echo '    ' . json_encode(getPhillipSaysTable($result->PhillipSays)) . ",\n";
		echo '    "' . $result->FileSizeT . "\",\n";
		echo '    "' . $result->Comments . "\",\n";
		echo '    "' . $result->Game . "\",\n";
		echo '    "' . $result->Type . "\",\n";
		echo '    "' . $result->Tags . "\"\n";
		echo "  ]";
	}
}
echo "\n]}";
?>