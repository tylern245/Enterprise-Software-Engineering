<?php
$data = "sid=$sid&uid=$username";

$connectLink=curl_init('https://cs4743.professorvaladez.com/api/query_files');
            curl_setopt($connectLink, CURLOPT_POST, 1);
            curl_setopt($connectLink, CURLOPT_POSTFIELDS, $data);
            curl_setopt($connectLink, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($connectLink, CURLOPT_HTTPHEADER, array(
                'content-type: application/x-www-form-urlencoded',
                'content-length: ' . strlen($data))
            );
            // log amount of time taken to query files
            $timeStart = microtime(true);
            $results = curl_exec($connectLink);
            $timeEnd = microtime(true);
            $executionTime = $timeEnd - $timeStart;

			$timestamp = date("Y-m-d H:i:s");
			curl_close($connectLink);

            // capture JSON data into an array
            $content = json_decode($results, true);

            // if query is successful
            if ($content[0] == "Status: OK" && $content[2] == "Action: Continue") {
                // store data into variables
                $str = explode(":", $content[0]);		// status
                $status = trim($str[1]);
            
                $str = explode(":", $content[1]);		// msg
                $msg = trim($str[1]);

                // store files into an array
                $filesArray = explode(",", $msg);

                $str = explode(":", $content[2]);		// action
                $action = trim($str[1]);

                // print out success message
                if (count($filesArray) == 1) {
                    echo "\r\n" . count($filesArray) . " file has be queried!\r\n";
                    echo "Action: $action\r\n";
                    echo "Time taken: $executionTime seconds";
                }
                else {
                    echo "\r\n" . count($filesArray) . " files have be queried!\r\n";
                    echo "Action: $action\r\n";
                    echo "Time taken: $executionTime seconds";
                }


                // log to database
                $sql = "INSERT INTO `session_history` (`sid`, `status`, `msg`, `action`, `timestamp`) values ('$sid', '$status', '$msg', '$action', '$timestamp')";
                if ($sessions_conn->query($sql)) {
                    echo "\r\nLogged.\r\n";
                }
                else{
                    echo "\r\nUnable to log. Error: " . $sessions_conn->error . "\r\n"; 
                }
                
                //request file
                include('api-requestfile.php');
            }
            else {
                echo "\r\n No new files to import.\r\n";
                echo "SID: $sid\r\n";
                echo "Time taken: $executionTime seconds";
            }
?>