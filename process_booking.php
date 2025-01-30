<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['flight_id'], $_POST['number_of_tickets'], $_POST['first_name'], $_POST['last_name'], $_POST['passport_number'], $_POST['dob'], $_POST['user_id'], $_POST['ticket_type'])) {
    $conn = OpenCon();

    $flightId = (int) $_POST['flight_id'];
    $numberOfTickets = (int) $_POST['number_of_tickets'];
    $userId = (int) $_POST['user_id'];
    $ticketType = $_POST['ticket_type'];
    $firstNames = $_POST['first_name'];
    $lastNames = $_POST['last_name'];
    $passportNumbers = $_POST['passport_number'];
    $dobs = $_POST['dob'];
    $bookingDateTime = date("Y-m-d H:i:s");

    // Validate flight and stock availability for the outbound flight
    $flightQuery = "SELECT * FROM flights WHERE fligtId = $flightId";
    $flightResult = mysqli_query($conn, $flightQuery);
    $flight = mysqli_fetch_assoc($flightResult);

    if (!$flight || $flight['stock'] < $numberOfTickets) {
        echo "<p>Not enough tickets available for this flight.</p>";
        exit;
    }

    // For two-way tickets, validate the return flight
    $returnFlight = null;
    if ($ticketType === "two-way" && isset($_POST['return_flight_id'])) {
        $returnFlightId = (int) $_POST['return_flight_id'];
        $returnFlightQuery = "SELECT * FROM flights WHERE fligtId = $returnFlightId";
        $returnFlightResult = mysqli_query($conn, $returnFlightQuery);
        $returnFlight = mysqli_fetch_assoc($returnFlightResult);

        if (!$returnFlight || $returnFlight['stock'] < $numberOfTickets) {
            echo "<p>Not enough tickets available for the return flight.</p>";
            exit;
        }
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert outbound flight bookings
        for ($i = 0; $i < $numberOfTickets; $i++) {
            $firstName = mysqli_real_escape_string($conn, $firstNames[$i]);
            $lastName = mysqli_real_escape_string($conn, $lastNames[$i]);
            $passportNumber = mysqli_real_escape_string($conn, $passportNumbers[$i]);
            $dob = mysqli_real_escape_string($conn, $dobs[$i]);

            $bookingQuery = "INSERT INTO bookings (userId, firstName, lastName, PassportNumber, dateOfBD, flightId, numberOfTickets, bookingDateTime)
                             VALUES ($userId, '$firstName', '$lastName', '$passportNumber', '$dob', $flightId, 1, '$bookingDateTime')";
            mysqli_query($conn, $bookingQuery);
        }

        // Update outbound flight stock
        $newStock = $flight['stock'] - $numberOfTickets;
        $updateStockQuery = "UPDATE flights SET stock = $newStock WHERE fligtId = $flightId";
        mysqli_query($conn, $updateStockQuery);

        // Insert return flight bookings for two-way tickets
        if ($ticketType === "two-way" && $returnFlight) {
            for ($i = 0; $i < $numberOfTickets; $i++) {
                $firstName = mysqli_real_escape_string($conn, $firstNames[$i]);
                $lastName = mysqli_real_escape_string($conn, $lastNames[$i]);
                $passportNumber = mysqli_real_escape_string($conn, $passportNumbers[$i]);
                $dob = mysqli_real_escape_string($conn, $dobs[$i]);

                $returnBookingQuery = "INSERT INTO bookings (userId, firstName, lastName, PassportNumber, dateOfBD, flightId, numberOfTickets, bookingDateTime)
                                       VALUES ($userId, '$firstName', '$lastName', '$passportNumber', '$dob', $returnFlightId, 1, '$bookingDateTime')";
                mysqli_query($conn, $returnBookingQuery);
            }

            // Update return flight stock
            $newReturnStock = $returnFlight['stock'] - $numberOfTickets;
            $updateReturnStockQuery = "UPDATE flights SET stock = $newReturnStock WHERE fligtId = $returnFlightId";
            mysqli_query($conn, $updateReturnStockQuery);
        }

        // Commit transaction
        mysqli_commit($conn);

        echo "<p>Booking successful!</p>";
        echo "<a href='flights.php'>Return to Flights</a>";
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        echo "<p>Booking failed: " . $e->getMessage() . "</p>";
    }

    CloseCon($conn);
} else {
    echo "<p>Invalid booking request.</p>";
}
