<?php
    function getUniqueLoanNumArray($data) {
        // initialize array
        $loanArray = array();

        // store every loan number (row) inside the loan array
        foreach ($data as $loan) {

            // if loanNum is not blank (aka the file has NOT been uploaded manually)
            if (!empty($loan['loanNum']))
            $loanArray[] = $loan['loanNum'];
        }

        // create a new array that only contains unique loan numbers (aka no duplicates)
        $loanArrayUnique = array_unique($loanArray);

        return $loanArrayUnique;
    }

    function getTotalSizeOfAllDocs($data) {
        $totalFileSize = 0;

        foreach ($data as $loan) {
            // if loanNum is not blank (aka the file has NOT been uploaded manually)
            if (!empty($loan['loanNum']))
                $totalFileSize += $loan['fileSize'];
        }

        return $totalFileSize;
    }

    // Function used for LIST OF DOCUMENT TYPES ACROSS ALL LOANS - TABLE VERSION
    // create a function to change table cell color to green if the document type is contained in the loan
    function hasDocType($type, $array) {
        // if (in_array($type, $array)) 
        //     echo htmlentities("style=background-color:#90EE90");

        return in_array($type, $array);
        
    }

    // Function used for LIST OF DOCUMENT TYPES ACROSS ALL LOANS
    // create a function to fill each column
    function countDocType($type, $array) {
        // counter for the # of documents per type
        $count = 0;
        
        // increment counter for every number of the doc type
        foreach ($array as $element) {
            if ($type == $element)
                $count++;
        }

        // if doc type is contained in the loan, print a checkmark and the number of that doc type
        if ($count > 0){
            echo "&#x2714;" . " = " . $count;
        }
    }

    // Function used for LIST OF DOCUMENT TYPES ACROSS ALL LOANS - TABLE VERSION
    // Determine if a loan is complete (aka has ALL types of documents)
    function hasAllDocType($array) {
        $allDocTypeArray = array("Credit", "Closing", "Title", "Financial", "Personal", "Internal", "Legal", "Other");

        if (count(array_diff($allDocTypeArray, $array)) == 0)
            return true;
    }

    // Function used for LIST OF DOCUMENT TYPES ACROSS ALL LOANS - LIST VERSION
    // Prints the doc type if it exists in the loan + number of occurrences
    function printExistingDocTypes($type, $array) {
        $count = count(array_keys($array, $type));

        if ($count > 0) {
            echo "<li>$count - $type</li>";
        }
    }

    // Function used for LIST OF DOCUMENT TYPES ACROSS ALL LOANS - LIST VERSION
    // Prints the doc types that are missing from the loan
    function printMissingDocTypes($array) {
        $allDocTypeArray = array("Credit", "Closing", "Title", "Financial", "Personal", "Internal", "Legal", "Other");
        $missing = array_diff($allDocTypeArray, $array);

        foreach ($missing as $element) {
            echo $element . ", ";
        }
    }
?>