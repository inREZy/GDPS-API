<?php
error_reporting(0);
include_once "../incl/lib/connection.php";
include_once "../config/api.php";
require_once "../incl/lib/mainLib.php";
header("Content-Type: application/json");
if($_POST["key"] == $apiKey) {
	$gs = new mainLib();
	if($_POST["id"]) {
		if(is_numeric($_POST["id"])) {
			$query = $db->prepare("SELECT * FROM levels WHERE levelID = :id");
			$query->execute([':id' => $_POST["id"]]);
			if($query->rowCount()) {
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
				echo json_encode(["success" => false, "message" => "The level you are searching for doesn't exist."]);
			}
		} else {
			echo json_encode(["success" => false, "message" => "ID can't be a string."]);
		}
	} else {
		echo json_encode(["success" => false, "message" => "ID should not be empty."]);
	}	
} else {
	echo json_encode(["success" => false, "message" => "Invalid API key."]);
}
?>
