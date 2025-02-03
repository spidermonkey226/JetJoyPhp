<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['signin-email']) || !isset($_POST['signin-password'])) {
        $_SESSION['error_sign'] = "Please provide both email and password.";
        //echo "Please provide both email and password.";
        header('Location: sign.php');
        exit;
    }

    $email = trim($_POST['signin-email']);
    $password = trim($_POST['signin-password']);
    $conn = OpenCon();

    // Fetch user details from the database
    $result = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");

    if (!$result || mysqli_num_rows($result) === 0) {
        $_SESSION['error_sign'] = "Email not found.";
        //echo "Email not found.";
        header('Location: sign.php');
        CloseCon($conn);
        exit;
    }

    $user = mysqli_fetch_assoc($result);

    // Check if the account is locked
    if ($user['locked']) {
        $time_elapsed = time() - $user['locktime'];
        if ($time_elapsed >= 60) {
            // Unlock account after 1 minute
            mysqli_query($conn, "UPDATE user SET locked = 0, attempts = 0, locktime = 0 WHERE email = '$email'");
            $user['locked'] = 0;
            $user['attempts'] = 0;
            $_SESSION['error_sign'] = "Your account is now unlocked. Please try again.";
            header('Location: sign.php');
            //echo "Your account is now unlocked. Please try again.";
        } else {
            $remaining_time = 60 - $time_elapsed;
            $_SESSION['error_sign'] = "Your account is locked. Please try again after $remaining_time seconds.";
            //echo "Your account is locked. Please try again after $remaining_time seconds.";
            header('Location: sign.php');
            CloseCon($conn);
            exit;
        }
    }

    // Check the password
    if ($password === $user['password']) {
        // Reset attempts on successful login
        mysqli_query($conn, "UPDATE user SET attempts = 0, locked = 0, locktime = 0 WHERE email = '$email'");
        mysqli_query($conn, "INSERT INTO login_attempts (user_id, email, success) VALUES ('{$user['id']}', '$email', 1)");
        $_SESSION['user_name'] = $user['fname'];
        $_SESSION['role'] = $user['Host'] ? 'admin' : 'guest'; 
        $_SESSION['userId'] = $user['id']; // Store user ID
        $_SESSION['userName'] = $user['fname']; // Optionally store user first name
    
        // Redirect based on existing behavior
        header("Location: toursss.php");
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
                    You have signed in successfully!<br>Redirecting to home...
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'toursss.php';
                    }, 3000);
                </script>
            </body>
            </html>";
            CloseCon($conn);
            exit;
       
    } else {
        // Increment attempts and lock the account if necessary
        $new_attempts = $user['attempts'] + 1;
        mysqli_query($conn, "INSERT INTO login_attempts (user_id, email, success) VALUES ('{$user['id']}', '$email', 0)");

        if ($new_attempts >= 3) {
            mysqli_query($conn, "UPDATE user SET attempts = $new_attempts, locked = 1, locktime = " . time() . " WHERE email = '$email'");
            $_SESSION['error_sign'] = "Incorrect password. Your account is now locked for 1 minute.";
            //echo "Your account is locked. Please try again after $remaining_time seconds.";
            header('Location: sign.php');
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
                    Incorrect password. Your account is now locked for 1 minute.
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'sign.php';
                    }, 3000);
                </script>
            </body>
            </html>";*/
            exit;
            //echo "Incorrect password. Your account is now locked for 1 minute.";
        } else {
            mysqli_query($conn, "UPDATE user SET attempts = $new_attempts WHERE email = '$email'");
            $_SESSION['error_sign'] = "Incorrect password. Attempt $new_attempts of 3.";
            //echo "Your account is locked. Please try again after $remaining_time seconds.";
            header('Location: sign.php');
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
                    Incorrect password. Attempt $new_attempts of 3.
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'sign.php';
                    }, 3000);
                </script>
            </body>
            </html>";*/
            exit;
            //echo "Incorrect password. Attempt $new_attempts of 3.";
        }
    }

    CloseCon($conn);
} else {
    $_SESSION['error_sign'] = "Invalid request method.";
    header('Location: sign.php');
    //echo "Invalid request method.";
    exit;
}
?>
