<?php
include 'users.php';
include 'db_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $conn = OpenCon();
    $user_found = true;
    // Fetch user details from the database
    $result = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");

    if (!$result || mysqli_num_rows($result) === 0) {
        $_SESSION['reset_message'] = "Email not found.";
        $user_found = false;
        CloseCon($conn);
        header('Location: sign.php');
        exit;
    }
    // Validate email
    if($user_found) {
        $user = mysqli_fetch_assoc($result);
        // Search for the user by email in the array
        
        
        $new_pass = '';
        $reset_link = "http://localhost/labs/php-JetJoy-main/updatepass.php";

        //Generate a 6-character random password
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 6; $i++) {
            $ind = rand(0, strlen($characters) - 1);
            $new_pass .= $characters[$ind];
        }
      
        mysqli_query($conn, "UPDATE user SET password='$new_pass',attempts = 0, locked = 0,locktime = " . time() ." WHERE email = '$email'");

                // (Optional) Save the updated user array back to the file
        $to = "alaadabour5@gmail.com";
        $subject = "Password Reset";
        $message = "<b>You reset your password</b>";
        $message .= "<h1>Your new password is $new_pass</h1>";
        $message .= "<a href='$reset_link'>Reset your password</a>";      
                
        $header = "From:alaadabour5@gmail.com \r\n";
        //$header .= "Cc:alaadabour5@gmail.com \r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";
                
        $retval = mail ($to,$subject,$message,$header);
                
        if( $retval == true ) {
            $_SESSION['reset_message'] = "Message sent successfully.";
        }else {
            $_SESSION['reset_message'] = "Message could not be sent.";
        }
            
        CloseCon($conn);
        header('Location: sign.php');
        exit;

       
    }

   
}
?>
