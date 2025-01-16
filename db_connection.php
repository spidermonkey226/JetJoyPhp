<?php
function OpenCon()
 {
 $dbhost = "localhost";
 $dbuser = "root";
 $dbpass = "1234";
 $db = "jetjoyuser";
 $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $db);

 // Check if connection failed
 if (!$conn) {
     die("Connect failed: " . mysqli_connect_error());
 }
 return $conn;
 }
 
function CloseCon($conn)
{
    $conn -> close();
}


   
?>