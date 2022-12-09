<?php
include('assets/php/functions.php');
include('assets/php/credentials.php');
// connect to sessions database
$sessions_conn = db_connect("sessions");

$data="username=" . urlencode($username) . "&password=" . urlencode($password);

$connectLink=curl_init('https://cs4743.professorvaladez.com/api/create_session');
			curl_setopt($connectLink, CURLOPT_POST, 1);
			curl_setopt($connectLink, CURLOPT_POSTFIELDS, $data);
			curl_setopt($connectLink, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($connectLink, CURLOPT_HTTPHEADER, array(
				'content-type: application/x-www-form-urlencoded',
				'content-length: ' . strlen($data))
			);

			$results = curl_exec($connectLink);
			$timestamp = date("Y-m-d H:i:s");
			curl_close($connectLink);

// capture JSON data into an array
$content = json_decode($results, true);

// if session is successfully created
if ($content[0] == "Status: OK" && $content[1] == "MSG: Session Created") {

	// store data into variables
	$str = explode(":", $content[0]);		// status
	$status = trim($str[1]);

	$str = explode(":", $content[1]);		// msg
	$msg = trim($str[1]);

	$sid = trim($content[2]);				// sid
	
	// print out success message
	echo "\r\nSession successfully created!\r\n";
	echo "SID: $sid\r\n";
	
	// log to database, print result of INSERT
	$sql = "INSERT INTO `session_history` (`sid`, `status`, `msg`, `action`, `timestamp`) values ('$sid', '$status', '$msg', '', '$timestamp')";
	if ($sessions_conn->query($sql)) {
		echo "\r\nLogged.\r\n";
	}
	else{
		echo "\r\nUnable to log. Error: " . $sessions_conn->error . "\r\n"; 
	}


    // query and request files
    include('api-queryfiles.php');

	// close session
	include('api-close.php');

}
else {
	// print out failure message
	echo "\r\nUnable to create session. :(\r\n";

	// print out error to be viewed
	foreach ($content as $element){
		echo $element . "\r\n";
	}

	// store data into variables
	$str = explode(":", $content[0]);		// status
	$status = trim($str[1]);

	$str = explode(":", $content[1]);		// msg
	$msg = trim($str[1]);

	$str = explode(":", $content[2]);
	$action = trim($str[1]);				// action

	// log to database, print result of INSERT
	$sql = "INSERT INTO `session_history` (`sid`, `status`, `msg`, `action`, `timestamp`) values ('', '$status', '$msg', '$action', '$timestamp')";
	if ($sessions_conn->query($sql)) {
		echo "\r\nLogged.\r\n";
	}
	else{
		echo "\r\nUnable to log. Error: " . $sessions_conn->error . "\r\n"; 
	}
}

?>