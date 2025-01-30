<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php';
include 'navbar.php';

$ticketType = ""; 
$searchPerformed = false;
$errorMessage = "";
?>
<div class="page-container">
    <div class="content-wrap">
        <div class="search-section">
            <link rel="stylesheet" href="styleflight.css">
            <h2>Search Flights</h2>

            <?php
            include 'db_connection.php';
            $conn = OpenCon();

            // Fetch unique departure and landing destinations from the flights table
            $departureOptions = mysqli_query($conn, "SELECT DISTINCT DepartureDes FROM flights");
            $landingOptions = mysqli_query($conn, "SELECT DISTINCT LandingDes FROM flights");
            ?>

            <form action="" method="GET" id="flight-search-form">
                <label for="departure">Departure Destination:</label>
                <select id="departure" name="departure" required>
                    <option value="">Select Departure</option>
                    <?php while ($row = mysqli_fetch_assoc($departureOptions)): ?>
                        <option value="<?php echo htmlspecialchars($row['DepartureDes']); ?>">
                            <?php echo htmlspecialchars($row['DepartureDes']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="landing">Landing Destination:</label>
                <select id="landing" name="landing" required>
                    <option value="">Select Landing</option>
                    <?php while ($row = mysqli_fetch_assoc($landingOptions)): ?>
                        <option value="<?php echo htmlspecialchars($row['LandingDes']); ?>">
                            <?php echo htmlspecialchars($row['LandingDes']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="flightdate">Departure Date:</label>
                <input type="date" id="flightdate" name="flightdate" required>
                
                <div id="return-date-container" style="display: none;">
                    <label for="returndate">Return Date:</label>
                    <input type="date" id="returndate" name="returndate">
                </div>
                
                <label for="travelers">Number of Travelers:</label>
                <input type="number" id="travelers" name="travelers" min="1" value="1" required>
                
                <label for="ticket-type">Ticket Type:</label>
                <select id="ticket-type" name="ticket_type" onchange="toggleReturnDate()" required>
                    <option value="one-way">One-Way</option>
                    <option value="two-way">Two-Way</option>
                </select>
                
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="product-list">
            <h2>Available Flights</h2>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['departure'], $_GET['landing'], $_GET['flightdate'], $_GET['ticket_type'])) {
                $departure = mysqli_real_escape_string($conn, $_GET['departure']);
                $landing = mysqli_real_escape_string($conn, $_GET['landing']);
                $flightdate = mysqli_real_escape_string($conn, $_GET['flightdate']);
                $ticketType = mysqli_real_escape_string($conn, $_GET['ticket_type']);
                $travelers = (int)$_GET['travelers'];

                $searchPerformed = true;

                // Fetch outbound flights
                $outboundQuery = "SELECT * FROM flights 
                                  WHERE DepartureDes = '$departure' 
                                  AND LandingDes = '$landing' 
                                  AND Flightdate = '$flightdate' 
                                  AND stock >= $travelers";
                $outboundFlights = mysqli_query($conn, $outboundQuery);

                // Fetch return flights only for two-way tickets
                $returnFlights = [];
                if ($ticketType === "two-way" && isset($_GET['returndate']) && !empty($_GET['returndate'])) {
                    $returndate = mysqli_real_escape_string($conn, $_GET['returndate']);
                    $returnQuery = "SELECT * FROM flights 
                                    WHERE DepartureDes = '$landing' 
                                    AND LandingDes = '$departure' 
                                    AND Flightdate = '$returndate' 
                                    AND stock >= $travelers";
                    $returnFlights = mysqli_query($conn, $returnQuery);
                }

                // Display outbound flights
                if ($outboundFlights && mysqli_num_rows($outboundFlights) > 0) {
                    while ($outboundFlight = mysqli_fetch_assoc($outboundFlights)) {
                        if ($ticketType === "two-way" && $returnFlights && mysqli_num_rows($returnFlights) > 0) {
                            mysqli_data_seek($returnFlights, 0); // Reset return flights pointer
                            while ($returnFlight = mysqli_fetch_assoc($returnFlights)) {
                                ?>
                                <div class="product-item">
                                    <h2>Outbound Flight</h2>
                                    <p>From: <?php echo $outboundFlight['DepartureDes']; ?></p>
                                    <p>To: <?php echo $outboundFlight['LandingDes']; ?></p>
                                    <p>Date: <?php echo $outboundFlight['Flightdate']; ?></p>
                                    <p>Departure Time: <?php echo $outboundFlight['DepartureTime']; ?></p>
                                    <p>Landing Time: <?php echo $outboundFlight['LandingTime']; ?></p>
                                    <p>Price: $<?php echo number_format($outboundFlight['price'], 2); ?></p>
                                    <p>Available Seats: <?php echo $outboundFlight['stock']; ?></p>

                                    <h3>Return Flight</h3>
                                    <p>From: <?php echo $returnFlight['DepartureDes']; ?></p>
                                    <p>To: <?php echo $returnFlight['LandingDes']; ?></p>
                                    <p>Date: <?php echo $returnFlight['Flightdate']; ?></p>
                                    <p>Departure Time: <?php echo $returnFlight['DepartureTime']; ?></p>
                                    <p>Landing Time: <?php echo $returnFlight['LandingTime']; ?></p>
                                    <p>Price: $<?php echo number_format($returnFlight['price'], 2); ?></p>
                                    <p>Available Seats: <?php echo $returnFlight['stock']; ?></p>
                                    <?php if (isset($_SESSION['userId'])): ?>
                                        <form action="booking.php" method="GET">
                                            <input type="hidden" name="flight_id" value="<?php echo $outboundFlight['fligtId']; ?>">
                                            <input type="hidden" name="return_flight_id" value="<?php echo $returnFlight['fligtId']; ?>">
                                            <input type="hidden" name="number_of_tickets" value="<?php echo $travelers; ?>">
                                            <input type="hidden" name="ticket_type" value="<?php echo htmlspecialchars($ticketType); ?>">
                                            <button type="submit">Book Now</button>
                                        </form>
                                    <?php else: ?>
                                        <p style="color: red;">You must <a href="sign.php">log in</a> to book a flight.</p>
                                    <?php endif; ?>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="product-item">
                                <h2>Outbound Flight</h2>
                                <p>From: <?php echo $outboundFlight['DepartureDes']; ?></p>
                                <p>To: <?php echo $outboundFlight['LandingDes']; ?></p>
                                <p>Date: <?php echo $outboundFlight['Flightdate']; ?></p>
                                <p>Departure Time: <?php echo $outboundFlight['DepartureTime']; ?></p>
                                <p>Landing Time: <?php echo $outboundFlight['LandingTime']; ?></p>
                                <p>Price: $<?php echo number_format($outboundFlight['price'], 2); ?></p>
                                <p>Available Seats: <?php echo $outboundFlight['stock']; ?></p>
                                <?php if (isset($_SESSION['userId'])): ?>
                                    <form action="booking.php" method="GET">
                                        <input type="hidden" name="flight_id" value="<?php echo $outboundFlight['fligtId']; ?>">
                                        <input type="hidden" name="number_of_tickets" value="<?php echo $travelers; ?>">
                                        <input type="hidden" name="ticket_type" value="one-way">
                                        <button type="submit">Book Now</button>
                                    </form>
                                <?php else: ?>
                                    <p style="color: red;">You must <a href="sign.php">log in</a> to book a flight.</p>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    }
                } else {
                    echo "<p>No flights found for your search criteria.</p>";
                }
            }
            ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</div>

<script>
function toggleReturnDate() {
    const ticketType = document.getElementById("ticket-type").value;
    const returnDateContainer = document.getElementById("return-date-container");

    if (ticketType === "two-way") {
        returnDateContainer.style.display = "block";
        document.getElementById("returndate").required = true;
    } else {
        returnDateContainer.style.display = "none";
        document.getElementById("returndate").required = false;
    }
}
</script>
