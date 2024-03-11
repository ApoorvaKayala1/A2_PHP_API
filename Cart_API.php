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
    
    // GET operation: Retrieve cart data with additional product information

    $result = mysqli_query($conn, "SELECT cart.id, cart.user_id, cart.product_id, cart.quantity, products.image, products.name, products.pricing 
                                    FROM cart 
                                    INNER JOIN products ON cart.product_id = products.id");

    $cartItems = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $cartItems[] = $row;
    }
    
    echo json_encode($cartItems);
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // POST operation: Add a new item to the cart
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    $userId = mysqli_real_escape_string($conn, $data['user_id']);
    $productId = mysqli_real_escape_string($conn, $data['product_id']);
    $quantity = mysqli_real_escape_string($conn, $data['quantity']);
    
    $query = "INSERT INTO cart (user_id, product_id, quantity) 
              VALUES ('$userId', '$productId', '$quantity')";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Item added to the cart successfully'));
    } else {
        echo json_encode(array('error' => 'Error adding item to the cart'));
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    
    // PUT operation: Update an existing item in the cart
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = mysqli_real_escape_string($conn, $data['id']);
    $newQuantity = mysqli_real_escape_string($conn, $data['quantity']);
    
    $query = "UPDATE cart SET quantity='$newQuantity' WHERE id=$id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Item in the cart updated successfully'));
    } else {
        echo json_encode(array('error' => 'Error updating item in the cart'));
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    // DELETE operation: Remove an item from the cart
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = mysqli_real_escape_string($conn, $data['id']);
    
    $query = "DELETE FROM cart WHERE id=$id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Item removed from the cart successfully'));
    } else {
        echo json_encode(array('error' => 'Error removing item from the cart'));
    }
}

// Close the database connection
$conn->close();

?>
