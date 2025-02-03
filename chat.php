<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="styleChat.css">
<?php
// Database Connection
$con = new mysqli("localhost", "root", "1234", "jetjoyuser");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Retrieve report ID and user email
$report_id = isset($_GET['report_id']) ? intval($_GET['report_id']) : 0;
$user_email = isset($_GET['user_email']) ? $con->real_escape_string($_GET['user_email']) : '';

if ($report_id == 0 || empty($user_email)) {
    die("Invalid report ID or user email.");
}

// Fetch chat history
$result = $con->query("SELECT * FROM ticket WHERE reportId = $report_id ORDER BY id ASC");

// Display chat messages
echo "<h2>Chat for Report ID: $report_id</h2>";
echo "<div class='chat-box'>";

while ($row = $result->fetch_assoc()) {
    if (!empty($row['feedback'])) {
        echo "<div class='user-message'><strong>User:</strong> " . htmlspecialchars($row['feedback']) . "</div>";
    }
    if (!empty($row['supportAnswer'])) {
        echo "<div class='admin-message'><strong>Admin:</strong> " . htmlspecialchars($row['supportAnswer']) . "</div>";
    }
}
echo "</div>";

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $con->real_escape_string($_POST['message']);
    $sender = $_POST['sender']; // 'user' or 'admin'

    if ($sender === 'user') {
        $stmt = $con->prepare("
            INSERT INTO ticket (reportId, fname, lname, phone, email, feedback, supportAnswer, status) 
            SELECT ?, fname, lname, phone, email, ?, '', status FROM ticket WHERE reportId = ? LIMIT 1
        ");
        $stmt->bind_param("isi", $report_id, $message, $report_id);
    } else {
        $stmt = $con->prepare("
            INSERT INTO ticket (reportId, fname, lname, phone, email, feedback, supportAnswer, status) 
            SELECT ?, fname, lname, phone, email, '', ?, status FROM ticket WHERE reportId = ? LIMIT 1
        ");
        $stmt->bind_param("isi", $report_id, $message, $report_id);
    }

    if ($stmt->execute()) {
        header("Location: chat.php?report_id=$report_id&user_email=$user_email");
        exit();
    } else {
        echo "<p>Error saving message: " . $stmt->error . "</p>";
    }
}
?>

<form method="POST" action="">
    <input type="hidden" name="sender" value="admin">
    <textarea name="message" required placeholder="Type your message here..."></textarea>
    <button type="submit">Send</button>
</form>

<?php include 'footer.php'; ?>
