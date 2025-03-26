<?php
session_start();
if (!isset($_SESSION['userid'])) {
  echo json_encode(["error" => "Not logged in"]);
  exit;
}

$sessionUserid = $_SESSION['userid'];
$requestUserid = $_GET['userid'] ?? '';

if ($sessionUserid != $requestUserid) {
  echo json_encode(["error" => "Unauthorized access"]);
  exit;
}

$settingPiece = $_GET['piece'] ?? '';
$settingValue = $_GET['value'] ?? '';

if ($settingPiece === '' || $settingValue === '') {
  echo json_encode(["error" => "Missing parameters"]);
  exit;
}

$settingPiece = htmlentities($settingPiece, ENT_QUOTES);
$settingValue = htmlentities($settingValue, ENT_QUOTES);

$settingPiece = str_replace("'", "\'", $settingPiece);
$settingValue = str_replace("'", "\'", $settingValue);
session_write_close();
include_once "../Libraries/PlayerSettings.php";
include_once "../includes/functions.inc.php";
include_once "../includes/dbh.inc.php";
if(!SaveSettingInDatabase($settingPiece))
{
  echo json_encode(["error" => "Invalid setting"]);
  exit;
}

ChangeSetting("", $settingPiece, $settingValue, $sessionUserid);
echo json_encode(["success" => "Setting updated"]);
exit;
?>