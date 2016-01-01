<?
include '../includes.php';

#########Cron check########
// $time = date(j.'\-'.m.'\-'.Y.'\-'.G.'\-'.i);
// fopen($time.".txt", "w+");

#######################################################################
###################### Variables declarations #########################

$currentday=date('Y-m-d');
echo "Today is : ".$currentday."<br>";

$currentdayprep = str_replace('-', '/', $currentday);
$yesterday = date('Y-m-d',strtotime($currentdayprep . "-1 days"));
echo "Yesterday was : ".$yesterday."<br>";

$currenttime=date('H:i');
echo "Current time is : ".$currenttime."<br><br>";

$index=0;
$gamelist = array('games'=> array(
	'gamefakeid' => array(),
	'journey' => array(),
	'hometeam' => array(),
	'scorehome' =>  array(),
	'awayteam' => array(),
	'scoreaway' => array()));

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

#######################################################################
###################### Fetching data from API #########################

//$liguefixtures="http://api.football-data.org/v1/soccerseasons/396/fixtures/";//Ligue1
//$liguefixtures="http://api.football-data.org/v1/soccerseasons/405/fixtures/";//ChampionsLeague
$liguefixtures="http://api.football-data.org/v1/soccerseasons/398/fixtures/";//PremierLeague
//$liguefixtures="http://api.football-data.org/v1/soccerseasons/399/fixtures/";//PrimeraDivision

$fix = json_decode(fetchdataonapi($liguefixtures));

foreach ($fix->fixtures as $game){

	//Condition #1 => Date is <= Today's date	
	if (retrievedate($game->date) == $currentday OR retrievedate($game->date) == $yesterday) {

		//Condition #2 => Status is finished
		if ($game->status == "FINISHED") {

			$index=$index+1;
			$gamelist[gamefakeid][$index]=$index;
			$gamelist[journey][$index]=$game->matchday;
			$gamelist[hometeam][$index]=$teamsacronyms[$game->homeTeamName];
			$gamelist[scorehome][$index]=$game->result->goalsHomeTeam;
			$gamelist[awayteam][$index]=$teamsacronyms[$game->awayTeamName];
			$gamelist[scoreaway][$index]=$game->result->goalsAwayTeam;
		}
	}
}// End for each • Response line of the API

//print_r($gamelist);
#######################################################################
###################### Fetching data from back up #####################

$gamesinbackup = json_decode('./matchesbackup.json');

echo $gamesinbackup[gamefakeid];


#######################################################################

//Save the new results backup
$fp = fopen('./matchesbackup.json', 'w');
fwrite($fp, json_encode($gamelist));
fclose($fp);


for ($i=1; $i <= $index ; $i++) { 
	$concatgamelist = $concatgamelist.($gamelist[hometeam][$i]."  ".$gamelist[scorehome][$i]." - ".$gamelist[scoreaway][$i]."  ".$gamelist[awayteam][$i]."\n");
}

echo $concatgamelist;

if ($concatgamelist != NULL) {
	###################### Sending data to Slack channel #####################

	//Hook Url
	//$url = 'https://hooks.slack.com/services/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'; //ligue1
	//$url='https://hooks.slack.com/services/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'; //championsleague
	$url = 'https://hooks.slack.com/services/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'; //premierleague
	//$url = 'https://hooks.slack.com/services/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'; //primeradiv
	 
	//Initiate cURL.
	$ch = curl_init($url);

	$jsonData = [
	    'attachments' => [[
	    	'fallback' => 'Check :flag-gb: Day '.$gamelist[journey][1].' latest result(s)',
	    	'pretext' => "@channel: ".$index." result(s) available for :flag-gb:",
	    	'title' => 'Premier League • Day '.$gamelist[journey][1],
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
}else{
	echo "No results available now.";
}
?>