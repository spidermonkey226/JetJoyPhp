<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'navbar.php';
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    echo "<p style='color: red;'>You need to log in to submit a report. <a href='sign.php'>Log in here</a></p>";
    exit;
}

$userEmail = $_SESSION['email'];  // Get the logged-in user's email
$conn = OpenCon();

// Fetch tickets related to the logged-in user's email
$query = "SELECT id, fname, feedback, supportAnswer, status FROM ticket WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports</title>
    <link rel="stylesheet" href="stylecontactus.css">
</head>
<body>
    <div class="product-grid">
        <div class="top-bar">
            <h1>My Reports</h1>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="container">
        <!-- Reports Section -->
        <div class="reports">
            <h2>Your Reports</h2>
            <table>
                <tr>
                    <th>First Name</th>
                    <th>Feedback</th>
                    <th>Conversation</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['fname']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                        <td>
                            <!-- Reply Button to Open Full Conversation -->
                            <button onclick="toggleConversation(<?php echo $row['id']; ?>)">Reply</button>
                            <div id="conversation-<?php echo $row['id']; ?>" style="display: none; padding: 10px; border: 1px solid #ddd; margin-top: 10px;">
                                
                                <!-- Display All Messages (User Feedback + Support Answer) -->
                                <p><strong>User:</strong> <span class='message-text'><?php echo htmlspecialchars($row['feedback']); ?></span></p>
                                <p><strong>Support:</strong> <span class='message-text'><?php echo htmlspecialchars($row['supportAnswer']); ?></span></p>

                                <!-- Reply Form (User Can Add More Replies) -->
                                <form action="reply_ticket.php" method="post">
                                    <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                                    <textarea name="reply_message" placeholder="Write your reply here..." required></textarea>
                                    <button type="submit">Send Reply</button>
                                </form>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Open New Ticket Section -->
        <h1>Open New Ticket</h1>
        <form class="frd" action="contactSave.php" method="post">
            <textarea id="feedback" name="feedback" placeholder="Write your feedback" required></textarea>
            <button type="submit" id="submit-form" name="Submit">Submit</button>
        </form>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
    <script src="conectuscheck.js"></script>
    
    <!-- JavaScript for Show/Hide Conversations -->
    <script>
        function toggleConversation(ticketId) {
            var conversationDiv = document.getElementById("conversation-" + ticketId);
            conversationDiv.style.display = "block"; // Always open when clicking "Reply"
        }
    </script>
</body>
</html>
