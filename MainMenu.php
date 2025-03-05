<?php

include_once 'MenuBar.php';
include "HostFiles/Redirector.php";
include_once "Libraries/PlayerSettings.php";
include_once 'Assets/patreon-php-master/src/PatreonDictionary.php';
include_once "APIKeys/APIKeys.php";
include_once './AccountFiles/AccountDatabaseAPI.php';

// Check if the user is banned
if (isset($_SESSION["userid"]) && IsBanned($_SESSION["userid"])) {
  header("Location: ./PlayerBanned.php");
  exit;
}

if (!empty($_SESSION['error'])) {
  $error = $_SESSION['error'];
  unset($_SESSION['error']);
  echo "<script>alert('" . $error . "')</script>";
}

$language = TryGet("language", 1);
$settingArray = [];
$defaultFormat = 0;
$defaultVisibility = (isset($_SESSION["useruid"]) ? 1 : 0);
if (isset($_SESSION["userid"])) {
  $savedSettings = LoadSavedSettings($_SESSION["userid"]);
  for ($i = 0; $i < count($savedSettings); $i += 2) {
    $settingArray[$savedSettings[intval($i)]] = $savedSettings[intval($i) + 1];
  }
  if (isset($settingArray[$SET_Format]))
    $defaultFormat = $settingArray[$SET_Format];
  if (isset($settingArray[$SET_GameVisibility]))
    $defaultVisibility = $settingArray[$SET_GameVisibility];
}
$_SESSION['language'] = $language;
$isPatron = $_SESSION["isPatron"] ?? false;

$createGameText = ($language == 1 ? "Create Game" : "ゲームを作る");
$languageText = ($language == 1 ? "Language" : "言語");
$createNewGameText = ($language == 1 ? "Create New Game" : "新しいゲームを作成する");
$starterDecksText = ($language == 1 ? "Starter Decks" : "おすすめデッキ");
$deckUrl = TryGet("deckUrl", '');

$canSeeQueue = isset($_SESSION["useruid"]);

?>

<?php
include_once 'Header.php';
?>

<!-- <div style="background: linear-gradient(to bottom, #17130b33, #17130b55 10%, #17130b 80%, #17130b); position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: -1;">
</div> -->

