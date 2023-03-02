<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sentimentanalyzer";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if(!$conn)
    {
        die('Please check your connection');
    }
?>
