<?
//INCLUDES FILE (edited on web AGAIN !)
function fetchdataonapi($url){
		# init curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_fields);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		// curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true); // make sure we see the sended header afterwards
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		//curl_setopt($ch, CURLOPT_POST, 1);

		# dont care about ssl
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		# download and close
		$output = curl_exec($ch);
		// $request =  curl_getinfo($ch, CURLINFO_HEADER_OUT);
		// $error = curl_error($ch);
		curl_close($ch);
		return $output;
}

// Api.football-data.org format -> 2015-11-04T19:45:00Z
// Becareful London Time !!

function retrievedate($inputdate){

	$firsthyphen = strpos($inputdate, "-")+1;
	$secondhyphen = strpos($inputdate, "-", $firsthyphen)+1;
	
	$yyyy=substr($inputdate, 0, 4);
	$mm=substr($inputdate, $firsthyphen,2);
	$dd=substr($inputdate, $secondhyphen,2);
	
	return $yyyy."-".$mm."-".$dd;
}

function retrievehour($inputdate){

	$firstdots = strpos($inputdate, ":");
	
	$hh=substr($inputdate, $firstdots-2,2)+1;
	$mn=substr($inputdate, $firstdots+1,2);
	
	return $hh.":".$mn;
}
?>
