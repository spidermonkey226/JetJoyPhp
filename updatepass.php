<?php

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; // This assumes the user is logged in or their email is known
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $conn = OpenCon();

    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    }
    else {
    // Hash the password for security
    //$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $query = "UPDATE user SET password = '$new_password',attempts = 0, locked = 0,locktime = " . time() ."  WHERE email = '$email'";
    if (mysqli_query($conn, $query)) {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Sign In Success</title>
            <style>
                container {
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
               Password updated successfully.
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
        //$message = "Password updated successfully.";
    } else {
        $message = "Error updating password: " . mysqli_error($conn);
    }
}

CloseCon($conn);

}
?>

    <title>Update Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container input[type="password"],
        .form-container input[type="email"],
        .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 10px;
            color: red;
        }
    </style>

    <div class="container" id="container">
        <div class="form-container sign-up">
        <form method="POST" action="updatepass.php">

            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <span id="email-error" class="error-message"></span>
            <input type="password" id="newpass" name="new_password" placeholder="Enter new password" required>
            <span id="newpass-error" class="error-message"></span>
            <input type="password" id="confpass"name="confirm_password" placeholder="Confirm new password" required>
            <span id="conpass-error" class="error-message"></span>
            <button type="submit" id="submit-button" name="submit">Update Password</button>
            
        </form>
        </div>
    </div>

    <script>
    let email = document.getElementById("email");
    let newpassword = document.querySelector('input[name="new_password"]');
    let conpassword = document.querySelector('input[name="confirm_password"]');
    const submitButton = document.getElementById('submit-button');

    email.addEventListener('blur', () => {
        const isValid = isValidEmail(email.value);
        document.getElementById('email-error').textContent = isValid ? '' : 'Invalid email address';
    });
    newpassword.addEventListener('blur', () => {
        const isValid = isValidPassword(newpassword.value);
        document.getElementById('newpass-error').textContent = isValid ? '' : 'Invalid password';
    });
    conpassword.addEventListener('blur', () => {
        const isValid = isValidPassword(conpassword.value);
        document.getElementById('conpass-error').textContent = isValid ? '' : 'Invalid password';
    });

    email.addEventListener('blur', validateInputs);
    newpassword.addEventListener('blur', validateInputs);
    conpassword.addEventListener('blur', validateInputs);

    function isValidEmail(email) {
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return regex.test(email);
    }

    function isValidPassword(password) {
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        return regex.test(password);
    }

    function validateInputs() {
        const isValidEmailValue = isValidEmail(email.value);
        const isValidPasswordValue = isValidPassword(password.value);
        submitButton.disabled = !(isValidEmailValue && isValidPasswordValue);
    }
</script>

