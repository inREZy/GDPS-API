<?php
error_reporting(0);
include_once "../incl/lib/connection.php";
include_once "../config/api.php";
require_once "../incl/lib/exploitPatch.php";
header('Content-Type: application/json');
if($_POST["key"] == $apiKey) {
	$userName = ExploitPatch::remove($_POST["user"]);
	if(is_numeric($_POST["page"]) or !$_POST["page"]) {
		if($_POST["page"] > 0) {
			$page = $_POST["page"] - 1;
		} else {
			$page = 0;
		}
		$page *= 5;
		$qExtID = $db->prepare("SELECT extID FROM users WHERE userName = :userName AND isRegistered = 1");
		$qExtID->execute([':userName' => $userName]);
		$extID = $qExtID->fetchColumn();
		if($qExtID->rowCount()) {
			$qLevels = $db->prepare("SELECT levelID, levelName FROM levels WHERE extID = :extID ORDER BY levelID DESC LIMIT $page, 5");
			$qLevels->execute([':extID' => $extID]);
			if($qLevels->rowCount()) {
				$levels = [];
				foreach($qLevels->fetchAll() as $level) {
					array_push($levels, ["levelID" => (int)$level["levelID"], "levelName" => $level["levelName"]]);
				}
				echo json_encode(["success" => true, "levels" => $levels]);
			} else {
				echo json_encode(["success" => false, "message" => "Levels by this user not found."]);
			}
		} else {
			echo json_encode(["success" => false, "message" => "The user doesn't exist."]);
		}
	} else {
		echo json_encode(["success" => false, "message" => "Page can't be a string."]);
	}
} else {
	echo json_encode(["success" => false, "message" => "Invalid API key."]);
}
?>