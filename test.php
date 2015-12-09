<?
header("Refresh:300");//check history every 5 minutes
include './includes.php';

###################### Fetching data from API #####################

//$ligue1fixtures="http://api.football-data.org/v1/soccerseasons/396/fixtures/";
$ligue1fixtures="http://api.football-data.org/v1/soccerseasons/405/fixtures/";//$c1fixtures
//$premleaguefixtures="";
//$primdivfixtures="";

$l1fix = json_decode(fetchdataonapi($ligue1fixtures));

$currentday=date('Y-m-d');
$currenttime=date('H:i');

//echo retrievehour("2015-11-04T19:45:00Z")."<br>";
echo $currenttime."<br>";

echo "<b><u>Today's games</u></b> :<br>";

foreach ($l1fix->fixtures as $l1game){

	//Condition #2 => Game's hour + 1h55 (10' to be safe) is <= server hour

	//Condition #1 => Date is <= Today's date	
	if ($currentday == retrievedate($l1game->date)) {
		//Condition #3 => Status is finished
		if ($l1game->status == "TIMED") {
			echo $l1game->homeTeamName." ".$l1game->result->goalsHomeTeam." vs. ".$l1game->result->goalsAwayTeam." ".$l1game->awayTeamName."<br>";
		}
	}

}// End for each • Response line of the API

###################### Sending data to Slack channel #####################

// //API Url
// $url='https://hooks.slack.com/services/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'; //championsleague
// //$url = 'https://hooks.slack.com/services/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'; //ligue1
// //$url = 'https://hooks.slack.com/services/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'; //premierleague
// //$url = 'https://hooks.slack.com/services/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'; //primeradiv
 
// //Initiate cURL.
// $ch = curl_init($url);

// //The JSON data.
// $jsonData = array(
//     //'username' => 'Ligue 1', //set in slack admin
//     // 'channel' => "@matt",
//     'text' => "@channel: Test Champions League bot.",
//     //"icon_url" => "https://slack.com/img/icons/app-57.png"//,//set in slack admin
//     //"icon_emoji" => ":smirk:"

// );
 
// //Encode the array into JSON.
// $jsonDataEncoded = json_encode($jsonData);
 
// //Tell cURL that we want to send a POST request.
// curl_setopt($ch, CURLOPT_POST, 1);
 
// //Attach our encoded JSON string to the POST fields.
// curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
 
// //Set the content type to application/json
// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
 
// //Execute the request
// $result = curl_exec($ch);

// // Step by step explanation of the above code:

// // We setup the URL that we want to send our JSON to.
// // We initiated cURL using curl_init.
// // We setup a PHP array containing sample data.
// // We encoded our PHP array into a JSON string by using the function json_encode.
// // We specified that we were sending a POST request by setting the CURLOPT_POST option to 1.
// // We attached our JSON data using the CURLOPT_POSTFIELDS option.
// // We set the content-type of our request to application/json. It is extremely important to note that you should always use “application/json”, not “text/json”. Simply put, using “text/json” is incorrect!
// // Finally, we used the function curl_exec to execute our POST request. If you want to check for errors at this stage, then you should check out my article on error handling with cURL.

?>