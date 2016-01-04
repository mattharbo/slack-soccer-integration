Spanish league<br><br>
<?php
include '../includes.php';

###################### Variables declarations #########################

$maininfo="http://api.football-data.org/v1/soccerseasons/399";//PrimeraDivisionMainInfo

$slackhookurl = 'https://hooks.slack.com/services/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'; //primeradiv

$leaguename="Primera Division";

$flag=":flag-es:";

$teamsacronyms = array('RC Deportivo La Coruna'=>'Depor',
'Real Sociedad de Fútbol'=>'Real Sociedad',
'RCD Espanyol'=>'Espanyol',
'Getafe CF'=>'Getafe',
'Club Atlético de Madrid'=>'Atletico',
'UD Las Palmas'=>'Las Palmas',
'Rayo Vallecano de Madrid'=>'Rayo',
'Valencia CF'=>'Valencia',
'Málaga CF'=>'Malaga',
'Sevilla FC'=>'Sevilla',
'Athletic Club'=>'Bilbao',
'FC Barcelona'=>'Barcelona',
'Sporting Gijón'=>'Gijon',
'Real Madrid CF'=>'Real',
'Levante UD'=>'Levante',
'RC Celta de Vigo'=>'Celta',
'Real Betis'=>'Real Betis',
'Villarreal CF'=>'Villarreal',
'Granada CF'=>'Grenada',
'SD Eibar'=>'Eibar');

$prefixbackupfile="es_";
$backuptrigger = "false";
$numberofupdates=0;

#######################################################################
################ Fetching current match day from API ##################

$objectapireturnformaininfo = fetchdatafromapi2($maininfo);
$arrayapireturnformaininfo = objectToArray($objectapireturnformaininfo);
$fp = fopen('./'.$prefixbackupfile.'leaguemaininfo.json', 'w');
fwrite($fp, json_encode($arrayapireturnformaininfo));
fclose($fp);

$matchday=$arrayapireturnformaininfo['currentMatchday'];

echo "<b>Last API update : </b>".$arrayapireturnformaininfo['lastUpdated']."<br>";

#######################################################################
############## Fetching matches for given day from API ################

$url="http://api.football-data.org/v1/soccerseasons/399/fixtures?matchday=".$matchday;//PrimeraDivision

echo $url."<br><br>";

$objectapireturnforcurrentmatchday = fetchdatafromapi2($url);
$arrayapireturn = objectToArray($objectapireturnforcurrentmatchday);

//print_r($arrayapireturn)."<br><br>";

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

			echo $fixtureinbackup['homeTeamName']." vs ".$fixtureinbackup['awayTeamName']." => ";
				
			//echo "<br>There is a home team name that matches ! Wouhou";
			if ($fixtureinbackup['result']['goalsHomeTeam'] != $gameretrieve['result']['goalsHomeTeam'] or $fixtureinbackup['result']['goalsAwayTeam'] != $gameretrieve['result']['goalsAwayTeam']) {
					
				echo "<b><font color='red'>A score have been updated!! :D</font></b><br>";
				//Place the result into the Slack array
				$concatgamelist = $concatgamelist.($teamsacronyms[$gameretrieve['homeTeamName']]."  ".$gameretrieve['result']['goalsHomeTeam']." - ".$gameretrieve['result']['goalsAwayTeam']."  ".$teamsacronyms[$gameretrieve['awayTeamName']]."\n");
				//Set the backup trigger to 'true'
				$backuptrigger = "true";
				//Compt number of updates
				$numberofupdates=$numberofupdates+1;

			}else{
				echo "Score unchanged<br>";
			}
		}
	}
}

if (!empty($concatgamelist)) {
	echo "<br>Changes recap : ".$concatgamelist;
}

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
	    	'title' => $leaguename.' • Day '.$gameretrieve['matchday'],
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