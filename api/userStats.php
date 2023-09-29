<?php
error_reporting(0);
include_once "../incl/lib/connection.php";
include_once "../config/api.php";
require_once "../incl/lib/exploitPatch.php";
header('Content-Type: application/json');
if($_POST["key"] == $apiKey) {
	$userName = ExploitPatch::remove($_POST["user"]);
	$query = $db->prepare("SELECT * FROM users WHERE userName = :userName AND isRegistered = 1");
	$query->execute([':userName' => $userName]);
	if($query->rowCount()) {
		$userInfo = $query->fetch();
		$query = $db->prepare("SELECT youtubeurl, twitter, twitch FROM accounts WHERE accountID = :extID");
		$query->execute([':extID' => $userInfo["extID"]]);
		$accInfo = $query->fetch();
		$youtube = "None";
		if($accInfo["youtubeurl"]) {
			$youtube = "http://youtube.com/channel/" . $accInfo["youtubeurl"];
		}
		$twitter = "None";
		if($accInfo["twitter"]) {
			$twitter = "http://twitter.com/" . $accInfo["twitter"];
		}
		$twitch = "None";
		if($accInfo["twitch"]) {
			$twitch = "http://twitch.tv/" . $accInfo["twitch"];
		}
		$links = ["youtube" => $youtube, "twitter" => $twitter, "twitch" => $twitch];
		echo json_encode(["success" => true, "userInfo" => ["name" => $userInfo["userName"], "stars" => (int)$userInfo["stars"], "demons" => (int)$userInfo["demons"], "coins" => (int)$userInfo["coins"], "userCoins" => (int)$userInfo["userCoins"], "creatorPoints" => (int)$userInfo["creatorPoints"], "diamonds" => (int)$userInfo["diamonds"], "moons" => (int)$userInfo["moons"], "orbs" => (int)$userInfo["orbs"], "completedLevels" => (int)$userInfo["completedLvls"], "isBanned" => (bool)$userInfo["isBanned"], "isCreatorBanned" => (bool)$userInfo["isCreatorBanned"], "links" => $links]]);
	} else {
		echo json_encode(["success" => false, "message" => "The user you are searching for doesn't exist."]);
	}
} else {
	echo json_encode(["success" => false, "message" => "Invalid API key."]);
}
?>