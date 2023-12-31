<?php
// Database connection parameters
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "school";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$phone = $_POST['phone'];
$pupil_id = $_POST['pupil_id'];

// Check the current number of parents assigned to the pupil
$parentCountQuery = $conn->prepare("SELECT COUNT(*) as parent_count FROM parents WHERE pupil_id = ?");
$parentCountQuery->bind_param("i", $pupil_id);
$parentCountQuery->execute();
$parentCountResult = $parentCountQuery->get_result();
$parentCountRow = $parentCountResult->fetch_assoc();
$currentParentCount = $parentCountRow['parent_count'];

// Check if the pupil can have more parents assigned
if ($currentParentCount < 2) {
    // Insert data into the "parents" table
    $sql = "INSERT INTO parents (fname, lname, phone, pupil_id) VALUES ('$fname', '$lname', '$phone', '$pupil_id')";
    
    // Check if the insertion was successful
    if ($conn->query($sql) === TRUE) {
        // Increment the parent_count in the "pupils" table
        $conn->query("UPDATE pupils SET parent_count = parent_count + 1 WHERE pupil_id = '$pupil_id'");
        echo "Data inserted successfully";
        echo '<br><input type="reset" id="return" value="Return" onclick="location.href=\'home.html\'" /> <br>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        echo '<br><input type="reset" id="return" value="Return" onclick="location.href=\'home.html\'" /> <br>';
    }
} else {
    // If the pupil already has two parents, do not assign more
    echo "Cannot assign more than two parents to the pupil.";
    echo '<br><input type="reset" id="return" value="Return" onclick="location.href=\'home.html\'" /> <br>';
}

// Close prepared statements and connection
$parentCountQuery->close();
$conn->close();
?>
