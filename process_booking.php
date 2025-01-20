<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['flight_id'], $_POST['number_of_tickets'], $_POST['first_name'], $_POST['last_name'], $_POST['passport_number'], $_POST['dob'])) {
    $conn = OpenCon();

    $flightId = (int) $_POST['flight_id'];
    $numberOfTickets = (int) $_POST['number_of_tickets'];
    $firstNames = $_POST['first_name'];
    $lastNames = $_POST['last_name'];
    $passportNumbers = $_POST['passport_number'];
    $dobs = $_POST['dob'];

    // Validate flight and stock availability
    $flightQuery = "SELECT * FROM flights WHERE fligtId = $flightId";
    $flightResult = mysqli_query($conn, $flightQuery);
    $flight = mysqli_fetch_assoc($flightResult);

    if (!$flight || $flight['stock'] < $numberOfTickets) {
        echo "<p>Not enough tickets available.</p>";
        exit;
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert booking details
        $bookingDateTime = date("Y-m-d H:i:s");
        $bookingQuery = "INSERT INTO bookings (userId, flightId, numberOfTickets, bookingDateTime) 
                         VALUES (1, $flightId, $numberOfTickets, '$bookingDateTime')";
        mysqli_query($conn, $bookingQuery);

        // Get the inserted booking ID
        $bookingId = mysqli_insert_id($conn);

        // Insert passenger details
        foreach ($firstNames as $index => $firstName) {
            $lastName = mysqli_real_escape_string($conn, $lastNames[$index]);
            $passportNumber = mysqli_real_escape_string($conn, $passportNumbers[$index]);
            $dob = mysqli_real_escape_string($conn, $dobs[$index]);

            $passengerQuery = "INSERT INTO passenger_details (bookingId, firstName, lastName, passportNumber, dateOfBirth) 
                               VALUES ($bookingId, '$firstName', '$lastName', '$passportNumber', '$dob')";
            mysqli_query($conn, $passengerQuery);
        }

        // Update flight stock
        $newStock = $flight['stock'] - $numberOfTickets;
        $updateStockQuery = "UPDATE flights SET stock = $newStock WHERE fligtId = $flightId";
        mysqli_query($conn, $updateStockQuery);

        // Commit transaction
        mysqli_commit($conn);

        echo "<p>Booking successful!</p>";
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
