<?php
error_reporting(0);
include_once "../incl/lib/connection.php";
include_once "../config/api.php";
require_once "../incl/lib/mainLib.php";
header("Content-Type: application/json");
if($_POST["key"] == $apiKey) {
	$gs = new mainLib();
	$result = $gs->songReupload($_POST["link"]);
	header("Content-Type: application/json"); // Because songReupload replaced a header.
	switch($result) {
		case -4:
			echo json_encode(["success" => false, "message" => "This URL doesn't point to a valid audio file."]);
			break;
		case -3:
			echo json_encode(["success" => false, "message" => "This song already exists in database."]);
			break;
		case -2:
			echo json_encode(["success" => false, "message" => "The download link isn't a valid URL."]);
			break;
		default:
			echo json_encode(["success" => true, "songID" => (int)$result]);
	}
} else {
	echo json_encode(["success" => false, "message" => "Invalid API key."]);
}
?>