<?php
include('assets/php/functions.php');
include('assets/php/credentials.php');
// connect to sessions database
$sessions_conn = db_connect("sessions");
$sid = "8338e33a18e1ba39c2506a2ff8322f9fd8bfb54b";

$data = "sid=$sid&uid=$username";
$connectLink=curl_init('https://cs4743.professorvaladez.com/api/close_session');
            curl_setopt($connectLink, CURLOPT_POST, 1);
            curl_setopt($connectLink, CURLOPT_POSTFIELDS, $data);
            curl_setopt($connectLink, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($connectLink, CURLOPT_HTTPHEADER, array(
                'content-type: application/x-www-form-urlencoded', 
                'content-length: ' . strlen($data)
            ));
            $results = curl_exec($connectLink);
            $timestamp = date("Y-m-d H:i:s");
            $content = json_decode($results, true);

            	// store data into variables
                $str = explode(":", $content[0]);		// status
                $status = trim($str[1]);

                $str = explode(":", $content[1]);		// msg
                $msg = trim($str[1]);

                $str = explode(":", $content[2]);       // action
                $action = trim($str[1]);

            if ($content[0] == "Status: OK") {
                echo "\r\n------------------------------";
                echo "\r\nSession successfully closed!\r\n";
                echo "SID: $sid\r\n";
            }
            else {
                // print the error to be viewed
                foreach ($content as $element){
                    echo $element . "\r\n";
                }
            }
            curl_close($connectLink);

            // log to database, print result of INSERT
            $sql = "INSERT INTO `session_history` (`sid`, `status`, `msg`, `action`, `timestamp`) values ('$sid', '$status', '$msg', '$action', '$timestamp')";
            if ($sessions_conn->query($sql)) {
                echo "\r\nLogged.\r\n";
            }
            else{
                echo "\r\nUnable to log. Error: " . $sessions_conn->error . "\r\n"; 
            }
?>