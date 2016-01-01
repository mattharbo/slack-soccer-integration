<?
include '../includes.php';

#########Cron check########
// $time = date(j.'\-'.m.'\-'.Y.'\-'.G.'\-'.i);
// fopen($time.".txt", "w+");

###################### Variables declarations #########################

$currentday=date('Y-m-d');
echo "Today is : ".$currentday."<br>";

$currentdayprep = str_replace('-', '/', $currentday);
$yesterday = date('Y-m-d',strtotime($currentdayprep . "-1 days"));
echo "Yesterday was : ".$yesterday."<br>";

$currenttime=date('H:i');
echo "Current time is : ".$currenttime."<br><br>";

$index=-1;
$gamelist = array('fixtures' =>
	array(array('date'=>'',
		'status'=>'',
		'matchday'=>'',
		'homeTeamName'=>'',
		'awayTeamName'=>'',
		'result'=>array('goalsHomeTeam'=>'',
			'goalsAwayTeam'=>'')
		),
	),
);

// $teamsacronyms = array('Arsenal FC'=>'Arsenal',
// 'Leicester City FC'=>'Leicester',
// 'Tottenham Hotspur FC'=>'Tottenham',
// 'Manchester City FC'=>'City',
// 'Crystal Palace FC'=>'Crystal',
// 'Manchester United FC'=>'Man. U',
// 'West Ham United FC'=>'West Ham',
// 'Watford FC'=>'Watford',
// 'Stoke City FC'=>'Stoke',
// 'Liverpool FC'=>'Liverpool',
// 'Everton FC'=>'Everton',
// 'Southampton FC'=>'Southampton',
// 'West Bromwich Albion FC'=>'Albion',
// 'Chelsea FC'=>'Chelsea',
// 'Norwich City FC'=>'Norwich',
// 'AFC Bournemouth'=>'Bournemouth',
// 'Swansea City FC'=>'Swansea',
// 'Newcastle United FC'=>'Newcastle',
// 'Sunderland AFC'=>'Sunderland',
// 'Aston Villa FC'=>'Aston Villa');

$teamsacronyms = array('RC Deportivo La Coruna'=>'Depor',
'Real Sociedad de Fútbol'=>'Real Sociedad',
'RCD Espanyol'=>'Espanyol',
'Getafe CF'=>'Getafe',
'Club Atlético de Madrid'=>'Atlético',
'UD Las Palmas'=>'Las Palmas',
'Rayo Vallecano de Madrid'=>'Rayo',
'Valencia CF'=>'Valencia',
'Málaga CF'=>'Málaga',
'Sevilla FC'=>'Sevilla',
'Athletic Club'=>'Bilbao',
'FC Barcelona'=>'Barcelona',
'Sporting Gijón'=>'Gijón',
'Real Madrid CF'=>'Real',
'Levante UD'=>'Levante',
'RC Celta de Vigo'=>'Celta',
'Real Betis'=>'Real Betis',
'Villarreal CF'=>'Villarreal',
'Granada CF'=>'Grenada',
'SD Eibar'=>'Eibar');

#######################################################################
###################### Fetching data from API #########################

//$liguefixtures="http://api.football-data.org/v1/soccerseasons/396/fixtures/";//Ligue1
//$liguefixtures="http://api.football-data.org/v1/soccerseasons/405/fixtures/";//ChampionsLeague
//$liguefixtures="http://api.football-data.org/v1/soccerseasons/398/fixtures/";//PremierLeague
$liguefixtures="http://api.football-data.org/v1/soccerseasons/399/fixtures/";//PrimeraDivision

$fix = json_decode(fetchdataonapi($liguefixtures));

