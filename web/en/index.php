English league<br>
<?php
include '../includes.php';

###################### Variables declarations #########################

$url="http://api.football-data.org/v1/soccerseasons/398/fixtures/?timeFrame=p1";//PremierLeague

$slackhookurl = 'https://hooks.slack.com/services/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'; //premierleague

$flag=":flag-gb:";

$teamsacronyms = array('Arsenal FC'=>'Arsenal',
'Leicester City FC'=>'Leicester',
'Tottenham Hotspur FC'=>'Tottenham',
'Manchester City FC'=>'City',
'Crystal Palace FC'=>'Crystal',
'Manchester United FC'=>'Man. U',
'West Ham United FC'=>'West Ham',
'Watford FC'=>'Watford',
'Stoke City FC'=>'Stoke',
'Liverpool FC'=>'Liverpool',
'Everton FC'=>'Everton',
'Southampton FC'=>'Southampton',
'West Bromwich Albion FC'=>'Albion',
'Chelsea FC'=>'Chelsea',
'Norwich City FC'=>'Norwich',
'AFC Bournemouth'=>'Bournemouth',
'Swansea City FC'=>'Swansea',
'Newcastle United FC'=>'Newcastle',
'Sunderland AFC'=>'Sunderland',
'Aston Villa FC'=>'Aston Villa');

$prefixbackupfile="gb_";
$backuptrigger = "false";
$numberofupdates=0;

#######################################################################
###################### Fetching data from API #########################

$objectapireturn = fetchdatafromapi2($url);
$arrayapireturn = objectToArray($objectapireturn);

// print_r($arrayapireturn);

#######################################################################
###################### Fetching data from back up #####################

$pathtofile = "./".$prefixbackupfile."matchesbackup.json";
$gamesinbackup = json_decode(file_get_contents($pathtofile), true);

// print_r($gamesinbackup);

#######################################################################
############## Comparing realtime data (API) with backup ##############

// Loop to parse real time game(s)
foreach ($arrayapireturn['fixtures'] as $gameretrieve) {

	//Loop to parse back up game(s)
	foreach ($gamesinbackup['fixtures'] as $fixtureinbackup) {

		//Main condition => Assuming that a single home team is playing once in 24 hours
		if ($fixtureinbackup['homeTeamName'] == $gameretrieve['homeTeamName']) {
				
			//echo "<br>There is a home team name that matches ! Wouhou";
			if ($fixtureinbackup['result']['goalsHomeTeam'] != $gameretrieve['result']['goalsHomeTeam'] or $fixtureinbackup['result']['goalsAwayTeam'] != $gameretrieve['result']['goalsAwayTeam']) {
					
				echo "A score have been updated!! :D<br>";
				//Place the result into the Slack array
				$concatgamelist = $concatgamelist.($teamsacronyms[$gameretrieve['homeTeamName']]."  ".$gameretrieve['result']['goalsHomeTeam']." - ".$gameretrieve['result']['goalsAwayTeam']."  ".$teamsacronyms[$gameretrieve['awayTeamName']]."\n");
				//Set the backup trigger to 'true'
				$backuptrigger = "true";
				//Compt number of updates
				$numberofupdates=$numberofupdates+1;

			}else{
				echo "This score is known buddy... Come back later ;)<br>";
				//Do nothing...
			}
		}
	}
}

echo $concatgamelist;

#######################################################################
###################### Posting massage to Slack #######################

if ($backuptrigger == "true") {
	
	echo "<br><br>Woop woop ! A Slack message has to be sent man ! => ";
	 
	//Initiate cURL.
	$ch = curl_init($slackhookurl);

	$jsonData = [
	    'attachments' => [[
	    	'fallback' => 'Check '.$flag.' Day '.$gameretrieve['matchday'].' latest result(s)',
	    	'pretext' => "@channel: ".$numberofupdates." result(s) available for ".$flag,
	    	'title' => 'Primera Division • Day '.$gameretrieve['matchday'],
	    	'text' => $concatgamelist,
	    	"mrkdwn_in" => ["text", "pretext"],
			//'color' => '#F35A00'	
		]]//end attachments
	];
	 
	//Encode the array into JSON.
	$jsonDataEncoded = json_encode($jsonData);
	 
	//Tell cURL that we want to send a POST request.
	curl_setopt($ch, CURLOPT_POST, 1);
	 
	//Attach our encoded JSON string to the POST fields.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	 
	//Set the content type to application/json
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
	 
	//Execute the request
	$result = curl_exec($ch);

	// Step by step explanation of the code above:
	// We setup the URL that we want to send our JSON to.
	// We initiated cURL using curl_init.
	// We setup a PHP array containing sample data.
	// We encoded our PHP array into a JSON string by using the function json_encode.
	// We specified that we were sending a POST request by setting the CURLOPT_POST option to 1.
	// We attached our JSON data using the CURLOPT_POSTFIELDS option.
	// We set the content-type of our request to application/json. It is extremely important to note that you should always use “application/json”, not “text/json”. Simply put, using “text/json” is incorrect!
	// Finally, we used the function curl_exec to execute our POST request. If you want to check for errors at this stage, then you should check out my article on error handling with cURL.
}

#######################################################################
######################## Back up file update ##########################

$fp = fopen('./'.$prefixbackupfile.'matchesbackup.json', 'w');
fwrite($fp, json_encode($arrayapireturn));
fclose($fp);
?>