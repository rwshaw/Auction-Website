<?php

// Basic DB open a connection.
function OpenDbConnection() {
    $servername = "localhost";
    $user = "website";
    $pass = "3ZqpGsAsmC6U2opZ";
    $database = "auctionsite"; 

    /*
    // Root user details
    $rootuser = "root";
    $rootpass = "";
    */

    $connect = new mysqli($servername, $user, $pass, $database);
    ConfirmDbConnection($connect); 
    return $connect;
}

function ConfirmDbConnection($connect) {
    if($connect -> connect_errno) {
        die('connection to DB failed: ' . $connect -> connect_errno ."<br>" . 'Reason: ' . $connect -> connect_error);
    }
}

// function ConfirmQueryResult($result) {
//     if (!$result || $result->num_rows == 0) {
//         die("Query failed. Reason: " . $result -> error);
//     }
// }

// Basic BD close a connection.
function CloseDbConnection($connection) {
    if (isset($connection)) {
        $connection -> close();
    }
}

function SQLQuery($query) {
    /**
     * // Function to open DB, take query as input, return associative array as result. Associative array can be looped through.
     * 
     * @param string $query Your query within double quotes
     */
    $con = OpenDbConnection();
    $result = $con->query($query);
    if ($result->num_rows>0) {
        $final_result = $result->fetch_all(MYSQLI_ASSOC);
        CloseDbConnection($con);
        return $final_result;
    } // otherwise return error.
    else {
        CloseDbConnection($con);
        return false;
    }
}

// function PrepSQL($prepared_statement, $bindings) {
//     /**
//      * Prepare a SQL statement to be executed.
//      * @param string $prepared_statement SQL statement with unknown values marked with "?"
//      */
//     $con = OpenDbConnection();
//     $stmt = $con->prepare($prepared_statement);
//     $stmt->bind_param($bindings);
// }
?>