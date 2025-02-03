<?php 
session_start(); // Ensure session starts
include 'db_connection.php';

// Debugging: Check if session variables exist
if (!isset($_SESSION['userName'], $_SESSION['email'])) {
    die("Error: You must be logged in to submit a ticket.<br> Debug Info: <pre>" . print_r($_SESSION, true) . "</pre>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Submit'])) {
    $conn = OpenCon();

    // Auto-fill user details from session
    $first_name = $_SESSION['userName'];  // First Name from session
    $last_name = "";  // Last Name is not stored in session, leaving empty
    $email = $_SESSION['email'];          // Email from session
    $phone = $_SESSION['phone'] ?? '';    // Optional phone field (if not in session, set empty)
    $feedback = trim($_POST['feedback']); // Get feedback input
    $status = "open";                      // Default status

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO ticket (fname, lname, phone, email, feedback, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $phone, $email, $feedback, $status);

    if ($stmt->execute()) {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Feedback Received</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f0f0f0;
                }
                .message {
                    text-align: center;
                    color: green;
                    font-size: 24px;
                    background: #e7ffe7;
                    padding: 20px;
                    border: 1px solid green;
                    border-radius: 10px;
                }
            </style>
        </head>
        <body>
            <div class='message'>
                We received your feedback, we will respond to you very soon.
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'contactus.php';
                }, 3000);
            </script>
        </body>
        </html>";
        exit;
    } else {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Error</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f0f0f0;
                }
                .message {
                    text-align: center;
                    color: red;
                    font-size: 24px;
                    background: #ffe7e7;
                    padding: 20px;
                    border: 1px solid red;
                    border-radius: 10px;
                }
            </style>
        </head>
        <body>
            <div class='message'>
                Error: " . htmlspecialchars($stmt->error) . "
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'contactus.php';
                }, 3000);
            </script>
        </body>
        </html>";
        exit;
    }

    $stmt->close();
    CloseCon($conn);
}
?>
