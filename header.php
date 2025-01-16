
<link rel="stylesheet" href="stylehead.css">

<header>
    <div class="header-container">
       
        <div class="header-left">
            <a href="toursss.php">
                <img src="logo1.png" alt="Website Logo" class="logo">
            </a>
        </div>
        
        <div class="header-center">
            <h1>My Website</h1>
        </div>

        
        <div class="header-right">
            <?php
             
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            if (isset($_SESSION['user_name'])): ?>
                <span>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <a href="logout.php" class="logout-button">Log Out</a>
            <?php else: ?>
                <a href="sign.php">
                    <i class="bi bi-person-circle" id="account"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
