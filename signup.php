<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userDetalis";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['fname'];
    $last_name = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['psw'], PASSWORD_BCRYPT); // Hash the password

    $sql = "INSERT INTO users (first_name, last_name, phone, email, password)
            VALUES ('$first_name', '$last_name', '$phone', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>