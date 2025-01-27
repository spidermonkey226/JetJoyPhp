<?php
include 'header.php';
include 'navbar.php';
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['flight_id'], $_GET['number_of_tickets'])) {
    $flightId = (int) $_GET['flight_id'];
    $numberOfTickets = (int) $_GET['number_of_tickets'];
    $userId = $_SESSION['userId']; // Get userId from session

    $conn = OpenCon();
    
    // Fetch flight details
    $flightQuery = "SELECT * FROM flights WHERE fligtId = $flightId";
    $flightResult = mysqli_query($conn, $flightQuery);
    $flight = mysqli_fetch_assoc($flightResult);

    if (!$flight) {
        echo "<p>Invalid flight ID.</p>";
        exit;
    }
}
?>

<div class="booking-form">
<link rel="stylesheet" href="stylebooking.css">
    <h2>Enter Passenger Details</h2>
    <form action="process_booking.php" method="POST">
        <input type="hidden" name="flight_id" value="<?php echo $flightId; ?>">
        <input type="hidden" name="number_of_tickets" value="<?php echo $numberOfTickets; ?>">
        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">

        <?php for ($i = 1; $i <= $numberOfTickets; $i++): ?>
            <h3>Passenger <?php echo $i; ?></h3>
            <label for="first_name_<?php echo $i; ?>">First Name:</label>
            <input type="text" id="first_name_<?php echo $i; ?>" name="first_name[]" required>

            <label for="last_name_<?php echo $i; ?>">Last Name:</label>
            <input type="text" id="last_name_<?php echo $i; ?>" name="last_name[]" required>

            <label for="passport_number_<?php echo $i; ?>">Passport Number:</label>
            <input type="text" id="passport_number_<?php echo $i; ?>" name="passport_number[]" required>

            <label for="dob_<?php echo $i; ?>">Date of Birth:</label>
            <input type="date" id="dob_<?php echo $i; ?>" name="dob[]" required>
        <?php endfor; ?>

        <button type="submit">Confirm Booking</button>
    </form>
</div>

<?php
include 'footer.php';
?>
