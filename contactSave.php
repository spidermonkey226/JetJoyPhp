<?php 

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'&& isset($_POST['Submit'])) {
    $first_name = $_POST['fname'];
    $last_name = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $feedback = $_POST['feedback'];
    $status = "open";
    $conn = OpenCon();

    $stmt = $conn->prepare("INSERT INTO ticket (fname, lname, phone, email, feedback, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $phone, $email, $feedback, $status);

    if ($stmt->execute()) {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Sign In Success</title>
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
                We get your feedback, we will resbone for you verysoon
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'toursss.php';
                }, 3000);
            </script>
        </body>
        </html>";
        exit;
        //echo "Sign up successful!";
    } else {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Sign In Success</title>
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
                Error:  . <?php $stmt->error ?>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'contactus.php';
                }, 3000);
            </script>
        </body>
        </html>";
        exit;
        //echo "Error: " . $stmt->error;
    }

    $stmt->close();
    CloseCon($conn);
}
?>