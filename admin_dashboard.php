<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$conn  = new mysqli("localhost", "root", "1234", "jetjoyuser");

if ($conn ->connect_error) {
    die("Connection failed: " . $conn ->connect_error);
}





$query = "SELECT user.id, user.fname, user.lname, user.email, user.phone, 
                 login_attempts.success, login_attempts.attempt_time
          FROM user 
          LEFT JOIN login_attempts ON user.id = login_attempts.user_id
          ORDER BY login_attempts.attempt_time DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <h2>Admin Dashboard - User Login Attempts</h2>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Success</th>
            <th>Attempt Time</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['fname']); ?></td>
            <td><?php echo htmlspecialchars($row['lname']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo $row['success'] ? 'Success' : 'Failed'; ?></td>
            <td><?php echo htmlspecialchars($row['attempt_time']); ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