<div class="core-wrapper">

  <div class="game-browser-wrapper">
    <div class="game-browser container bg-yellow" style='overflow-y:auto;'>
      <?php
      try {
        include "ServerChecker.php";
      } catch (\Exception $e) {
      }
      ?>
    </div>
  </div>

  <div class='create-game-wrapper'>
    <?php
    if (IsMobile())
      echo ("<div class='create-game container bg-yellow' style='overflow-y:visible'>");
    else
      echo ("<div class='create-game container bg-yellow' style='overflow-y:auto'>");
    ?>

    <!-- Tabs container -->
    <div class="create-game-tabs">
      <span class="create-game-tab active" onclick="openCreateGameTab(event, 'find-game')">Find Game</span>
      <span class="create-game-tab" onclick="openCreateGameTab(event, 'create-game')">Create Game</span>
    </div>

    <!-- Tab content for "Find Game" -->
    <div id="find-game" class="create-game-tab-content active">
      <!-- <h2>Find Game</h2> -->
      <!-- Content for finding existing games -->
      <form style='width:100%;display:inline-block;' action='<?= $redirectPath ?>/FindGame.php'>
        <?php
        $favoriteDecks = [];
        if (isset($_SESSION["userid"])) {
          $favoriteDecks = LoadFavoriteDecks($_SESSION["userid"]);
          if (count($favoriteDecks) > 0) {
            $selIndex = -1;
            if (isset($settingArray[$SET_FavoriteDeckIndex]))
              $selIndex = $settingArray[$SET_FavoriteDeckIndex];
            echo ("<div class='SelectDeckInput'>Favorite Decks");
            echo ("<select name='favoriteDecks' id='favoriteDecks'>");
            for ($i = 0; $i < count($favoriteDecks); $i += 4) {
              echo ("<option value='" . $i . "<fav>" . $favoriteDecks[$i] . "'" . ($i == $selIndex ? " selected " : "") . ">" . $favoriteDecks[$i + 1] . "</option>");
            }
            echo ("</select></div>");
          }
        }
        ?>
        <label for="fabdb">Deck Link (<u><a style='color:lightblue;' href='https://swustats.net/' target='_blank'>SWU
              Stats</a></u>,
          <u><a style='color:lightblue;' href='https://www.swudb.com/' target='_blank'>SWUDB</a></u>, or <u><a
              style='color:lightblue;' href='https://sw-unlimited-db.com/'
              target='_blank'>SW-Unlimited-DB</a></u>)</label>
        <input type="text" id="fabdb" name="fabdb" placeholder="Use the URL, Deck Link, or JSON text"
          value='<?= $deckUrl ?>'>
        <?php
        if (isset($_SESSION["userid"])) {
          echo ("<span class='save-deck'>");
          echo ("<labelfor='favoriteDeck'><input class='inputFavoriteDeck' type='checkbox' id='favoriteDeck' name='favoriteDeck' />");
          echo ("Save to Favorite Decks</label>");
          echo ("</span>");
        }
        ?>
        <label for="format" class='SelectDeckInput'>Format</label>
        <select name="format" id="format" onchange="toggleInfoBox()">
          <?php
          if ($canSeeQueue) {
            echo ("<option value='cc' " . ($defaultFormat == 0 ? " selected" : "") . ">Premier</option>");
            echo ("<option value='compcc' " . ($defaultFormat == 1 ? " selected" : "") . ">Request-Undo Premier</option>");
          }
          echo ("<option value='livinglegendscc'" . ($defaultFormat == 4 ? " selected" : "") . ">Open Format</option>");
          ?>
        </select>
        <div style='text-align:center;'>
          <input type="submit" class="create-game-button" value="Find Game">
        </div>
      </form>
    </div>

    <!-- Tab content for "Create Game" -->
    <div id="create-game" class="create-game-tab-content">
      <!-- <h2><?php echo ($createNewGameText); ?></h2> -->
      <form style='width:100%;display:inline-block;' action='<?= $redirectPath ?>/CreateGame.php'>
        <?php
        $favoriteDecks = [];
        if (isset($_SESSION["userid"])) {
          $favoriteDecks = LoadFavoriteDecks($_SESSION["userid"]);
          if (count($favoriteDecks) > 0) {
            $selIndex = -1;
            if (isset($settingArray[$SET_FavoriteDeckIndex]))
              $selIndex = $settingArray[$SET_FavoriteDeckIndex];
            echo ("<div class='SelectDeckInput'>Favorite Decks");
            echo ("<select name='favoriteDecks' id='favoriteDecks'>");
            for ($i = 0; $i < count($favoriteDecks); $i += 4) {
              echo ("<option value='" . $i . "<fav>" . $favoriteDecks[$i] . "'" . ($i == $selIndex ? " selected " : "") . ">" . $favoriteDecks[$i + 1] . "</option>");
            }
            echo ("</select></div>");
          }
        }
        ?>
        <label for="fabdb">Deck Link (<u><a style='color:lightblue;' href='https://swustats.net/' target='_blank'>SWU
              Stats</a></u>,
          <u><a style='color:lightblue;' href='https://www.swudb.com/' target='_blank'>SWUDB</a></u>, or <u><a
              style='color:lightblue;' href='https://sw-unlimited-db.com/'
              target='_blank'>SW-Unlimited-DB</a></u>)</label>
        <input type="text" id="fabdb" name="fabdb" placeholder="Use the URL, Deck Link, or JSON text"
          value='<?= $deckUrl ?>'>
        <?php
        if (isset($_SESSION["userid"])) {
          echo ("<span class='save-deck'>");
          echo ("<labelfor='favoriteDeck'><input class='inputFavoriteDeck' type='checkbox' id='favoriteDeck' name='favoriteDeck' />");
          echo ("Save to Favorite Decks</label>");
          echo ("</span>");
        }
        ?>
        <label for="gameDescription" class='game-name-label'>Game Name</label>
        <input type="text" id="gameDescription" name="gameDescription" placeholder="Game #">

        <label for="format" class='SelectDeckInput'>Format</label>
        <select name="format" id="format" onchange="toggleInfoBox()">
          <?php
          if ($canSeeQueue) {
            echo ("<option value='cc' " . ($defaultFormat == 0 ? " selected" : "") . ">Premier</option>");
            echo ("<option value='compcc' " . ($defaultFormat == 1 ? " selected" : "") . ">Request-Undo Premier</option>");
          }
          echo ("<option value='livinglegendscc'" . ($defaultFormat == 4 ? " selected" : "") . ">Open Format</option>");
          ?>
        </select>

        <label for="visibility" class='SelectDeckInput'>Visibility</label>
        <select name="visibility" id="visibility">
          <?php
          if ($canSeeQueue) {
            echo ("<option value='public' " . ($defaultVisibility == 1 ? " selected" : "") . ">Public</option>");
          }
          echo ("<option value='private' " . ($defaultVisibility == 0 ? " selected" : "") . ">Private</option>");
          ?>
        </select>

        <div style='text-align:center;'>
          <input type="submit" class="create-game-button" value="<?php echo ($createGameText); ?>">
        </div>
      </form>
    </div>
  </div>
