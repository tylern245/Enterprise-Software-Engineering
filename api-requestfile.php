<?php
    $documents_conn = db_connect("doc_storage");

    foreach($filesArray as $path){
        $pathExploded = explode("/", $path);
        $fileName = $pathExploded[count($pathExploded) - 1];

        $fileNameExploded = explode("-", $fileName);
        $loanNum = $fileNameExploded[0];
        $docType = $fileNameExploded[1];

        $fileSize = 0;      // assume file size = 0 if content header is not found
        $fileContent;

        $data = "sid=$sid&uid=$username&fid=$fileName";

        $connectLink=curl_init('https://cs4743.professorvaladez.com/api/request_file');
                    curl_setopt($connectLink, CURLOPT_POST, 1);
                    curl_setopt($connectLink, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($connectLink, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($connectLink, CURLOPT_HEADER, true);
                    curl_setopt($connectLink, CURLOPT_HTTPHEADER, array(
                        'content-type: application/x-www-form-urlencoded',
                        'content-length: ' . strlen($data))
                    );

                    if (curl_errno($connectLink)){
                        echo curl_error($connectLink);
                        echo "\r\nFailed to request file: $fileName";
                    }
                    else {
                        $fileContent = curl_exec($connectLink);  
                        // set file size if a valid content header is being supplied, and Content-Length exists
                        if (preg_match('/Content-Length: (\d+)/', $fileContent, $matches)){
                            $fileSize = (int)$matches[1];            
                        }

                        echo "Successfully requested file: $fileName\tSize: $fileSize B\r\n";
                    }
                                                
                    $contentsClean = addslashes($fileContent);
                    $timestamp = date("Y-m-d H:i:s");
                    curl_close($connectLink);

        $uploadBy = get_current_user();
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        
        // write file to database
        // $sql="INSERT INTO `documents` (`fileName`,`path`,`uploadBy`,`uploadDate`,`status`, `fileSize` ,`fileType`, `content`) values ('$fileName', '', '$uploadBy', '$timestamp', 'active', '$fileSize', '$fileType', '$contentsClean')";

        $sql = "INSERT INTO `documentsCRON` (`loanNum`, `docType`, `fileName`, `uploadBy`, `uploadDate`, `status`, `fileType`, `content`, `fileSize`) VALUES ('$loanNum','$docType','$fileName','$uploadBy','$timestamp','active','$fileType','$contentsClean','$fileSize')";

        $result = $documents_conn->query($sql) or
        die("Something went wrong with $sql<br>".$result->error);
    }
?>