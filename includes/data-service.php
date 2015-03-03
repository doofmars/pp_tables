<?php
require_once( '../../../../wp-load.php' );
global $wpdb;
$table_name = $wpdb->prefix . 'consolidation';
$results = $wpdb->get_results("SELECT * FROM $table_name ");
header('Content-Type: application/json');

$first = true;

echo "[\n";
foreach ( $results as $result ) 
{	
	if ($first) {
		$first = false;
	} else {
		echo ",\n";
	}

	if () {
		# code...
	} else {
		# code...
	}
	
	
	echo "  {\n";
	echo '    "Name": "' . get_site_url() .  $result->URL . "||" . $result->Name . "\",\n";
	echo '    "Posted": "' . $result->Posted . "\",\n";
	echo '    "Released": "' . $result->Released . "\",\n";
	echo '    "Downloads": "' . $result->Downloads . "\",\n";
	echo '    "Recs": "' . $result->Recs . "\",\n";
	echo '    "AvRec": "' . $result->AvRec . "\",\n";
	echo '    "PhillipSays": "' . $result->PhillipSays . "\",\n";
	echo '    "Comments": "' . $result->Comments . "\",\n";
	echo '    "FileSizeT": "' . $result->FileSizeT . "\",\n";
	echo '    "Game": "' . $result->Game . "\",\n";
	echo '    "Type": "' . $result->Type . "\",\n";
	echo '    "Tags": "' . $result->Tags . "\"\n";
	echo "  }";
}
echo "\n]";

?>