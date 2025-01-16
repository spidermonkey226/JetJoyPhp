<?php?>
<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="stylecontactus.css">
<body>
<div class="product-grid">
        <div class="top-bar">
            <h1>Contact Us</h1>
        </div>
</div>
<div class="contener">
<div class="fr">
    <br>
    <form class="frd" action="contactSave.php" method="post">
        <input type="text" id="fname" name="fname" placeholder="First Name">
        <span id="fname-error" class="error-message"></span>
        <input type="text" id="lname" name="lname" placeholder="Last Name" />
        <span id="lname-error" class="error-message"></span>
        <input type="text" id="phone" name="phone" placeholder="Phone Number" />
        <span id="phone-error" class="error-message"></span>
        <input type="email" id="email" name="email" placeholder="Email">
        <span id="email-error" class="error-message"></span>
        <input type="text" id="feedback" name="feedback" placeholder="feedback" style="width: 500; height: 250;"><br><br>
        <button id="submet-form" name="Submit">Submit</button>
    </form>

    <p>Click on the submit button to submit the feedback.</p>
</div>
</div>
    <?php include 'footer.php'; ?>
    <script src="conectuscheck.js"></script>
</body>
