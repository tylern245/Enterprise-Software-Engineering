<?php
include("assets/php/functions.php");
include("assets/php/report-functions.php");
// include("report-functions.php");
$dblink = db_connect("doc_storage");

/*** UPDATE 11/29/2022 : Remember to exclude files uploaded manually */

// query for only the loan numbers
$sql = "SELECT `loanNum`, `docType`, `fileName`, `fileSize` FROM `documentsCRON`";
$result = $dblink->query($sql) or
    die("Something went wrong with: $sql<br>" . $dblink->error);

// fetch all loan numbers (rows) in an associative array and store in variable
$data=$result->fetch_all(MYSQLI_ASSOC);

// array of loan numbers
$loanArray = getUniqueLoanNumArray($data);

// find number of loan numbers
$totalLoanNum = count($loanArray);

// find total size of all documents received from the API
$totalFileSize = getTotalSizeOfAllDocs($data);

// find average size of all documents received from the API
$averageFileSize = $totalFileSize / $totalLoanNum;

// initialize array of COMPLETED loans
$completedLoans = array();
?>

<html>
    <head>
        <title>Report</title>
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <link href="assets/css/report.css" rel="stylesheet" />
        <meta charset="UTF-8">
    </head>
    <body>
        <div class="myinfo">
            <p>Tyler Nguyen</p>
            <p>apd212</p>
            <p>Enterprise Software Engineering</p>
            <p>Homework 5 - Reporting and Analytics</p>
        </div>
        <hr class="myborder">
        
         <!-- TABLE OF CONTENTS  -->
        
        <div id="table-of-contents">
        <h1>Table of Contents</h1>
        <h5>(with working links! :D)</h5>
            <a href="#allLoansList">List of All Loans</a>
            <br>
            <a href="#totals-average">Totals and Average</a>
            <br>
            <a href="#allDocTypesTable">List of Document Types Across All Loans - Table Version</a>
            <br>
            <a href="#allDocTypesList">List of Document Types Across All Loans - List Version</a>
            <br>
            <a href="#completedLoansList">Completed Loans</a>
        </div>

        <hr class="myborder">

        <!-- LIST OF ALL LOANS  -->

        <div class="container" id="allLoansList">
            <h1>LIST OF ALL LOANS</h1><h5><a href="#top">(back to top)</a></h5>
            <div class="row">
                <div class="col-md-7">
                    <table>
                    <thead id="table-header">
                    <tr>
                        <th>#</th>
                        <th>Loan Number</th>
                        <th>Number of Documents Received</th>
                        <th>Total Size of Documents Received (in B)</th>
                        <th>Below or Average Size</th>
                    </tr>
                    </thead>
                    <?php
                        $rowNumber = 1;     // row number count for table display purposes
                        $totalLoanSize = 0; // initialize total size of ALL documents receieved

                        // display number of documents for each loan number
                        foreach ($loanArray  as $loanNum) :
                            // if the user encounters a blank loan number (aka a file uploaded manually to the database), skip to the next iteration of the loop
                            if (empty($loanNum)) {
                                continue;
                            }
                            
                            // find total number of documents (for current loan number)
                            $totalNumOfDocs_query = "SELECT COUNT(`fileSize`) FROM `documentsCRON` WHERE `fileName` LIKE '%$loanNum%'";
                            $totalNumOfDocs_result = $dblink->query($totalNumOfDocs_query);
                            $totalNumOfDocs = $totalNumOfDocs_result->fetch_array(MYSQLI_NUM);

                            // find total size of all documents (for current loan number)
                            $currentLoanSize_query = "SELECT SUM(`fileSize`) FROM `documentsCRON` WHERE `loanNum` = '$loanNum'";
                            $currentLoanSize_result = $dblink->query($currentLoanSize_query);
                            $currentLoanSize = $currentLoanSize_result->fetch_array(MYSQLI_NUM);
                    ?>
                            <tr>
                                <td><?=$rowNumber++?></td>
                                <td><?=$loanNum?></td>
                                <td><?=$totalNumOfDocs[0]?></td>
                                <td><?=number_format($currentLoanSize[0])?></td>
                                <td id="above-below"><?php echo ($currentLoanSize[0] > $averageFileSize) ? "&#8593; Above" : "&#8595; Below";?></td>
                            </tr>

                    <?php
                        // increment current loan size to total
                        $totalLoanSize += $currentLoanSize[0];
                        endforeach;
                    ?>
                    </table>
                </div>

            <!-- TOTALS AND AVERAGE  -->

                <div class="col-md-4" id="totals-average">
                <h1>TOTALS AND AVERAGE</h1><h5><a href="#top">(back to top)</a></h5>
                    <table class="totals-table">
                    <tr>
                        <th>Total Number of Unique Loan Numbers</th>
                        <td align="right"><?=$totalLoanNum?></td>
                    </tr>
                    <tr>
                        <th>Total Size of All Documents Received (in B)</th>
                        <td align="right"><?=number_format($totalLoanSize, 2)?></td>
                    </tr>
                    <tr>
                        <th>Average Size of All Documents Received (in B)</th>
                        <td align="right"><?=number_format($averageFileSize, 2)?></td>
                    </tr>
                    </table>
                </div>
            </div>
        </div> <!-- end of container -->

        <hr class="myborder">

        <!-- LIST OF DOCUMENT TYPES ACROSS ALL LOANS - TABLE VERSION  -->

        <div class="container" id="allDocTypesTable">
            <h1>LIST OF DOCUMENT TYPES ACROSS ALL LOANS - TABLE VERSION</h1><h5><a href="#top">(back to top)</a></h5>
            <table id="loanDocTypes">
                <thead id="table-header">
                <tr>
                    <th>#</th>
                    <th>Loan Number</th>
                    <th>Credit</th>         <!--- 1 -->
                    <th>Closing</th>
                    <th>Title</th>
                    <th>Financial</th>
                    <th>Personal</th>
                    <th>Internal</th>
                    <th>Legal</th>
                    <th>Other</th>          <!--- 8 -->
                    <th>Total</th>
                    <th>Is the loan complete?</th>
                </tr>
                </thead>
                <?php
                    // row number count for table display purposes
                    $rowNumber = 1;     

                    // for each loan number, query for all documents types it has
                    foreach ($loanArray as $loanNum):
                        
                        // if the user encounters a blank loan number (aka a file uploaded manually to the database),
                        // skip to the next iteration of the loop
                        if (empty($loanNum)) {
                            continue;
                        }
                        
                        // SELECT query with current loan number
                        $currentLoanDocTypes_query = "SELECT `docType` FROM `documentsCRON` WHERE `loanNum` = '$loanNum'";
                        $currentLoanDocTypes_result = $dblink->query($currentLoanDocTypes_query);
                        $currentLoanDocTypes_rows = $currentLoanDocTypes_result->fetch_all(MYSQLI_ASSOC);
                        
                        // store the current loan's document types in an array
                        $docTypeArray = array();
                        foreach ($currentLoanDocTypes_rows as $loan) {
                            $docTypeArray[] = $loan['docType'];
                        }

                        // find total number of documents for (current loan number)
                        $totalNumOfDocs_query = "SELECT COUNT(`fileSize`) FROM `documentsCRON` WHERE `fileName` LIKE '%$loanNum%'";
                        $totalNumOfDocs_result = $dblink->query($totalNumOfDocs_query);
                        $totalNumOfDocs = $totalNumOfDocs_result->fetch_array(MYSQLI_NUM);
                        
                ?>
                        <!-- if the loan number contains the doc type, print a checkmark and change cell color to green -->
                        <tr>
                            <td><?=$rowNumber++?></td>
                            <td><?=$loanNum?></td>
                            <td <?=(hasDocType("Credit", $docTypeArray))    ? htmlentities("style=background-color:#90EE90") : "" ?>><?=countDocType("Credit", $docTypeArray);?></td>
                            <td <?=(hasDocType("Closing", $docTypeArray))   ? htmlentities("style=background-color:#90EE90") : "" ?>><?=countDocType("Closing", $docTypeArray);?></td>
                            <td <?=(hasDocType("Title", $docTypeArray))     ? htmlentities("style=background-color:#90EE90") : "" ?>><?=countDocType("Title", $docTypeArray);?></td>
                            <td <?=(hasDocType("Financial", $docTypeArray)) ? htmlentities("style=background-color:#90EE90") : "" ?>><?=countDocType("Financial", $docTypeArray);?></td>
                            <td <?=(hasDocType("Personal", $docTypeArray))  ? htmlentities("style=background-color:#90EE90") : "" ?>><?=countDocType("Personal", $docTypeArray);?></td>
                            <td <?=(hasDocType("Internal", $docTypeArray))  ? htmlentities("style=background-color:#90EE90") : "" ?>><?=countDocType("Internal", $docTypeArray);?></td>
                            <td <?=(hasDocType("Legal", $docTypeArray))     ? htmlentities("style=background-color:#90EE90") : "" ?>><?=countDocType("Legal", $docTypeArray);?></td>
                            <td <?=(hasDocType("Other", $docTypeArray))     ? htmlentities("style=background-color:#90EE90") : "" ?>><?=countDocType("Other", $docTypeArray);?></td>
                            <td><?=$totalNumOfDocs[0]?></td>
                            <td>
                                <?php 
                                if (hasAllDocType($docTypeArray) == true){
                                    $completedLoans[] = $loanNum;
                                    echo "COMPLETE!";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </table>
        </div>      <!--- end of div container -->

        <hr class="myborder">

        <!-- LIST OF DOCUMENT TYPES ACROSS ALL LOANS - LIST VERSION  -->

        <div class="container" id="allDocTypesList">
            <h1>LIST OF DOCUMENT TYPES ACROSS ALL LOANS - LIST VERSION</h1><h5><a href="#top">(back to top)</a></h5>
            <hr class="list-border">
            <?php
                // row number count for list display purposes
                $rowNumber = 1;   

                // for each loan number, query for all documents types it has
                foreach ($loanArray as $loanNum) :
    
                    // if the user encounters a blank loan number (aka a file uploaded manually to the database),
                    // skip to the next iteration of the loop
                    if (empty($loanNum)) {
                        continue;
                    }
                    
                    // SELECT query with current loan number
                    $currentLoanDocTypes_query = "SELECT `docType` FROM `documentsCRON` WHERE `loanNum` = '$loanNum'";
                    $currentLoanDocTypes_result = $dblink->query($currentLoanDocTypes_query);
                    $currentLoanDocTypes_rows = $currentLoanDocTypes_result->fetch_all(MYSQLI_ASSOC);
                    
                    // store the current loan's document types in an array
                    $docTypeArray = array();
                    foreach ($currentLoanDocTypes_rows as $loan) {
                        $docTypeArray[] = $loan['docType'];
                    }

                    // find total number of documents for (current loan number)
                    $totalNumOfDocs_query = "SELECT COUNT(`fileSize`) FROM `documentsCRON` WHERE `fileName` LIKE '%$loanNum%'";
                    $totalNumOfDocs_result = $dblink->query($totalNumOfDocs_query);
                    $totalNumOfDocs = $totalNumOfDocs_result->fetch_array(MYSQLI_NUM);
            ?>
                    <!-- for every loan number, list bullet points of the existing + missing document types -->
                    <div id="loanListItem">
                    <p><b>#<?=$rowNumber++?>    <u>Loan Number:</u></b> <?=$loanNum?> <?=(hasAllDocType($docTypeArray)) ? "<b><u>(COMPLETE LOAN!)</u></b>" : ""?></p>
                    <ul>
                        <li><u>Total # of Docs:</u> <?=$totalNumOfDocs[0]?> </li>
                        <li><u>Contains:</u>
                            <ul>
                                <!-- if there is none of doc type, do not print a list item at all -->
                                <?=(hasDocType("Credit", $docTypeArray))    ? printExistingDocTypes("Credit", $docTypeArray)    : ""?>
                                <?=(hasDocType("Closing", $docTypeArray))   ? printExistingDocTypes("Closing", $docTypeArray)   : ""?>
                                <?=(hasDocType("Title", $docTypeArray))     ? printExistingDocTypes("Title", $docTypeArray)     : ""?>
                                <?=(hasDocType("Financial", $docTypeArray)) ? printExistingDocTypes("Financial", $docTypeArray) : ""?>
                                <?=(hasDocType("Personal", $docTypeArray))  ? printExistingDocTypes("Personal", $docTypeArray)  : ""?>
                                <?=(hasDocType("Internal", $docTypeArray))  ? printExistingDocTypes("Internal", $docTypeArray)  : ""?>
                                <?=(hasDocType("Legal", $docTypeArray))     ? printExistingDocTypes("Legal", $docTypeArray)     : ""?>
                                <?=(hasDocType("Other", $docTypeArray))     ? printExistingDocTypes("Other", $docTypeArray)     : ""?>
                            </ul>
                        </li>
                        <li><u>Missing:</u> <?=printMissingDocTypes($docTypeArray)?></li>
                    </ul>
                    </div>
                    <hr class="list-border">
            <?php endforeach; ?>
        </div> <!--- end of div container -->
        
        <hr class="myborder">
        
        <!-- COMPLETED LOANS  -->

        <div class="container" id="completedLoansList">
            <h1>COMPLETED LOANS</h1><h5><a href="#top">(back to top)</a></h5>
            <table>
                <tr>
                    <th>Loan Number</th>
                </tr>
                <?php foreach ($completedLoans as $loan): ?>
                    <tr>
                        <td><?php echo $loan?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div> <!--- end of div container -->

        <hr class="myborder">
    </body>
</html>