</div>


<div class="petranaki-column">
  <div class="petranaki-overview container bg-yellow">
    <p><b>Petranaki is an open-source, fan-made platform.</b></p>
    <p>It is an educational tool only, meant to facilitate researching decks and strategies that is supportive of
      in-person play. As such, direct competition through the form of automated tournaments or rankings will not be
      added.</p>
    <p>This tool is free to use and is published non-commercially. Payment is not required to access any functionality.
    </p>
  </div>

  <div class="petranaki-news container bg-yellow" style='<?php if (IsMobile())
    echo ("display:none; "); ?>'>
    <h2>News</h2>
    <div style="position: relative;">
      <div style='vertical-align:middle; text-align:center;'>
        <img src="./Images/TWI Starter.png" width="320">
        <h3 style="margin: 15px 0; display: block;">We got a new look!</h3>
        <p>Petranaki.net will continue to be maintained by familiar devs OotTheMonk, Brubraz, ninin, and LVE while
          Karabast.net will be reserved for the New Engine Team and all the work they've been making excellent progress
          with! This is to ensure we still have the engine available to those who have been enjoying it in its current
          state</p>
      </div>
    </div>
    <?php
    /*
    if (!$isPatron) {
      echo '<div>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8442966023291783"
            crossorigin="anonymous"></script>
        <!-- MainMenu -->
        <ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-8442966023291783"
            data-ad-slot="5060625180"
            data-ad-format="auto"
            data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div>';
    }
    */
    ?>
  </div>

</div>

</div>
</div>

<script>
  function openCreateGameTab(evt, tabName) {
    var i, tabcontent, tablinks;

    // Hide all tab content
    tabcontent = document.getElementsByClassName("create-game-tab-content");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }

    // Remove the 'active' class from all tabs
    tablinks = document.getElementsByClassName("create-game-tab");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab content and add the 'active' class to the clicked tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
  }

  // Show the first tab by default
  document.getElementsByClassName("create-game-tab")[0].click();
</script>

<script>
  function changeLanguage() {
    window.location.search = '?language=' + document.getElementById('languageSelect').value;
  }

  function toggleInfoBox() {
    var formatSelect = document.getElementById('format');
    var infoBox = document.getElementById('info-box');
    if (formatSelect.value === 'openform') {
      infoBox.style.display = 'block';
    } else {
      infoBox.style.display = 'none';
    }
  }

  // Ensure the info box is displayed correctly based on the default selected format
  window.onload = function () {
    toggleInfoBox();
  };
</script>
<?php
include_once 'Disclaimer.php';
?>