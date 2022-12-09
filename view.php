<link href="assets/css/bootstrap.css" rel="stylesheet" />
<link href="assets/css/style.css" rel="stylesheet"/>
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.12.4.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<?php
	include("assets/php/functions.php");
	$dblink=db_connect("doc_storage");
	$autoid=$_REQUEST['fid'];
	echo '<div id="page-inner">';
	echo '<h1 class="page-head-line">View Files on DB</h1>';
	echo '<div class="panel-body">';

	/* display only the chosen file from search */
	$sql="SELECT * from `documentsCRON` where `auto_id` = '$autoid'";
	
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql<br>".$result->error);
		$data = $result->fetch_array(MYSQLI_ASSOC);

		if ($data['path'] != NULL) {
			echo '<p>File: <a href="uploads/'.$data['fileName'].'" target="_blank">'.$data['fileName'].'</a></p>';
			echo '<p>something<p>';
		}
		else {
			$content=$data['content'];
			$ftype=$data['fileType'];
			
			/* creating file with unique name using uniqid() */
			// $newFileWithPath = tempnam('/var/www/html/uploads', date("Y-m-d_H:i:s"));
			$newFile = date("Y-m-d_H:i:s") . "_" . uniqid();

			/* adding extension to file name */
			$newFile = $newFile  . "." . $ftype;
			// rename($newFileWithPath, $newFileWithType);

			/* truncate string to only include file name and extension
			using strripos() to locate the last occurrence of */
			// $newFileName = substr($newFileWithType, strripos($newFileWithType, "/") + 1);

			if (!($fp=fopen("/var/www/html/uploads/$newFile","w")))
				echo "<p>File could not be loaded at this time</p>";
			else
			{
				fwrite($fp,$content);
				fclose($fp);
				echo '<p>File: <a href="uploads/'.$newFile.'" target="_blank">'.$data['fileName'].'</a></p>';
			}

			/* delete file */
			// unlink("/var/www/html/uploads/$newFile");
		}

	echo '</div>';//end panel-body
	echo '</div>';//end page-inner
?>