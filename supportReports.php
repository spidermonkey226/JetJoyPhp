<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

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
$result = $con->query("SELECT * FROM ticket WHERE reportId = $report_id ORDER BY id ASC LIMIT 1");
$row = $result->fetch_assoc();

// Display chat messages
echo "<h2>Chat for Report ID: $report_id</h2>";
echo "<div class='chat-box'>";

$feedback_messages = explode("\n", $row['feedback'] ?? '');
$support_messages = explode("\n", $row['supportAnswer'] ?? '');

foreach ($feedback_messages as $message) {
    if (!empty(trim($message))) {
        echo "<div class='user-message'><strong>User:</strong> " . htmlspecialchars($message) . "</div>";
    }
}

foreach ($support_messages as $message) {
    if (!empty(trim($message))) {
        echo "<div class='admin-message'><strong>Admin:</strong> " . htmlspecialchars($message) . "</div>";
    }
}

echo "</div>";

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $con->real_escape_string($_POST['message']);
    $sender = $_POST['sender']; // 'user' or 'admin'

    if ($sender === 'user') {
        $stmt = $con->prepare("UPDATE ticket SET feedback = CONCAT(IFNULL(feedback, ''), '\n', ?) WHERE reportId = ?");
        $stmt->bind_param("si", $message, $report_id);
    } else {
        $stmt = $con->prepare("UPDATE ticket SET supportAnswer = CONCAT(IFNULL(supportAnswer, ''), '\n', ?) WHERE reportId = ?");
        $stmt->bind_param("si", $message, $report_id);
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
