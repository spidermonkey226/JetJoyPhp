<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'navbar.php';
include 'db_connection.php';

if (!isset($_SESSION['userId'])) {
    echo "<p style='color: red;'>You need to log in to view your booking history. <a href='sign.php'>Log in here</a></p>";
    exit;
}

$userId = $_SESSION['userId'];
$conn = OpenCon();

// Fetch bookings grouped by flight and booking time
$query = "SELECT b.flightId, b.bookingDateTime, f.flghtName, f.DepartureDes, f.LandingDes, f.Flightdate, 
                 GROUP_CONCAT(CONCAT(b.firstName, ' ', b.lastName) SEPARATOR ', ') AS passengerNames,
                 b.numberOfTickets
          FROM bookings b
          JOIN flights f ON b.flightId = f.fligtId
          WHERE b.userId = ?
          GROUP BY b.flightId, b.bookingDateTime";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link rel="stylesheet" href="stylebookinghistory.css">
</head>
<body>
    <div class="page-container">
        <div class="content-wrap">
            <h1>Your Booking History</h1>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="booking-item">
                        <h2>Flight: <?php echo htmlspecialchars($row['flghtName']); ?></h2>
                        <p><strong>From:</strong> <?php echo htmlspecialchars($row['DepartureDes']); ?></p>
                        <p><strong>To:</strong> <?php echo htmlspecialchars($row['LandingDes']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($row['Flightdate']); ?></p>
                        <p><strong>Booking Time:</strong> <?php echo htmlspecialchars($row['bookingDateTime']); ?></p>
                        <p><strong>Passengers:</strong> <?php echo htmlspecialchars($row['passengerNames']); ?></p>
                        <p><strong>Number of Tickets:</strong> <?php echo htmlspecialchars($row['numberOfTickets']); ?></p>
                        
                        <a href="generate_pdf.php?booking_id=<?php echo $row['flightId']; ?>" target="_blank">
                            <button>Download PDF</button>
                        </a>
                    </div>
                <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>You have no bookings yet.</p>
            <?php endif; ?>

        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
CloseCon($conn);
?>
