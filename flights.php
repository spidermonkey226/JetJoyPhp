<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<?php
include 'db_connection.php';
/*
$products = [
    [
        'product_id' => 1,
        'product_name' => 'Athens Tour',
        'price' => 200.00,
        'stock_quantity' => 20
    ],
    [
        'product_id' => 2,
        'product_name' => 'Dubai Tour',
        'price' => 350.00,
        'stock_quantity' => 35
    ],
    [
        'product_id' => 3,
        'product_name' => 'Romania Tour',
        'price' => 250.00,
        'stock_quantity' => 15
    ],
    [
        'product_id' => 4,
        'product_name' => 'Hamburg Tour',
        'price' => 300.00,
        'stock_quantity' => 35
    ]
];*/

?>

<div class="product-list">
    <link rel="stylesheet" href="styleflight.css">

    <?php 
    $conn = OpenCon();
     $result = mysqli_query($conn, "SELECT * FROM flights");
     while ($user = mysqli_fetch_assoc($result)): ?>
        <div class="product-item">
            <h2><?php echo $user['flghtName']; ?></h2>
            <p>Price: $<?php echo number_format($user['price'], 2); ?></p>
            <p>Stock Available: <?php echo $user['stock']; ?></p>
            
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

<?php include 'footer.php'; ?>