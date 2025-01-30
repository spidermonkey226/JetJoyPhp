<head>
    <link rel="stylesheet" href="stylenav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Default role if not logged in
$role = $_SESSION['role'] ?? 'guest'; // 'guest' for visitors
?>
<nav>
    <ul>
        <li><a href="toursss.php">Home</a></li>
        
        <?php if ($role === 'admin'): ?>
            <li><a href="addflight.php"><i class="bi bi-airplane"></i> Flights</a></li>
            <li><a href="hotels.php"> Hotels</a></li>
            <li><a href="ourdeals.php">Our Deals</a></li>
            <li><a href="car.php"><i class="bi bi-car-front"></i> Car Rent</a></li>
            <li><a href="connectWithShow.php">Manage Users</a></li>
            <li><a href="supportReports.php">Reports</a></li>
        <?php endif; ?>
        <?php if ($role === 'guest'): ?>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="flights.php"><i class="bi bi-airplane"></i> Flights</a></li>
            <li><a href="hotels.php"> Hotels</a></li>
            <li><a href="ourdeals.php">Our Deals</a></li>
            <li><a href="car.php"><i class="bi bi-car-front"></i> Car Rent</a></li>
            <li><a href="bookinghistory.php">booking history</a></li>
        <?php endif; ?>
    </ul>
</nav>


