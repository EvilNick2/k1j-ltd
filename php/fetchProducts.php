<?php
header('Content-Type: application/json');
include 'config.php';

// Fetch products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = array();
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Convert stocked field to boolean
        $row['stocked'] = (bool)$row['stocked'];
        $products[] = $row;
    }
} else {
    echo json_encode(array("message" => "No products found"));
    exit();
}

echo json_encode($products);

$conn->close();
?>