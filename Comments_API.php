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

    // GET operation: Retrieve comments data

    $result = mysqli_query($conn, "SELECT * FROM comments");
    
    $comments = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    
    echo json_encode($comments);
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // POST operation: Add a new comment

    $data = json_decode(file_get_contents("php://input"), true);
    
    $productId = mysqli_real_escape_string($conn, $data['product_id']);
    $userId = mysqli_real_escape_string($conn, $data['user_id']);
    $rating = mysqli_real_escape_string($conn, $data['rating']);
    $image = mysqli_real_escape_string($conn, $data['image']);
    $text = mysqli_real_escape_string($conn, $data['text']);
    
    $query = "INSERT INTO comments (product_id, user_id, rating, image, text) 
              VALUES ('$productId', '$userId', '$rating', '$image', '$text')";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Comment added successfully'));
    } else {
        echo json_encode(array('error' => 'Error adding comment'));
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    // PUT operation: Update an existing comment

    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = mysqli_real_escape_string($conn, $data['id']);
    $newProductId = mysqli_real_escape_string($conn, $data['product_id']);
    $newUserId = mysqli_real_escape_string($conn, $data['user_id']);
    $newRating = mysqli_real_escape_string($conn, $data['rating']);
    $newImage = mysqli_real_escape_string($conn, $data['image']);
    $newText = mysqli_real_escape_string($conn, $data['text']);
    
    $query = "UPDATE comments SET 
              product_id='$newProductId', 
              user_id='$newUserId', 
              rating='$newRating', 
              image='$newImage', 
              text='$newText' 
              WHERE id=$id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Comment updated successfully'));
    } else {
        echo json_encode(array('error' => 'Error updating comment'));
    }
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // DELETE operation: Delete a comment

    $data = json_decode(file_get_contents("php://input"), true);
    
    $id = mysqli_real_escape_string($conn, $data['id']);
    
    $query = "DELETE FROM comments WHERE id=$id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(array('message' => 'Comment deleted successfully'));
    } else {
        echo json_encode(array('error' => 'Error deleting comment'));
    }
}

// Close the database connection
$conn->close();

?>
