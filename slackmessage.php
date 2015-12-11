<?php
//Test from terminal
###################### Sending data to Slack channel #####################

//API Url
$url='https://hooks.slack.com/services/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'; //championsleague
//$url = 'https://hooks.slack.com/services/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'; //ligue1
//$url = 'https://hooks.slack.com/services/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'; //premierleague
//$url = 'https://hooks.slack.com/services/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'; //primeradiv
 
//Initiate cURL.
$ch = curl_init($url);

//Flag array
//France :flag-fr:
//Urkain :flag-ua:
//Spain :flag-es:
//Italy :flag-it:
//Sweden :flag-se:
//Russia :flag-ru:
//Deutschland :flag-de:
//Netherland :flag-nl:
//Portugal :flag-pt:
//Israel :flag-li:
//Poland :flag-id:
//Slovakia :flag-sk:
//England :flag-gb:
//Belgium :flag-be:
//Kroatia :flag-hr:
//Cyprus :flag-cy:
//Turkia :flag-tr:
//Kazakstan :flag-kz:
//Greece :flag-gr:


//The JSON data.
$jsonData = [
    'attachments' => [[
    	'fallback' => 'Check Day 6 fixtures',
    	'pretext' => "@channel: Upcoming games",
    	'title' => 'Champions League Day 6',
    	'text' => ":flag-it: Roma *vs.* Bate :flag-ua:\n:flag-de: Bayer *vs.* Barça :flag-es:\n:flag-gr: Olympiakos *vs.* Arsenal :flag-gb:\n:flag-hr: Dinamo Zagreb *vs.* Bayern :flag-de:\n:flag-gb: Chelsea *vs.* FC Porto :flag-pt:\n:flag-ua: Dynamo Kiev *vs.* Maccabi :flag-li:\n:flag-be: La Gantoise *vs.* Zénith :flag-ru:\n:flag-es: FC Valencia *vs.* Lyon :flag-fr:",
    	"mrkdwn_in" => ["text", "pretext"],
		// 'fields' => [
		// 	['value' => 'PSG vs. Shaktior','short' => 'false'],
		// 	['value' => 'Team 2 vs. Team 3','short' => 'false']
		// 	],//end fields
		//'color' => '#F35A00'	
	]]//end attachments
];
 
//Encode the array into JSON.
$jsonDataEncoded = json_encode($jsonData);

echo $jsonDataEncoded;
 
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
 
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
 
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
 
//Execute the request
$result = curl_exec($ch);

// Step by step explanation of the above code:

// We setup the URL that we want to send our JSON to.
// We initiated cURL using curl_init.
// We setup a PHP array containing sample data.
// We encoded our PHP array into a JSON string by using the function json_encode.
// We specified that we were sending a POST request by setting the CURLOPT_POST option to 1.
// We attached our JSON data using the CURLOPT_POSTFIELDS option.
// We set the content-type of our request to application/json. It is extremely important to note that you should always use “application/json”, not “text/json”. Simply put, using “text/json” is incorrect!
// Finally, we used the function curl_exec to execute our POST request. If you want to check for errors at this stage, then you should check out my article on error handling with cURL.
?>