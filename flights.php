<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 include 'header.php'; ?>
<?php include 'navbar.php'; 


$ticketType = ""; ?>
<div class="page-container">
    <div class="content-wrap">
        <div class="search-section">
            <link rel="stylesheet" href="styleflight.css">
            <h2>Search Flights</h2>
            <form action="" method="GET" id="flight-search-form">
                <label for="departure">Departure Destination:</label>
                <input type="text" id="departure" name="departure" placeholder="Enter departure location">
                
                <label for="landing">Landing Destination:</label>
                <input type="text" id="landing" name="landing" placeholder="Enter landing location">
                
                <label for="flightdate">Departure Date:</label>
                <input type="date" id="flightdate" name="flightdate">
                
                <div id="return-date-container" style="display: none;">
                    <label for="returndate">Return Date:</label>
                    <input type="date" id="returndate" name="returndate">
                </div>
                
                <label for="travelers">Number of Travelers:</label>
                <input type="number" id="travelers" name="travelers" min="1" value="1">
                
                <label for="ticket-type">Ticket Type:</label>
                <select id="ticket-type" name="ticket_type" onchange="toggleReturnDate()">
                    <option value="one-way">One-Way</option>
                    <option value="two-way">Two-Way</option>
                </select>
                
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="product-list">
            <h2>Available Flights</h2>

            <?php
            include 'db_connection.php';
            $conn = OpenCon();
            
            $outboundFlights = [];
            $returnFlights = [];
            $searchPerformed = false;

            if ($_SERVER["REQUEST_METHOD"] == "GET" && (isset($_GET['departure']) || isset($_GET['landing']) || isset($_GET['flightdate']) || isset($_GET['ticket_type']))) {
                $departure = !empty($_GET['departure']) ? mysqli_real_escape_string($conn, $_GET['departure']) : '';
                $landing = !empty($_GET['landing']) ? mysqli_real_escape_string($conn, $_GET['landing']) : '';
                $flightdate = !empty($_GET['flightdate']) ? mysqli_real_escape_string($conn, $_GET['flightdate']) : '';
                $ticketType = isset($_GET['ticket_type']) ? mysqli_real_escape_string($conn, $_GET['ticket_type']) : '';
                $travelers = isset($_GET['travelers']) ? (int)$_GET['travelers'] : 1;
                $searchPerformed = true;

                $outboundQuery = "SELECT * FROM flights WHERE stock >= $travelers";
                
                if ($departure) {
                    $outboundQuery .= " AND DepartureDes LIKE '%$departure%'";
                }
                if ($landing) {
                    $outboundQuery .= " AND LandingDes LIKE '%$landing%'";
                }
                if ($flightdate) {
                    $outboundQuery .= " AND Flightdate = '$flightdate'";
                }

                $outboundFlights = mysqli_query($conn, $outboundQuery);

                if ($ticketType === "two-way" && isset($_GET['returndate'])) {
                    $returndate = mysqli_real_escape_string($conn, $_GET['returndate']);
                    $returnQuery = "SELECT * FROM flights 
                                    WHERE DepartureDes LIKE '%$landing%' 
                                    AND LandingDes LIKE '%$departure%' 
                                    AND Flightdate = '$returndate' 
                                    AND stock >= $travelers";
                    $returnFlights = mysqli_query($conn, $returnQuery);
                }
            } else {
                // Default case: Show all flights
                $outboundFlights = mysqli_query($conn, "SELECT * FROM flights");
            }

            if ($outboundFlights && mysqli_num_rows($outboundFlights) > 0): ?>
                <h3>Outbound Flights</h3>
                <?php while ($flight = mysqli_fetch_assoc($outboundFlights)): ?>
                    <div class="product-item">
                        <h2><?php echo $flight['flghtName']; ?></h2>
                        <p>From: <?php echo $flight['DepartureDes']; ?></p>
                        <p>To: <?php echo $flight['LandingDes']; ?></p>
                        <p>Date: <?php echo $flight['Flightdate']; ?></p>
                        <p>Departure Time: <?php echo $flight['DepartureTime']; ?></p>
                        <p>Landing Time: <?php echo $flight['LandingTime']; ?></p>
                        <p>Price: $<?php echo number_format($flight['price'], 2); ?></p>
                        <p>Available Seats: <?php echo $flight['stock']; ?></p>
                        <?php if (isset($_SESSION['userId'])): ?>
                            <!-- Show Book Now button if logged in -->
                            <form action="booking.php" method="GET">
                                <input type="hidden" name="flight_id" value="<?php echo $flight['fligtId']; ?>">
                                <input type="hidden" name="number_of_tickets" value="<?php echo $travelers; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $_SESSION['userId']; ?>">
                                <button type="submit">Book Now</button>
                            </form>
                        <?php else: ?>
                            <!-- Show Login Required message -->
                            <p><a href="sign.php" style="color: blue;">Log in</a> to book this flight.</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile;
            else: ?>
                <p>No flights found<?php echo $searchPerformed ? " for your search criteria" : ""; ?>.</p>
            <?php endif;

            if ($ticketType === "two-way" && $returnFlights && mysqli_num_rows($returnFlights) > 0): ?>
                <h3>Return Flights</h3>
                <?php while ($flight = mysqli_fetch_assoc($returnFlights)): ?>
                    <div class="product-item">
                        <h2><?php echo $flight['flghtName']; ?></h2>
                        <p>From: <?php echo $flight['DepartureDes']; ?></p>
                        <p>To: <?php echo $flight['LandingDes']; ?></p>
                        <p>Date: <?php echo $flight['Flightdate']; ?></p>
                        <p>Departure Time: <?php echo $flight['DepartureTime']; ?></p>
                        <p>Landing Time: <?php echo $flight['LandingTime']; ?></p>
                        <p>Price: $<?php echo number_format($flight['price'], 2); ?></p>
                        <p>Available Seats: <?php echo $flight['stock']; ?></p>
                        
                        <?php if (isset($_SESSION['userId'])): ?>
                            <!-- Show Book Now button if logged in -->
                            <form action="booking.php" method="GET">
                                <input type="hidden" name="flight_id" value="<?php echo $flight['fligtId']; ?>">
                                <input type="hidden" name="number_of_tickets" value="<?php echo $travelers; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $_SESSION['userId']; ?>">
                                <button type="submit">Book Now</button>
                            </form>
                        <?php else: ?>
                            <!-- Show Login Required message -->
                            <p><a href="sign.php" style="color: blue;">Log in</a> to book this flight.</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile;
            elseif ($ticketType === "two-way"): ?>
                <p>No return flights found for your search criteria.</p>
            <?php endif;

            CloseCon($conn); ?>
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
