<?php

include "./Libraries/HTTPLibraries.php";
include_once './includes/functions.inc.php';
include_once "./includes/dbh.inc.php";

session_start();

if (!isset($_SESSION["useruid"])) {
  echo ("Please login to view this page.");
  exit;
}
$useruid = $_SESSION["useruid"];
if ($useruid != "OotTheMonk" && $useruid != "love" && $useruid != "ninin" && $useruid != "Brubraz") {
  echo ("You must log in to use this page.");
  exit;
}

$playerToBan = TryGET("playerToBan", "");
$banReason = TryGET("banReason", "");
$banDays = TryGET("banDays", 30);
$ipToBan = TryGET("ipToBan", "");
$playerNumberToBan = TryGET("playerNumberToBan", "");
$playerToUnban = TryGET("playerToUnban", "");

if ($playerToBan != "") {
  BanPlayer($playerToBan);
}
if ($playerToUnban != "") {
  UnbanPlayer($playerToUnban);
}
if ($ipToBan != "") {
  $gameName = $ipToBan;
  include './MenuFiles/ParseGamefile.php';
  $ipToBan = ($playerNumberToBan == "1" ? $hostIP : $joinerIP);
  file_put_contents('./HostFiles/bannedIPs.txt', $ipToBan . "\r\n", FILE_APPEND | LOCK_EX);
}



header("Location: ./zzModPage.php");
