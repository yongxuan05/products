<?php
// used to connect to the database
$host = "localhost";
$db_name = "yongxuan";
$username = "yongxuan";
$password = "!G@y/dKdCJiRrIab";

try {
    //$con is a key
    $con = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
}

// show error
catch (PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
}
