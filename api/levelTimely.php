<?php
error_reporting(0);
include_once "../incl/lib/connection.php";
include_once "../config/api.php";
require_once "../incl/lib/mainLib.php";
header("Content-Type: application/json");
if($_POST["key"] == $apiKey) {
	$gs = new mainLib();
	$type = $_POST["type"];
	if($type == "0" or $type == "1") {
		$qTimely = $db->prepare("SELECT levelID FROM dailyfeatures WHERE timestamp < :time AND type = :type ORDER BY timestamp DESC LIMIT 1");
		$qTimely->execute([':time' => time(), ':type' => $type]);
		if($qTimely->rowCount()) {
			$timelyID = $qTimely->fetchColumn();
			$query = $db->prepare("SELECT * FROM levels WHERE levelID = :id");
			$query->execute([':id' => $timelyID]);
			$levelInfo = $query->fetch();
			$difficulty = $gs->getDifficulty($levelInfo["starDifficulty"], $levelInfo["starAuto"], $levelInfo["starDemon"]);
			if($levelInfo["starDemon"]) {
				$difficulty = $gs->getDemonDiff($levelInfo["starDemonDiff"]) . " Demon";
			}
			$desc = base64_decode($levelInfo["levelDesc"]);
			if(!$desc) {
				$desc = "No description provided.";
			}
			$length = $gs->getLength($levelInfo["levelLength"]);
			if($levelInfo["songID"]) {
				$query = $db->prepare("SELECT ID, name, authorName FROM songs WHERE ID = :songID");
				$query->execute([':songID' => $levelInfo["songID"]]);
				$songInfo = $query->fetch();
				$song = $songInfo["name"] . " by " . $songInfo["authorName"] . " | " . $songInfo["ID"];
			} else {
				$song = $gs->getAudioTrack($levelInfo["audioTrack"]);
			}
			echo json_encode(["success" => true, "level" => ["ID" => (int)$levelInfo["levelID"], "name" => $levelInfo["levelName"], "description" => $desc, "version" => (int)$levelInfo["levelVersion"], "author" => $levelInfo["userName"], "objects" => (int)$levelInfo["objects"], "coins" => (int)$levelInfo["coins"], "stars" => (int)$levelInfo["starStars"], "starCoins" => (bool)$levelInfo["starCoins"], "featured" => (bool)$levelInfo["starFeatured"], "epic" => (bool)$levelInfo["starEpic"], "difficulty" => $difficulty, "length" => $length, "downloads" => (int)$levelInfo["downloads"], "likes" => (int)$levelInfo["likes"], "song" => $song]]);
		} else {
			echo json_encode(["success" => false, "message" => "Level not found."]);
		}
	} else {
		echo json_encode(["success" => false, "message" => "Invalid type."]);
	}
} else {
	echo json_encode(["success" => false, "message" => "Invalid API key."]);
}
?>