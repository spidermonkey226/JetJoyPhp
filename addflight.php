<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="styleAddFlight.css">
<?php 
include 'db_connection.php';
// Add new flight
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addButton'])) {
    $flight_name = $_POST['flight-name'];
    $departure_des = $_POST['departure-des'];
    $landing_des = $_POST['landing-des'];
    $departure_time = $_POST['departure-time'];
    $landing_time = $_POST['landing-time'];
    $flight_date = $_POST['flight-date'];
    $flight_price = $_POST['flight-price'];
    $flight_stock = $_POST['flight-stock'];

    $conn = OpenCon();

    $stmt = $conn->prepare("INSERT INTO flights (flghtName, DepartureDes, LandingDes, DepartureTime, LandingTime, Flightdate, price, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdi", $flight_name, $departure_des, $landing_des, $departure_time, $landing_time, $flight_date, $flight_price, $flight_stock);

    if ($stmt->execute()) {
        header('Location: addflight.php');
        exit;
    } else {
        $_SESSION['error_flightadd'] = "Flight couldn't be added.";
    }
    CloseCon($conn);
}
// Edit flight details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitEdit'])) {
    $flight_id = $_POST['flight-id'];
    $flight_name = $_POST['flight-name-edit'];
    $departure_des = $_POST['departure-des-edit'];
    $landing_des = $_POST['landing-des-edit'];
    $departure_time = $_POST['departure-time-edit'];
    $landing_time = $_POST['landing-time-edit'];
    $flight_date = $_POST['flight-date-edit'];
    $flight_price = $_POST['flight-price-edit'];
    $flight_stock = $_POST['flight-stock-edit'];

    $conn = OpenCon();

    $stmt = $conn->prepare("UPDATE flights SET flghtName = ?, DepartureDes = ?, LandingDes = ?, DepartureTime = ?, LandingTime = ?, Flightdate = ?, price = ?, stock = ? WHERE fligtId = ?");
    $stmt->bind_param("ssssssdii", $flight_name, $departure_des, $landing_des, $departure_time, $landing_time, $flight_date, $flight_price, $flight_stock, $flight_id);

    if (!$stmt->execute()) {
        echo "Error updating flight details.";
    }

    CloseCon($conn);
}
?>
<div class="page-container">
    <div class="content-wrap">
          <!-- Search Section -->
        <div class="search-section" id="search-flight">
            <form method="GET" action="addflight.php">
                <label for="flight-search">Search by Flight Name:</label>
                <select name="search" id="flight-search">
                    <option value="">Select a Flight</option>
                    <?php 
                    $conn = OpenCon();
                    $query = "SELECT DISTINCT flghtName FROM flights";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)): ?>
                        <option value="<?php echo htmlspecialchars($row['flghtName']); ?>" 
                            <?php if (isset($_GET['search']) && $_GET['search'] === $row['flghtName']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($row['flghtName']); ?>
                        </option>
                    <?php endwhile; 
                    CloseCon($conn); ?>
                </select>
                <button type="submit">Search</button>
            </form>
        </div>

         <!-- Jump to Add Flight Section -->
         <div class="jump-to-add">
            <a href="#add-flight">Go to Add Flight</a>
        </div>

         <!-- Flight List -->
        <div class="product-list">
            <h1>All Flights</h1>
            <?php 
            $conn = OpenCon();
            $whereClause = "";
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = mysqli_real_escape_string($conn, $_GET['search']);
                $whereClause = "WHERE flghtName = '$search'"; // Search by exact flight name
            }

            $query = "SELECT * FROM flights $whereClause";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0):
                while ($flight = mysqli_fetch_assoc($result)): ?>
                    <div class="product-item">
                        <h2><?php echo $flight['flghtName']; ?></h2>
                        <p>From: <?php echo $flight['DepartureDes']; ?></p>
                        <p>To: <?php echo $flight['LandingDes']; ?></p>
                        <p>Departure Time: <?php echo $flight['DepartureTime']; ?></p>
                        <p>Landing Time: <?php echo $flight['LandingTime']; ?></p>
                        <p>Date: <?php echo $flight['Flightdate']; ?></p>
                        <p>Price: $<?php echo number_format($flight['price'], 2); ?></p>
                        <p>Stock: <?php echo $flight['stock']; ?></p>
                        
                        <!-- Form to edit flight details -->
                        <form action="addflight.php" method="POST">
                            <input type="hidden" name="flight-id" value="<?php echo $flight['fligtId']; ?>">

                            <label>Flight Name:</label>
                            <input type="text" name="flight-name-edit" value="<?php echo $flight['flghtName']; ?>" required>

                            <label>Departure:</label>
                            <input type="text" name="departure-des-edit" value="<?php echo $flight['DepartureDes']; ?>" required>

                            <label>Landing:</label>
                            <input type="text" name="landing-des-edit" value="<?php echo $flight['LandingDes']; ?>" required>

                            <label>Departure Time:</label>
                            <input type="datetime-local" name="departure-time-edit" value="<?php echo date('Y-m-d\TH:i', strtotime($flight['DepartureTime'])); ?>" required>

                            <label>Landing Time:</label>
                            <input type="datetime-local" name="landing-time-edit" value="<?php echo date('Y-m-d\TH:i', strtotime($flight['LandingTime'])); ?>" required>

                            <label>Flight Date:</label>
                            <input type="date" name="flight-date-edit" value="<?php echo $flight['Flightdate']; ?>" required>

                            <label>Price:</label>
                            <input type="number" step="0.01" name="flight-price-edit" value="<?php echo $flight['price']; ?>" required>

                            <label>Stock:</label>
                            <input type="number" name="flight-stock-edit" value="<?php echo $flight['stock']; ?>" required>

                            <button type="submit" name="submitEdit">Save Changes</button>
                        </form>
                    </div>
                <?php endwhile; 
            else: ?>
                <p>No flights found matching your search criteria.</p>
            <?php 
            endif;
            CloseCon($conn); ?>
        </div>

        
        <!-- Add Flight Form -->
        <div class="form-container" id="add-flight">
            <form method="POST" action="addflight.php">
            <h1>Add Flight</h1>
            <label>Flight Name:</label>
                <input type="text" name="flight-name" placeholder="Flight Name" required>

                <label>Departure:</label>
                <input type="text" name="departure-des" placeholder="Departure Destination" required>

                <label>Landing:</label>
                <input type="text" name="landing-des" placeholder="Landing Destination" required>

                <label>Departure Time:</label>
                <input type="datetime-local" name="departure-time" required>

                <label>Landing Time:</label>
                <input type="datetime-local" name="landing-time" required>

                <label>Flight Date:</label>
                <input type="date" name="flight-date" required>

                <label>Price:</label>
                <input type="number" step="0.01" name="flight-price" placeholder="Price" required>

                <label>Stock:</label>
                <input type="number" name="flight-stock" placeholder="Stock" required>

               
                <?php if (!empty($error_add_message)): ?>
                    <p class="error_sign"><?php echo htmlspecialchars($error_add_message); ?></p>
                <?php endif; ?>
              <!-- Sign In Button -->
              <button type="submit" name="addButton">Add Flight</button>
            </form>
        </div>
         <!-- Jump to Search Flight Section -->
        <div class="jump-to-add">
            <a href="#search-flight">Go to Top</a>
        </div>
    </div>
   
    
</div>

<?php include 'footer.php'; ?>