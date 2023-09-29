<?php
error_reporting(0);
include_once "../incl/lib/connection.php";
include_once "../config/api.php";
header('Content-Type: application/json');
if($_POST["key"] == $apiKey) {
	if(is_numeric($_POST["page"]) or !$_POST["page"]) {
		if($_POST["page"] > 0) {
			$page = $_POST["page"] - 1;
		} else {
			$page = 0;
		}
		$page *= 5;
		$query = $db->prepare("SELECT ID, name, authorName, size, download FROM songs ORDER BY ID DESC LIMIT $page, 5");
		$query->execute();
		$result = $query->fetchAll();
		if($result) {
			$songs = [];
			foreach($result as $song) {
				$songDL = str_replace(["%3A", "%2F", "%3F"], [":", "/", "?"], $song["download"]);
				array_push($songs, ["songID" => (int)$song["ID"], "songName" => $song["name"], "authorName" => $song["authorName"], "size" => (float)$song["size"], "download" => $songDL]);
			}
			echo json_encode(["success" => true, "songs" => $songs]);
		} else {
			echo json_encode(["success" => false, "message" => "Songs not found."]);
		}
	} else {
		echo json_encode(["success" => false, "message" => "Page can't be a string."]);
	}
} else {
	echo json_encode(["success" => false, "message" => "Invalid API key."]);
}
?>