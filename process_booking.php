<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['flight_id'], $_POST['number_of_tickets'], $_POST['first_name'], $_POST['last_name'], $_POST['passport_number'], $_POST['dob'], $_POST['user_id'])) {
    $conn = OpenCon();

    $flightId = (int) $_POST['flight_id'];
    $numberOfTickets = (int) $_POST['number_of_tickets'];
    $userId = (int) $_POST['user_id'];
    $firstNames = $_POST['first_name'];
    $lastNames = $_POST['last_name'];
    $passportNumbers = $_POST['passport_number'];
    $dobs = $_POST['dob'];
    $bookingDateTime = date("Y-m-d H:i:s");

    // Validate flight and stock availability
    $flightQuery = "SELECT * FROM flights WHERE fligtId = $flightId";
    $flightResult = mysqli_query($conn, $flightQuery);
    $flight = mysqli_fetch_assoc($flightResult);

    if (!$flight || $flight['stock'] < $numberOfTickets) {
        echo "<p>Not enough tickets available for this flight.</p>";
        exit;
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert booking details for each passenger
        for ($i = 0; $i < $numberOfTickets; $i++) {
            $firstName = mysqli_real_escape_string($conn, $firstNames[$i]);
            $lastName = mysqli_real_escape_string($conn, $lastNames[$i]);
            $passportNumber = mysqli_real_escape_string($conn, $passportNumbers[$i]);
            $dob = mysqli_real_escape_string($conn, $dobs[$i]);

            $bookingQuery = "INSERT INTO bookings (userId, firstName, lastName, PassportNumber, dateOfBD, flightId, numberOfTickets, bookingDateTime)
                             VALUES ($userId, '$firstName', '$lastName', '$passportNumber', '$dob', $flightId, 1, '$bookingDateTime')";
            mysqli_query($conn, $bookingQuery);
        }

        // Update flight stock
        $newStock = $flight['stock'] - $numberOfTickets;
        $updateStockQuery = "UPDATE flights SET stock = $newStock WHERE fligtId = $flightId";
        mysqli_query($conn, $updateStockQuery);

        // Commit transaction
        mysqli_commit($conn);

        echo "<p>Booking successful!</p>";
        echo "<a href='flight.php'>Return to Flights</a>";
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        echo "<p>Booking failed: " . $e->getMessage() . "</p>";
    }

    CloseCon($conn);
} else {
    echo "<p>Invalid booking request.</p>";
}
?>
