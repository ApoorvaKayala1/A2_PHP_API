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
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

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

    if (isset($_GET['id'])) {

        // GET operation: Retrieve data for a specific product
        $productId = intval($_GET['id']);
        $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $productId");
        $product = mysqli_fetch_assoc($result);

        if ($product) {
            echo json_encode($product);
        } else {
            echo json_encode(array('error' => 'Product not found'));
        }
    } 
    
    else {

        // GET operation: Retrieve data for all products
        $result = mysqli_query($conn, "SELECT * FROM products");
        $products = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        echo json_encode($products);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // POST operation: Add a new product

    $data = json_decode(file_get_contents("php://input"), true);
    
    $description = mysqli_real_escape_string($conn, $data['description']);
    $image = mysqli_real_escape_string($conn, $data['image']);
    $pricing = mysqli_real_escape_string($conn, $data['pricing']);
    $shippingCost = mysqli_real_escape_string($conn, $data['shipping_cost']);
    
    $query = "INSERT INTO products (description, image, pricing, shipping_cost) 
              VALUES ('$description', '$image', '$pricing', '$shippingCost')";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Product added successfully'));
    } else {
        echo json_encode(array('error' => 'Error adding product'));
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    // PUT operation: Update an existing product

    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = mysqli_real_escape_string($conn, $data['id']);
    $newDescription = mysqli_real_escape_string($conn, $data['description']);
    $newImage = mysqli_real_escape_string($conn, $data['image']);
    $newPricing = mysqli_real_escape_string($conn, $data['pricing']);
    $newShippingCost = mysqli_real_escape_string($conn, $data['shipping_cost']);
    
    $query = "UPDATE products SET 
              description='$newDescription', 
              image='$newImage', 
              pricing='$newPricing', 
              shipping_cost='$newShippingCost' 
              WHERE id=$id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Product updated successfully'));
    } else {
        echo json_encode(array('error' => 'Error updating product'));
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    // DELETE operation: Remove a product
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = mysqli_real_escape_string($conn, $data['id']);
    
    $query = "DELETE FROM products WHERE id=$id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Product removed successfully'));
    } else {
        echo json_encode(array('error' => 'Error removing product'));
    }
}

// Close the database connection
$conn->close();

?>
