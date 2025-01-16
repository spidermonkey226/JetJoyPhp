<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<?php 
include 'db_connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST'&& isset($_POST['addButton'])) {
    $flight_name = $_POST['flight-name'];
    $flight_price = $_POST['flight-price'];
    $flight_stock = $_POST['flight-stock'];
    $conn = OpenCon();

    $stmt = $conn->prepare("INSERT INTO flights (flghtName,price,stock) VALUES (?, ?, ?)");
    $stmt->bind_param("sss",  $flight_name, $flight_price,$flight_stock);

    if ($stmt->execute()) {
        header('Location: addflight.php');
        exit;
    }
    else{
        $_SESSION['error_flightadd'] = "Flight couldn't be added.";
        //echo "Please provide both email and password.";
    }
    CloseCon($conn);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitEdit'])) {
    $newStock = $_POST['stock-edit'];
    $flightId = $_POST['flight-id'];
    $conn = OpenCon();

    $stmt = $conn->prepare("UPDATE flights SET stock = ? WHERE fligtId = ?");
    $stmt->bind_param("ii", $newStock, $flightId);

    if (!$stmt->execute()) {
        echo "Error updating stock.";
    }

    CloseCon($conn);
}
?>
<div class="product-list">
    <link rel="stylesheet" href="styleAddFlight.css">

    <?php 
    $conn = OpenCon();
     $result = mysqli_query($conn, "SELECT * FROM flights");
     while ($user = mysqli_fetch_assoc($result)): ?>
        <div class="product-item">
            <h2><?php echo $user['flghtName']; ?></h2>
            <p>Price: $<?php echo number_format($user['price'], 2); ?></p>
            <p>Stock Available: <?php echo $user['stock']; ?></p>
            <!-- Form to edit stock -->
            <form action="addflight.php" method="POST">
                <input type="hidden" name="flight-id" value="<?php echo $user['fligtId']; ?>">
                <input type="number" id="stock-edit" name="stock-edit" placeholder="Edit Stock">
                <button id="submitEdit" name="submitEdit">Submit</button>
            </form>
            
            <form action="cart.php" method="POST" onsubmit="return false;">
                <label for="quantity-<?php echo $user['fligtId']; ?>">Select Quantity:</label>
                <input type="number" id="quantity-<?php echo $user['fligtId']; ?>" name="quantity" min="1" max="<?php echo $user['stock']; ?>" value="1">
                <input type="hidden" name="product_id" value="<?php echo $user['fligtId']; ?>">
            </form>
        </div>
    <?php
    endwhile; 
    CloseCon($conn);?>
</div>
<div class="form-container addflight">
        <form method="POST" action="addflight.php">
            <h1>Add flight</h1>
            <input type="text" id="flight-name" name="flight-name" placeholder="Flight Name">
            <input type="number" id="flight-price" name="flight-price" placeholder="Flight Price">
            <input type="number" id="flight-stock" name="flight-stock" placeholder="Flight Stock">

            <?php if (!empty($error_add_message)): ?>
                <p class="error_sign"><?php echo htmlspecialchars($error_add_message); ?></p>
            <?php endif; ?>
            <!-- Sign In Button -->
            <button id="addButton" name="addButton">Add</button>
        </form>
</div>

<?php include 'footer.php'; ?>