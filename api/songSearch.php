<?php
error_reporting(0);
include_once "../incl/lib/connection.php";
include_once "../config/api.php";
require_once "../incl/lib/exploitPatch.php";
header('Content-Type: application/json');
if($_POST["key"] == $apiKey) {
	$str = ExploitPatch::remove($_POST["query"]);
	if($str) {
		$query = $db->prepare("(SELECT ID, name, authorName, size, download FROM songs WHERE ID = :str) UNION (SELECT ID, name, authorName, size, download FROM songs WHERE name LIKE CONCAT('%', :str, '%')) ORDER BY ID DESC LIMIT 5");
		$query->execute([':str' => $str]);
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
		echo json_encode(["success" => false, "message" => "Query should not be empty."]);
	}
} else {
	echo json_encode(["success" => false, "message" => "Invalid API key."]);
}
?>