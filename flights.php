<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<?php $ticketType=""?>
<div class="page-container">
    <div class="content-wrap">
        <div class="search-section">
            <link rel="stylesheet" href="styleflight.css">
            <h2>Search Flights</h2>
            <form action="" method="GET" id="flight-search-form">
                <label for="departure">Departure Destination:</label>
                <input type="text" id="departure" name="departure" placeholder="Enter departure location" required>
                
                <label for="landing">Landing Destination:</label>
                <input type="text" id="landing" name="landing" placeholder="Enter landing location" required>
                
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
            include 'db_connection.php';
            $conn = OpenCon();
            
            $outboundFlights = [];
            $returnFlights = [];

            if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['departure'], $_GET['landing'], $_GET['flightdate'], $_GET['ticket_type'], $_GET['travelers'])) {
                $departure = mysqli_real_escape_string($conn, $_GET['departure']);
                $landing = mysqli_real_escape_string($conn, $_GET['landing']);
                $flightdate = mysqli_real_escape_string($conn, $_GET['flightdate']);
                $ticketType = mysqli_real_escape_string($conn, $_GET['ticket_type']);
                $travelers = (int) $_GET['travelers'];

                $outboundQuery = "SELECT * FROM flights 
                                  WHERE DepartureDes LIKE '%$departure%' 
                                  AND LandingDes LIKE '%$landing%' 
                                  AND Flightdate = '$flightdate' 
                                  AND stock >= $travelers";
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
                        
                        <form action="booking.php" method="GET">
                            <input type="hidden" name="flight_id" value="<?php echo $flight['fligtId']; ?>">
                            <input type="hidden" name="number_of_tickets" value="<?php echo $travelers; ?>">
                            <button type="submit">Book Now</button>
                        </form>
                    </div>
                <?php endwhile;
            else: ?>
                <p>No outbound flights found for your search criteria.</p>
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
                        
                        <form action="book.php" method="POST">
                            <label for="quantity-<?php echo $flight['fligtId']; ?>">Number of Travelers:</label>
                            <input type="number" id="quantity-<?php echo $flight['fligtId']; ?>" name="quantity" min="1" max="<?php echo $flight['stock']; ?>" value="1" required>
                            <input type="hidden" name="flight_id" value="<?php echo $flight['fligtId']; ?>">
                            <button type="submit">Book Now</button>
                        </form>
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
