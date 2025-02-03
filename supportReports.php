<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<?php
// Database Connection
$con = new mysqli("localhost", "root", "1234", "jetjoyuser");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Handle Support Answer Update (Appending Replies)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateReport'])) {
    $reportID = intval($_POST['report-id']);
    $newSupportMessage = $con->real_escape_string($_POST['support-answer']);
    $status = $con->real_escape_string($_POST['status']);

    // Fetch the current conversation
    $query = "SELECT supportAnswer FROM ticket WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $reportID);
    $stmt->execute();
    $stmt->bind_result($currentSupportConversation);
    $stmt->fetch();
    $stmt->close();

    // Append new message to existing conversation
    $updatedConversation = trim($currentSupportConversation . "\nSupport: " . $newSupportMessage);

    // Update Report (Appending the reply, not overwriting)
    $stmt = $con->prepare("UPDATE ticket SET supportAnswer = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $updatedConversation, $status, $reportID);

    if ($stmt->execute()) {
        echo "<p>Report ID {$reportID} updated successfully.</p>";
    } else {
        echo "<p>Error updating report ID {$reportID}: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Display Feedback Reports
$result = $con->query("SELECT id, fname, lname, phone, email, feedback, supportAnswer, status FROM ticket");

if ($result->num_rows > 0) {
    echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Feedback</th>
        <th>Conversation</th>
        <th>Status</th>
        <th>Action</th>
    </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['id']) . "</td>
            <td>" . htmlspecialchars($row['fname']) . "</td>
            <td>" . htmlspecialchars($row['lname']) . "</td>
            <td>" . htmlspecialchars($row['phone']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['feedback']) . "</td>
            <td>
                <button onclick='toggleConversation(" . $row['id'] . ")'>View Conversation</button>
                <div id='conversation-" . $row['id'] . "' style='display: none; padding: 10px; border: 1px solid #ddd; margin-top: 10px;'>
                    <p><strong>User:</strong> <span class='message-text'>" . htmlspecialchars($row['feedback']) . "</span></p>
                    <p><strong>Support:</strong> <span class='message-text'>" . htmlspecialchars($row['supportAnswer']) . "</span></p>
                </div>
            </td>
            <td>" . htmlspecialchars($row['status']) . "</td>
            <td>
                <form method='POST' action='' class='action-form'>
                    <input type='hidden' name='report-id' value='" . htmlspecialchars($row['id']) . "'>
                    <textarea name='support-answer' placeholder='Write Answer' required></textarea>
                    <select name='status' required>
                        <option value='' disabled>Select Status</option>
                        <option value='Pending'" . ($row['status'] === 'Pending' ? ' selected' : '') . ">Pending</option>
                        <option value='Resolved'" . ($row['status'] === 'Resolved' ? ' selected' : '') . ">Resolved</option>
                        <option value='Closed'" . ($row['status'] === 'Closed' ? ' selected' : '') . ">Closed</option>
                    </select>
                    <button type='submit' name='updateReport'>Submit</button>
                </form>
            </td>
        </tr>";
    }

    echo "</table>";
} else {
    echo "No reports found.";
}

$con->close();
?>

<link rel="stylesheet" href="styleSupport.css">
<?php include 'footer.php'; ?>

<!-- JavaScript for Show/Hide Conversations -->
<script>
    function toggleConversation(ticketId) {
        var conversationDiv = document.getElementById("conversation-" + ticketId);
        if (conversationDiv.style.display === "none") {
            conversationDiv.style.display = "block";
        } else {
            conversationDiv.style.display = "none";
        }
    }
</script>
