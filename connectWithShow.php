<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<?php
$con = new mysqli("localhost", "root", "1234", "jetjoyuser");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Add new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUser'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing
    $host = isset($_POST['Host']) ? 1 : 0;

    $stmt = $con->prepare("INSERT INTO user (fname, lname, phone, email, password, Host) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $fname, $lname, $phone, $email, $password, $host);
    
    if ($stmt->execute()) {
        header("Location: connectWithShow.php"); // Refresh page after adding
        exit;
    } else {
        echo "<p style='color:red;'>Error adding user.</p>";
    }
}

// Edit user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editUser'])) {
    $id = $_POST['user_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $host = isset($_POST['Host']) ? 1 : 0;

    $stmt = $con->prepare("UPDATE user SET fname=?, lname=?, phone=?, email=?, Host=? WHERE id=?");
    $stmt->bind_param("ssssii", $fname, $lname, $phone, $email, $host, $id);
    
    if ($stmt->execute()) {
        header("Location: connectWithShow.php"); // Refresh page after updating
        exit;
    } else {
        echo "<p style='color:red;'>Error updating user.</p>";
    }
}

// Fetch users
$result = $con->query("SELECT id, fname, lname, phone, email, Host FROM user");

?>

<link rel="stylesheet" href="styleShowUser.css">

<div class="user-container">
    <h1>Manage Users</h1>
    <?php if ($result->num_rows > 0): ?>
        <table border='1'>
            <tr>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Host</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="connectWithShow.php">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <td><input type="text" name="fname" value="<?php echo $row['fname']; ?>" required></td>
                        <td><input type="text" name="lname" value="<?php echo $row['lname']; ?>" required></td>
                        <td><input type="text" name="phone" value="<?php echo $row['phone']; ?>" required></td>
                        <td><input type="email" name="email" value="<?php echo $row['email']; ?>" required></td>
                        <td>
                            <input type="checkbox" name="Host" <?php echo $row['Host'] ? 'checked' : ''; ?>>
                        </td>
                        <td><button type="submit" name="editUser">Save</button></td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <!-- Add New User Form -->
    <div class="add-user-form">
        <h2>Add New User</h2>
        <form method="POST" action="connectWithShow.php">
            <input type="text" name="fname" placeholder="First Name" required>
            <input type="text" name="lname" placeholder="Last Name" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <label><input type="checkbox" name="Host"> Host</label></br>
            <button type="submit" name="addUser">Add User</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
