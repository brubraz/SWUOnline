<?php

  use Patreon\API;
  use Patreon\OAuth;

function PatreonLogin($access_token, $silent=true, $debugMode=false)
{
  $output = new stdClass();
  $output->patreonCampaigns = [];
  if($access_token == "") return $output;
  if($access_token == "PERMANENT")
  {
    $_SESSION["isPatron"] = true;
    return $output;
  }

  $api_client = new API($access_token);
	$api_client->api_return_format = 'object';

	$patron_response = $api_client->fetch_patron_campaigns();

  if(is_string($patron_response))
  {
    if(!$silent) echo($patron_response);
    return $output;
  }

	$patron = $patron_response->data;

	$relationships = $patron->relationships;
	if(isset($relationships)) $memberships = $relationships->memberships;

  if(!isset($patron_response->included)) return $output;
  $yourPatronages = [];
  $activeStatus = [];
	for($i=0; $i<count($patron_response->included); ++$i)
	{
    $_SESSION["patreonAuthenticated"] = true;
		$include = $patron_response->included[$i];
    if($debugMode)
    {
      echo($include->id . " ");
      if($include->attributes && isset($include->attributes->patron_status)) echo($include->attributes->patron_status . " " . $include->relationships->campaign->data->id);
      else if(isset($include->attributes->creation_name)) echo($include->attributes->creation_name);
      echo("<BR>");
    }
    if($include->attributes && isset($include->attributes->patron_status))
    {
      $activeStatus[$include->relationships->campaign->data->id] = $include->attributes->patron_status;
    }
    if($include->type == "campaign" && (!isset($activeStatus[$include->id]) || $activeStatus[$include->id] == "former_patron")) continue;

    if($include->type == "campaign")
    {
      $campaign = PatreonCampaign::tryFrom($include->id);
      if($campaign != null)
      {
        $_SESSION[$campaign->SessionID()] = true;
        $campaignName = $campaign->CampaignName();
  			$yourPatronages[] = $campaignName;
        $output->patreonCampaigns[] = $campaignName;
      }
    }
	}

  if(!$silent)
  {
    echo("<h2 style='margin: 8px 0 16px 0;'>Your patronages:</h2>");
    echo("<ul style='display: block; margin-bottom: 24px; list-style-type: disc; list-style-position: inside;'>");
    for($i=0; $i<count($yourPatronages); ++$i)
    {
      echo("<li style='font-size: 20px; display: list-item; font-weight: bold; margin-bottom: 8px;'>" . $yourPatronages[$i] . "</li>");
    }
    echo("</ul>");
    echo("<h4 style='margin-bottom: 8px;'>Not seeing something you expect to see?</h4>");
    echo("<h4 style='margin-bottom: 8px;'>1. Check your patreon page to make sure it's listed in your currently supported campaigns</h4>");
    echo("<h4 style='margin-bottom: 24px;'>2. Reach out on our discord server!</h4>");
  }
  return $output;
}

?>
