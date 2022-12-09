<?php

	// $hostname="localhost";
	// $username="webserver";
	// $password="cAuLCVYR]9kxEz6u";
	// $db="doc_management_schema";
	// $mysqli=new mysqli($hostname,$username,$password,$db);

	// if (mysqli_connect_errno()){
	// 	die("Error connecting to database: ".mysqli_connect_error());
	// }
	
//	$sql="SELECT * FROM `USERS` WHERE 1";
//	$result=$mysqli->query($sql) or
//		die("Something went wrong with $sql".$mysqli->error);
//	while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
//		echo "<p> Entry $data[userID]: $data[firstName] $data[lastName] </p>";
//	}

//	$sql="INSERT INTO `USERS` (`userID`, `firstName`, `lastName`, `permissions`, `age`, `log`, `dob`, `address`, `phone`) VALUES (NULL, 'TT', 'Nguyen', '', '21', '', '', '', '1113334444')";
//		$mysqli->query($sql) or
//			die("Something went wrong with $sql ".$mysqli->error);
//		echo "<p>Executed $sql</p>";
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Tyler Nguyen's Website</title>
</head>
<body>
		<p>Tyler Nguyen's Website for Enterprise Software Engineering - FALL 2022</p>
		<br>
		<a href="https://ec2-3-139-55-178.us-east-2.compute.amazonaws.com/dbadmin">Database</a>
		<br>
		<a href="https://ec2-3-139-55-178.us-east-2.compute.amazonaws.com/upload-search.php">Assignment 3 - File Upload and Search</a>
		<br>
		<a href="https://ec2-3-139-55-178.us-east-2.compute.amazonaws.com/report.php">Report</a>
		<br>
</body>
</html>