
<?php

// Basic DB open a connection.
function OpenDbConnection() {
    $servername = "localhost";
    $user = "website";
    $pass = "3ZqpGsAsmC6U2opZ";

    /*
    // Root user details
    $rootuser = "root";
    $rootpass = "";
    */

    $connect = new mysqli($servername, $user, $pass);

    $connect->select_db("auctionsite");

    if ($connect -> error) {
        die("Connection to DB failed: " . $connect->connect_error);
    }
    return $connect;
}

// Basic BD close a connection.
function CloseDbConnection($connection) {
    $connection -> close();
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
        return false;
    }
    // CloseDbConnection($con); //need to manually close connection after using function. PHP will terminate fn execution after return statement.
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