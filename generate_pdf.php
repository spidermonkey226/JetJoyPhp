<?php
session_start(); // Ensure session is started

// Clear any previous output to prevent corruption
ob_end_clean();
ob_start();

require 'vendor/autoload.php'; // Use Composer's autoload
use Dompdf\Dompdf;
use Dompdf\Options;

include 'db_connection.php';

if (!isset($_GET['booking_id']) || !isset($_SESSION['userId'])) {
    die("Invalid request. Please log in and try again.");
}

$bookingId = (int)$_GET['booking_id'];
$userId = $_SESSION['userId'];

$conn = OpenCon();

// Fetch booking details
$query = "SELECT b.flightId, b.bookingDateTime, f.flghtName, f.DepartureDes, f.LandingDes, f.Flightdate, 
                 GROUP_CONCAT(CONCAT(b.firstName, ' ', b.lastName) SEPARATOR ', ') AS passengerNames,
                 b.numberOfTickets
          FROM bookings b
          JOIN flights f ON b.flightId = f.fligtId
          WHERE b.userId = ? AND b.flightId = ?
          GROUP BY b.flightId, b.bookingDateTime";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die("Booking not found.");
}

// Secure data output
$flightName = htmlspecialchars($booking['flightName']);
$departure = htmlspecialchars($booking['DepartureDes']);
$landing = htmlspecialchars($booking['LandingDes']);
$flightDate = htmlspecialchars($booking['Flightdate']);
$bookingTime = htmlspecialchars($booking['bookingDateTime']);
$passengerNames = htmlspecialchars($booking['passengerNames']);
$numberOfTickets = htmlspecialchars($booking['numberOfTickets']);

// Get the absolute path for the logo
$logoPath = __DIR__ . "logo1.png"; // Adjust based on your project structure
$logoBase64 = base64_encode(file_get_contents($logoPath));


// Create HTML for the PDF with styling
$html = "
<style>
    body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
    .container { padding: 20px; border: 1px solid #ddd; width: 600px; margin: auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0px 0px 10px #aaa; }
    .header { text-align: center; padding-bottom: 20px; }
    .header img { width: 150px; }
    .title { font-size: 24px; font-weight: bold; color: #333; text-align: center; }
    .details { padding: 15px; font-size: 16px; }
    .details p { margin: 5px 0; }
    .footer { margin-top: 30px; padding: 10px; text-align: center; background-color: #0073e6; color: white; font-size: 14px; border-radius: 5px; }
    .footer a { color: #fff; text-decoration: none; font-weight: bold; }
</style>

<div class='container'>
    <div class='header'>
        <img src='data:image/png;base64,$logoBase64' alt='Website Logo'>
    </div>

    <div class='title'>Booking Confirmation</div>

    <div class='details'>
        <p><strong>Flight Name:</strong> $flightName</p>
        <p><strong>From:</strong> $departure</p>
        <p><strong>To:</strong> $landing</p>
        <p><strong>Flight Date:</strong> $flightDate</p>
        <p><strong>Booking Time:</strong> $bookingTime</p>
        <p><strong>Passengers:</strong> $passengerNames</p>
        <p><strong>Number of Tickets:</strong> $numberOfTickets</p>
    </div>

    <div class='footer'>
        <p>Thank you for booking with us! Safe travels.</p>
        <p><a href='https://yourwebsite.com/contact'>Contact Us</a> | <a href='https://yourwebsite.com/privacy'>Privacy Policy</a></p>
    </div>
</div>
";

// Set DOMPDF options for better performance
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Clear output buffer again before streaming PDF
ob_end_clean();

// Output the PDF for download
$dompdf->stream("Booking_$bookingId.pdf", ["Attachment" => false]);

CloseCon($conn);
?>
