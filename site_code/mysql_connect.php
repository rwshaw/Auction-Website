 <!-- This won't be served as a file on front end. Include this as an import in each file that needs to connect to DB.
Use functions below to open and close DB connections with the website user login credentials.-->

<?php
// Connecting to DB

function OpenDbConnection() {
    $servername = "localhost";
    $user = "website";
    $pass = "3ZqpGsAsmC6U2opZ";
    $database = "auctionsite"

    /*
    // Root user details
    $rootuser = "root";
    $rootpass = "";
    */

    $connect = new mysqli($servername, $user, $pass, $database);

    if ($connect -> error) {
        die("Connection to DB failed: " . $connection->connect_error);
    }
    return $connect;
}

function CloseDbConnection($connection) {
    if(isset($connection)) {
        $connection -> close();
    }
}

?>