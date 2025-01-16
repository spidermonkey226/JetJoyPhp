<?php

include 'db_connection.php';




if ($_SERVER['REQUEST_METHOD'] === 'POST'&& isset($_POST['signup'])) {
    $first_name = $_POST['fname'];
    $last_name = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = ($_POST['psw']); // Hash the password

    $conn = OpenCon();
    // Check if email already exists
    $query = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $query->store_result();

    if ($query->num_rows > 0) {
        $_SESSION['error_signup'] = "Error: User with this email already exists.";
        //echo "Please provide both email and password.";

        /*echo "
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
                Error: User with this email already exists.
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'sign.php';
                }, 3000);
            </script>
        </body>
        </html>";*/
        //echo "Error: User with this email already exists.";
        $query->close();
        CloseCon($conn);
 
        exit;
    }
    $query->close();
    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO user (fname, lname, phone, email, password, locked, attempts, locktime) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $locked = 0;
    $attempts = 0;
    $timelocked = 0;
    $stmt->bind_param("sssssiis", $first_name, $last_name, $phone, $email, $password, $locked, $attempts, $timelocked);
    

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
                Sign up successful!
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'sign.php';
                }, 3000);
            </script>
        </body>
        </html>";
        exit;
        //echo "Sign up successful!";
    } else {
        $_SESSION['error_signup'] = "Error:  . <?php $stmt->error ?>";
        /*echo "
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
                    window.location.href = 'sign.php';
                }, 3000);
            </script>
        </body>
        </html>";*/
 
        exit;
        //echo "Error: " . $stmt->error;
    }

    $stmt->close();
    CloseCon($conn);
    // Create an associative array
   

    //$file_path = 'users.json';
    /*
    // Read existing data
    $json_data = file_exists($file_path) ? json_decode(file_get_contents($file_path), true) : [];

    // Add new data
    $json_data[] = $user_data;

    // Save back to file
    file_put_contents($file_path, json_encode($json_data, JSON_PRETTY_PRINT));

    echo "User data saved successfully.";*/
}
?>
