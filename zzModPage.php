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

echo ("<h1>Banned players:</h1>");
$banfileHandler = fopen("./HostFiles/bannedPlayers.txt", "r");
echo ("<div style='position:absolute; padding:10px; z-index:1; top:15%; left:35%; width:500px; height:650px;
  background-color:rgba(74, 74, 74, 0.9); border: 2px solid #1a1a1a; border-radius: 5px; overflow-y: scroll;'>");
while (!feof($banfileHandler)) {
  $bannedPlayer = fgets($banfileHandler);
  echo ($bannedPlayer . "<BR>");
}
fclose($banfileHandler);

echo ("<br><br><form  action='./BanPlayer.php'>");
?>
<label for="playerToBan" style='font-weight:bolder; margin-left:10px;'>Player to ban:</label>
<input type="text" id="playerToBan" name="playerToBan" value="">
<input type="submit" value="Ban">
</form>

<form action='./BanPlayer.php'>
<label for="playerToUnban" style='font-weight:bolder; margin-left:10px;'>Player to unban:</label>
<input type="text" id="playerToUnban" name="playerToUnban" value="">
<input type="submit" value="Unban">
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
echo ("<br><div>Most recently created accounts:</div>");
$userData = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_array($userData, MYSQLI_NUM)) {
  echo ($row[0] . "<BR>");
}
mysqli_close($conn);


echo ("<br><h1>Banned IPs:</h1>");
$banfileHandler = fopen("./HostFiles/bannedIPs.txt", "r");
while (!feof($banfileHandler)) {
  $bannedIP = fgets($banfileHandler);
  echo ($bannedIP . "<BR>");
}
fclose($banfileHandler);
?>

<br>
<form action='./BanPlayer.php'>
  <label for="ipToBan" style='font-weight:bolder; margin-left:10px;'>Game to IP ban from:</label>
  <input type="text" id="ipToBan" name="ipToBan" value="">
  <label for="playerNumberToBan" style='font-weight:bolder; margin-left:10px;'>Player to ban? (1 or 2):</label>
  <input type="text" id="playerNumberToBan" name="playerNumberToBan" value="">
  <input type="submit" value="Ban">
</form>

<br>

<form action='./CloseGame.php'>
  <label for="gameToClose" style='font-weight:bolder; margin-left:10px;'>Game to close:</label>
  <input type="text" id="gameToClose" name="gameToClose" value="">
  <input type="submit" value="Close Game">
</form>

<form action='./BootPlayer.php'>
  <label for="gameToClose" style='font-weight:bolder; margin-left:10px;'>Game:</label>
  <input type="text" id="gameToClose" name="gameToClose" value="">
  <label for="playerToBoot" style='font-weight:bolder; margin-left:10px;'>Player to boot:</label>
  <input type="text" id="playerToBoot" name="playerToBoot" value="">
  <input type="submit" value="Boot Player">
</form>

</div>