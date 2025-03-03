<?php

include_once 'MenuBar.php';

include_once './includes/functions.inc.php';
include_once "./includes/dbh.inc.php";

if (!isset($_SESSION["useruid"])) {
  echo ("Please login to view this page.");
  exit;
}
$useruid = $_SESSION["useruid"];
if ($useruid != "OotTheMonk" && $useruid != "love" && $useruid != "ninin" && $useruid != "Brubraz") {
  echo ("You must log in to use this page.");
  exit;
}

echo ("<h1 style='padding-left: 30px; padding-top: 20px;'>Moderation Tools</h1>");
echo ("<div style='position:absolute; z-index:1; top:15%; left:50%; transform:translateX(-50%); width:100%; max-width:500px; max-height:70vh;
  background-color:rgba(74, 74, 74, 0.9); border: 2px solid #1a1a1a; border-radius: 5px; overflow-y: scroll; padding: 32px;'>");
echo ("<h2 style='margin-bottom: 16px;'>Banned Players</h2>");
echo ("<ul style='display:block; margin-left: 20px;'>");
$bannedPlayers = ListBannedPlayers();
foreach($bannedPlayers as $bannedPlayer) {
  echo ("<li style='display: list-item; list-style: disc;'>" . $bannedPlayer . "</li>");
}
echo ("</ul>");

echo ("<br><form  action='./BanPlayer.php'>");
?>

<div style="margin: 20px 0px; border-top: 1px solid #666; border-bottom: 1px solid #666;">
  <h2 style="margin-top: 20px;">Ban Player</h2>

  <div style="margin-top: 24px;">
    <label for="playerToBan" style='font-weight:bolder; display:block;'>Player to ban:</label>
    <input id="playerToBan" type="text" name="playerToBan" required style="width:100%;">
  </div>

  <div style="margin-top: 4px;">
    <label for="banReason" style='font-weight:bolder; display:block; margin-bottom:8px;'>Ban reason:</label>
    <textarea id="banReason" name="banReason" rows="4" cols="50" required style="width:100%;"></textarea>
  </div>

  <div style="margin-top: 16px;">
    <label for="banDays" style='font-weight:bolder; display:block;'>Ban duration (days):</label>
    <input type="number" id="banDays" name="banDays" min="1" required style="width:100%;">
  </div>

  <input type="submit" value="Ban" style="min-width:100px;" required>
</div>

</form>

<form action='./BanPlayer.php' style="border-bottom: 1px solid #666;">

<h2 style="margin-top: 20px;">Unban Player</h2>
<label for="playerToUnban" style='font-weight:bolder; display:block;'>Player to unban:</label>
<input type="text" id="playerToUnban" name="playerToUnban" required style="width:100%; display:block;">
<input type="submit" value="Unban" style="min-width:100px;">
</form>


<?php
$conn = GetDBConnection();
$sql = "SELECT usersUid FROM users ORDER BY usersId DESC LIMIT 20";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
  //header("location: ../Signup.php?error=stmtfailed");
  echo ("ERROR");
  exit();
}

//mysqli_stmt_bind_param($stmt, "ss", $username, $email);
mysqli_stmt_execute($stmt);

// "Get result" returns the results from a prepared statement
echo ("<br><h2>Most recently created accounts:</h2>");
echo ("<ul style='display:block; margin-left: 20px;'>");
$userData = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_array($userData, MYSQLI_NUM)) {
  echo ("<li style='display: list-item; list-style: disc;'>" . $row[0] . "</li>");
}
echo ("</ul>");
mysqli_close($conn);

echo ("<div style='border-bottom: 1px solid #666; height: 1px; margin-top: 20px;'/>");
echo ("<br><h2>Banned IPs:</h2>");
echo ("<ul style='display:block; margin-left: 20px;'>");
$banfileHandler = fopen("./HostFiles/bannedIPs.txt", "r");
while (!feof($banfileHandler)) {
  $bannedIP = fgets($banfileHandler);
  if ($bannedIP != "") {
    echo ("<li style='display: list-item; list-style: disc;'>" . $bannedIP . "</li>");
  }
}
fclose($banfileHandler);
echo ("</ul>");
?>

<br>
<form action='./BanPlayer.php' style="border-bottom: 1px solid #666; border-top: 1px solid #666;">
  <label for="ipToBan" style='font-weight:bolder; display:block; margin-top: 20px;'>Game to IP ban from:</label>
  <input type="text" id="ipToBan" name="ipToBan" value="" style="width:100%; display:block;">
  <label for="playerNumberToBan" style='font-weight:bolder; margin-left:10px; display:block;'>Player to ban? (1 or 2):</label>
  <input type="text" id="playerNumberToBan" name="playerNumberToBan" value="" style="width:100%; display:block;">
  <input type="submit" value="Ban" style="min-width:100px;">
</form>

<br>

<form action='./CloseGame.php' style="border-bottom: 1px solid #666; margin-bottom: 20px;">
  <label for="gameToClose" style='font-weight:bolder; display:block;'>Game to close:</label>
  <input type="text" id="gameToClose" name="gameToClose" value="" style="width:100%; display:block;">
  <input type="submit" value="Close Game" style="min-width:100px;">
</form>

<form action='./BootPlayer.php'>
  <label for="gameToClose" style='font-weight:bolder; display:block;'>Game:</label>
  <input type="text" id="gameToClose" name="gameToClose" value="" style="width:100%; display:block;">
  <label for="playerToBoot" style='font-weight:bolder; display:block;'>Player to boot:</label>
  <input type="text" id="playerToBoot" name="playerToBoot" value="" style="width:100%; display:block;">
  <input type="submit" value="Boot Player" style="min-width:100px;">
</form>

</div>