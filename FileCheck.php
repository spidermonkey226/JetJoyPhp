<?php
$message = ""; 
if (isset($_POST['submit'])) {

    $target_dir = "uploads/";

    $file1 = $target_dir . basename($_FILES['file1']['name']);
    $file2 = $target_dir . basename($_FILES['file2']['name']);

    if (move_uploaded_file($_FILES['file1']['tmp_name'], $file1) && move_uploaded_file($_FILES['file2']['tmp_name'], $file2)) {

        $file1txt = fopen($file1, "r");
        $file2txt = fopen($file2, "r");

        if ($file1txt && $file2txt) {
            $file1_content = fread($file1txt, filesize($file1));
            $file2_content = fread($file2txt, filesize($file2));

            if ($file1_content == $file2_content) {
                $message = "Files are equal"; 
            } else {
                $message = "Files are not equal"; 
            }

            fclose($file1txt);
            fclose($file2txt);
        } else {
            $message = "Error opening one or both files."; 
        }
    } else {
        $message = "Error uploading one or both files."; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Luxury Holiday Packages</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php include 'header.php'; ?>
    
    <div class="navbar-new">
        <h1 class="luxury">Luxury Holiday Packages</h1>
    </div>

    <div class="content">
        <h2>Welcome to Luxury Holiday Packages</h2>
        <p>Explore our exclusive holiday packages and experience the luxury you deserve.</p>
    </div>

    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="file1">Upload First File:</label>
            <input type="file" name="file1" required>
            <label for="file2">Upload Second File:</label>
            <input type="file" name="file2" required>
            <button type="submit" name="submit">Upload Files</button>
        </form>

        <div class="message">
            <?php echo $message; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
