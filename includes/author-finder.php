<?php
require_once( '../../../../wp-load.php' );
global $wpdb;

$inputtable = "wp_YJYgB8jJ_posts";
$outputtable = "wp_YJYgB8jJ_postmeta";
$start ="<li><strong>Author: </strong>";
$end ="</li>";

$results = $wpdb->get_results("SELECT ID, post_content FROM $inputtable WHERE post_content LIKE '%$start%'");

$resultA = "";
$resultB = "";
$resultC = "";

//Enable to get plaintext
//header('Content-Type: text/plain'); 


foreach ( $results as $result ) {
	$posStart = stripos($result->post_content, $start);
	$stringA = substr($result->post_content, $posStart);
	$posEnd = stripos($stringA, $end);
	$stringB = substr($stringA, 0, $posEnd);
	$stringC = substr($stringB, strlen($start));
	
	$author = get_post_meta($result->ID, "Author", true);
	if ($author != "" ) {
		if ($stringC === $author) {
			$resultA .= $result->ID . " " . $stringC . "###" . $author . "<br>\n";
		} else {
			$resultB .= $result->ID . " " . $stringC . "###" . $author . "<br>\n";
		}
	} else {
			$resultC .= "INSERT INTO $outputtable (`meta_id` ,`post_id` ,`meta_key` ,`meta_value`) VALUES (NULL , '" .$result->ID . "', 'Author', '" . $stringC ."');<br>\n";
	}
}

echo "<h1>Results with the same Author</h1><br>\n";
echo $resultA;
echo "<h1>Results with different Author</h1><br>\n";
echo $resultB;
echo "<h1>Results without Author</h1><br>\n";
echo $resultC;

?>