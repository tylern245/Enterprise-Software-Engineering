<?php

include("../assets/php/functions.php");
$dblink = db_connect("doc_storage");

// query for only the loan numbers
$sql = "SELECT `loanNum` FROM `documentsCRON`";
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

var_dump($loanArrayUnique);

$rowNumber = 1;     // row number count for table display purposes

// display number of documents for each loan number
foreach ($loanArrayUnique as $num=>$loan) {
    // create query to count the # of documents for each loan number
    $sql2 = "SELECT COUNT(`fileName`) FROM `documentsCRON` WHERE `fileName` LIKE '%$loan%'";

    // store query result in a variable
    $result2 = $dblink->query($sql2) or
        die ("Something went wrong with: $sql<br>" . $dblink->error);

    // fetch array of numbered index (should only contain one row, which is the file COUNT)
    $numOfFiles = $result2->fetch_array(MYSQLI_NUM);
    
    echo $rowNumber++ . " => Loan #$loan has $numOfFiles[0] documents\n";
}
?>