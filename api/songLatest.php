<?php
error_reporting(0);
include_once "../incl/lib/connection.php";
include_once "../config/api.php";
header('Content-Type: application/json');
if($_POST["key"] == $apiKey) {
    $query = $db->prepare("SELECT ID, name, authorName, size, download FROM songs ORDER BY ID DESC LIMIT 1");
    $query->execute();
    $song = $query->fetch();
    if($song) {
        $songDL = str_replace(["%3A", "%2F", "%3F"], [":", "/", "?"], $song["download"]);
        echo json_encode(["success" => true, "songID" => (int)$song["ID"], "songName" => $song["name"], "authorName" => $song["authorName"], "size" => (float)$song["size"], "download" => $songDL]);
    } else {
        echo json_encode(["success" => false, "message" => "Latest song not found."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid API key."]);
}
?>