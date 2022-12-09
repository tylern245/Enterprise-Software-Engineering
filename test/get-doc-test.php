<?php
include("../assets/php/functions.php");
$dblink = db_connect("doc_storage");

// query for only the loan numbers
$sql = "SELECT `loanNum`, `docType`, `fileName`, `fileSize` FROM `documentsCRON`";
$result = $dblink->query($sql) or
    die("Something went wrong with: $sql<br>" . $dblink->error);

// initialize array
$loanArray = array();

// fetch all loan numbers (rows) in an associative array and store in variable
$data=$result->fetch_all(MYSQLI_ASSOC);

// store every loan number (row) inside the loan array
foreach ($data as $loan) {
    $loanArray[] = $loan['loanNum'];
}

// create a new array that only contains unique loan numbers (aka no duplicates)
$loanArrayUnique = array_unique($loanArray);

// for each loan number, query for all documents types it has
foreach ($loanArrayUnique as $loanNum) {
    $currentLoanDocTypes_query = "SELECT `docType` FROM `documentsCRON` WHERE `loanNum` = $loanNum";
    $currentLoanDocTypes_result = $dblink->query($currentLoanDocTypes_query);
    $currentLoanDocTypes_rows = $currentLoanDocTypes_result->fetch_all(MYSQLI_ASSOC);
    
    // store the current loan's document types in an array
    $docTypeArray = array();
    foreach ($currentLoanDocTypes_rows as $loan) {
        $docTypeArray[] = $loan['docType'];
    }

    printArray($docTypeArray);

    echo "\n";
}


function printArray($array) {
    foreach($array as $element) {
        echo $element . ", ";
    }

    echo "\n";
}

?>