foreach ($fix->fixtures as $game){

	//Condition #1 => Date is <= Today's date	
	if (retrievedate($game->date) == $currentday OR retrievedate($game->date) == $yesterday) {

		//Condition #2 => Status is finished
		if ($game->status == "FINISHED") {

			$index=$index+1;
			$gamelist['fixtures'][$index]['date']=$game->date;
			$gamelist['fixtures'][$index]['status']=$game->status;
			$gamelist['fixtures'][$index]['matchday']=$game->matchday;
			$gamelist['fixtures'][$index]['homeTeamName']=$teamsacronyms[$game->homeTeamName];
			$gamelist['fixtures'][$index]['awayTeamName']=$teamsacronyms[$game->awayTeamName];
			$gamelist['fixtures'][$index]['result']['goalsHomeTeam']=$game->result->goalsHomeTeam;
			$gamelist['fixtures'][$index]['result']['goalsAwayTeam']=$game->result->goalsAwayTeam;
		}
	}
}// End for each • Response line of the API

#######################################################################
###################### Fetching data from back up #####################

$pathtofile = "./matchesbackup.json";
$gamesinbackup = json_decode(file_get_contents($pathtofile), true);

#######################################################################
############## Comparing backup with realtime data (API) ##############



#######################################################################

//Save the new results backup
// $fp = fopen('./matchesbackup.json', 'w');
// fwrite($fp, json_encode($gamelist));
// fclose($fp);


for ($i=0; $i <= $index ; $i++) { 
	$concatgamelist = $concatgamelist.($gamelist['fixtures'][$i]['homeTeamName']."  ".$gamelist['fixtures'][$i]['result']['goalsHomeTeam']." - ".$gamelist['fixtures'][$i]['result']['goalsAwayTeam']."  ".$gamelist['fixtures'][$i]['awayTeamName']."\n");
}

echo "<br><br>";
echo $concatgamelist;

// if ($concatgamelist != NULL) {
// 	###################### Sending data to Slack channel #####################

// 	//Hook Url
// 	//$url = 'https://hooks.slack.com/services/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'; //ligue1
// 	//$url='https://hooks.slack.com/services/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'; //championsleague
// 	$url = 'https://hooks.slack.com/services/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'; //premierleague
// 	//$url = 'https://hooks.slack.com/services/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'; //primeradiv
	 
// 	//Initiate cURL.
// 	$ch = curl_init($url);

// 	$jsonData = [
// 	    'attachments' => [[
// 	    	'fallback' => 'Check :flag-gb: Day '.$gamelist[journey][1].' latest result(s)',
// 	    	'pretext' => "@channel: ".$index." result(s) available for :flag-gb:",
// 	    	'title' => 'Premier League • Day '.$gamelist[journey][1],
// 	    	'text' => $concatgamelist,
// 	    	"mrkdwn_in" => ["text", "pretext"],
// 			//'color' => '#F35A00'	
// 		]]//end attachments
// 	];
	 
// 	//Encode the array into JSON.
// 	$jsonDataEncoded = json_encode($jsonData);
	 
// 	//Tell cURL that we want to send a POST request.
// 	curl_setopt($ch, CURLOPT_POST, 1);
	 
// 	//Attach our encoded JSON string to the POST fields.
// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	 
// 	//Set the content type to application/json
// 	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
	 
// 	//Execute the request
// 	$result = curl_exec($ch);

// 	// Step by step explanation of the code above:
// 	// We setup the URL that we want to send our JSON to.
// 	// We initiated cURL using curl_init.
// 	// We setup a PHP array containing sample data.
// 	// We encoded our PHP array into a JSON string by using the function json_encode.
// 	// We specified that we were sending a POST request by setting the CURLOPT_POST option to 1.
// 	// We attached our JSON data using the CURLOPT_POSTFIELDS option.
// 	// We set the content-type of our request to application/json. It is extremely important to note that you should always use “application/json”, not “text/json”. Simply put, using “text/json” is incorrect!
// 	// Finally, we used the function curl_exec to execute our POST request. If you want to check for errors at this stage, then you should check out my article on error handling with cURL.
// }else{
// 	echo "No results available now.";
// }
?>