<?php

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

// Include the database connection file
include('DB_Connection.php');

// Set the response header to JSON
header('Content-Type: application/json');

// Handle different HTTP methods

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET operation: Retrieve order data
    $result = mysqli_query($conn, "SELECT * FROM orders");
    
    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    echo json_encode($orders);
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // POST operation: Add a new order

    $data = json_decode(file_get_contents("php://input"), true);

    $userId = mysqli_real_escape_string($conn, $data['user_id']);
    $productId = mysqli_real_escape_string($conn, $data['product_id']);
    $quantity = mysqli_real_escape_string($conn, $data['quantity']);
    $totalAmount = mysqli_real_escape_string($conn, $data['total_amount']);

    $query = "INSERT INTO orders (user_id, product_id, quantity, total_amount) 
              VALUES ('$userId', '$productId', '$quantity', '$totalAmount')";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(array('message' => 'Order placed successfully'));
    } else {
        echo json_encode(array('error' => 'Error placing order'));
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    // PUT operation: Update an existing order

    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = mysqli_real_escape_string($conn, $data['id']);
    $newUserId = mysqli_real_escape_string($conn, $data['user_id']);
    $newProductId = mysqli_real_escape_string($conn, $data['product_id']);
    $newQuantity = mysqli_real_escape_string($conn, $data['quantity']);
    $newTotalAmount = mysqli_real_escape_string($conn, $data['total_amount']);
    
    $query = "UPDATE orders SET 
              user_id='$newUserId', 
              product_id='$newProductId', 
              quantity='$newQuantity', 
              total_amount='$newTotalAmount' 
              WHERE id=$id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Order updated successfully'));
    } else {
        echo json_encode(array('error' => 'Error updating order'));
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    // DELETE operation: Cancel an order
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = mysqli_real_escape_string($conn, $data['id']);
    
    $query = "DELETE FROM orders WHERE id=$id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Order cancelled successfully'));
    } else {
        echo json_encode(array('error' => 'Error cancelling order'));
    }
}

// Close the database connection
$conn->close();

?>
