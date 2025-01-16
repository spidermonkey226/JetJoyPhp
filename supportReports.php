<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<?php
// Database Connection
$con = new mysqli("localhost", "root", "1234", "jetjoyuser");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Handle Support Answer Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateReport'])) {
    $reportID = intval($_POST['report-id']);
    $supportAnswer = $con->real_escape_string($_POST['support-answer']);
    $status = $con->real_escape_string($_POST['status']);

    // Update Report
    $stmt = $con->prepare("UPDATE ticket SET supportAnswer = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $supportAnswer, $status, $reportID);

    if ($stmt->execute()) {
        echo "<p>Report ID {$reportID} updated successfully.</p>";
    } else {
        echo "<p>Error updating report ID {$reportID}: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Display Feedback Reports
$result = $con->query("SELECT id, fname, lname, phone, email, feedback, supportAnswer, status FROM ticket");

if ($result->num_rows > 0) {
    echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Feedback</th>
        <th>Support Answer</th>
        <th>Status</th>
        <th>Action</th>
    </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['id']) . "</td>
            <td>" . htmlspecialchars($row['fname']) . "</td>
            <td>" . htmlspecialchars($row['lname']) . "</td>
            <td>" . htmlspecialchars($row['phone']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['feedback']) . "</td>
            <td>" . htmlspecialchars($row['supportAnswer']) . "</td>
            <td>" . htmlspecialchars($row['status']) . "</td>
            <td>
                <form method='POST' action='' class='action-form'>
                    <input type='hidden' name='report-id' value='" . htmlspecialchars($row['id']) . "'>
                    <input type='text' name='support-answer' placeholder='Write Answer' value='" . htmlspecialchars($row['supportAnswer']) . "' required>
                    <select name='status' required>
                        <option value='' disabled>Select Status</option>
                        <option value='Pending'" . ($row['status'] === 'Pending' ? ' selected' : '') . ">Pending</option>
                        <option value='Resolved'" . ($row['status'] === 'Resolved' ? ' selected' : '') . ">Resolved</option>
                        <option value='Closed'" . ($row['status'] === 'Closed' ? ' selected' : '') . ">Closed</option>
                    </select>
                    <button type='submit' name='updateReport'>Submit</button>
                </form>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "No reports found.";
}

$con->close();
?>
<link rel="stylesheet" href="styleSupport.css">

<?php include 'footer.php'; ?>
