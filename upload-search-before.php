<!DOCTYPE html>
<link href="assets/css/bootstrap.css" rel="stylesheet" />
<link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
<link href="assets/css/style.css" rel="stylesheet" />
<script src="assets/js/custom.js"></script>
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.12.4.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/bootstrap-fileupload.js"></script>

<?php
    include('assets/php/functions.php');
    $dblink=db_connect("doc_storage");

	date_default_timezone_set("America/Chicago");

    if (isset($_POST['upload-file']))
    {
        $uploadDate=date("Y-m-d H:i:s");
        $uploadBy="user@test.mail";
        $fileName=$_FILES['userfile']['name'];
        $tmpName=$_FILES['userfile']['tmp_name'];
        $fileSize=$_FILES['userfile']['size'];
        $fileType=$_FILES['userfile']['type'];
        $docType = pathinfo($fileName, PATHINFO_EXTENSION);
        $fp=fopen($tmpName, 'r');
        $content=fread($fp, filesize($tmpName));
        fclose($fp);
        $contentsClean=addslashes($content);
        
        $sql="INSERT into `documents` (`fileName`,`path`,`uploadBy`,`uploadDate`,`status`, `fileSize` ,`fileType`, `content`) VALUES ('$fileName', '', '$uploadBy', '$uploadDate', 'active', '$fileSize', '$docType', '$contentsClean')";
        // $sql = "INSERT INTO `documents` (`fileName`, `path` `uploadBy`, `uploadDate`, `status`, `fileType`, `content`, `fileSize`) VALUES ('$fileName', '','$uploadBy','$uploadDate','active','$docType','$contentsClean','$fileSize')";
        $result = $dblink->query($sql) or
            die("Something went wrong with $sql<br>".$result->error);

        // redirect("upload-search.php?msg=success");
        redirect("https://ec2-3-139-55-178.us-east-2.compute.amazonaws.com/upload-search.php?msg=success");
    }
?>

<body>
<div class="">
<div class="row">
    <!-- Upload File -->
    <div class="col-md-6">
    <?php
    if (isset($_REQUEST['msg']) && ($_REQUEST['msg']=="success")) {
    ?>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">Close</button>
            Document successfully uploaded!
        </div>
    <?php
    }
    ?>
     <span class="border border-dark"></span>
        <div id="page-inner">
            <h1 class="page-head-line">Upload a New File to DocStorage</h1>
            <div class="panel-body">
                <form method="post" enctype="multipart/form-data" action="">
                    <input type="hidden" name="uploadedby" value="user@test.mail">
                    <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                    <div class="form-group">
                        <label class="control-label col-lg-4">File Upload</label>
                        <div class="fileupload-div">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <span class="btn btn-file btn-primary">
                                            <span class="fileupload-new">Select File</span>
                                            <span class="fileupload-exists">Change</span><input name="userfile" id="userfile" type="file">
                                        </span>
                                    </div>
                                    <div class="col-md-2"><a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" name="upload-file" value="upload-file" id="uploadButton" class="btn btn-lg btn-block btn-success" onclick="return isFileUploaded('userfile')">Upload File</button>
                </form>
            </div> <!-- end of panel-body -->
        </div>
    </div> <!-- end of first column-->
    
    <!-- Search for File -->
    <div class="col-md-6">
        <span class="border border-dark"></span>
        <div id="page-inner">
            <h1 class="page-head-line">Search Files on DB</h1>
            <div class="panel-body">
                <?php
                if (!isset($_POST['search-file'])) {
                ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label>Search String:</label>
                        <input type="text" class="form-control" name="searchString">
                    </div>
                    <select name="searchType">
                        <option value="name">Name</option>
                        <option value="uploadBy">Uploaded By</option>
                        <option value="uploadDate">Date</option>
                        <option value="all">All</option>
                    </select>
                    <hr>
                    <button type="submit" name="search-file" value="upload-file" id="searchButton" class="btn btn-lg btn-block btn-secondary">Search</button>
                </form>
                <?php
                }
                ?>

                <?php
                if (isset($_POST['search-file'])) {            
                    $searchType=$_POST['searchType'];
                    $searchString=addslashes($_POST['searchString']);
                    switch($searchType) {
                        case "name":
                            $sql="SELECT `auto_id`, `fileName`, `uploadDate`, `uploadBy`, `fileSize` FROM `documents` WHERE `fileName` like '%$searchString%'";
                            break;
                        case "uploadBy":
                            $sql="SELECT `auto_id`, `fileName`, `uploadDate`, `uploadBy`, `fileSize` FROM `documents` WHERE `uploadBy` like '%$searchString%'";
                            break;
                        case "uploadDate":
                            $sql="SELECT `auto_id`, `fileName`, `uploadDate`, `uploadBy`, `fileSize` FROM `documents` WHERE `uploadDate` like '%$searchString%'";
                            break;
                        case "all":
                            $sql="SELECT `auto_id`, `fileName`, `uploadDate`, `uploadBy`, `fileSize` FROM `documents`";
                            break;
                        default:
                            redirect("search.php?msg=searchTypeError");
                            break;
                    }
                    
                    $result=$dblink->query($sql) or
                        die("Something went wrong with $sql<br>".$dblink->error);
                ?>    
                    <table>
                    <p> <?php echo mysqli_num_rows($result) ?> results found. </p>
                    <!-- <button onclick="window.location.href='/upload-search.php'">Go Back</button> -->
                    <button onclick="history.back()">Go Back</button>
                    <tr>
                        <th>Record #</th>
                        <th>File Name</th>
                        <th>Upload Date</th>
                        <th>File Size (in B)</th>
                    </tr>
                <?php
                    while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                ?>
                        <tr>
                            <td><?php echo $data['auto_id'] ?></td>
                            <td><?php echo $data['fileName'] ?></td>
                            <td><?php echo $data['uploadDate'] ?></td>
                            <td><?php echo $data['fileSize'] ?></td>              
                            <td><a href="view.php?fid=<?php echo htmlentities($data['auto_id'])?>" target="_blank"> View </a></td>
                        </tr>
                <?php
                    } // end of while loop
                }   // end of if statement
                ?>
                
                    </table>
            </div> <!-- end of panel-body -->
        </div> <!-- end of page inner -->
    </div> <!-- end of second column-->
</div> <!-- end of row-->
</div> <!-- end of container -->
</body> <!-- end of body -->