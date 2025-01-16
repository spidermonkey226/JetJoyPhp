<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<?php
$con = new mysqli("localhost", "root", "1234", "jetjoyuser");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$result = $con->query("SELECT fname, lname, phone, email ,password FROM user");

if ($result->num_rows > 0) {
    echo "<table border='1'>
    <tr>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Pass</th>
    </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['fname']}</td>
            <td>{$row['lname']}</td>
            <td>{$row['phone']}</td>
            <td>{$row['email']}</td>
            <td>{$row['password']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "No users found.";
}

$con->close();
?>
<link rel="stylesheet" href="styleShowUser.css">

<?php include 'footer.php'; ?>

