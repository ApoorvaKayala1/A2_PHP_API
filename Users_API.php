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

    // GET operation: Retrieve user data

    if (isset($_GET['id'])) {
        // Fetch user data based on user ID
        $userId = mysqli_real_escape_string($conn, $_GET['id']);
        $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $userId");

        $userData = mysqli_fetch_assoc($result);
        echo json_encode($userData);
    } else {
        // Fetch all users
        $result = mysqli_query($conn, "SELECT * FROM users");

        $users = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        echo json_encode($users);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // POST operation: Add a new user

    $data = json_decode(file_get_contents("php://input"), true);

    $email = mysqli_real_escape_string($conn, $data['email']);
    $password = mysqli_real_escape_string($conn, $data['password']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $purchaseHistory = mysqli_real_escape_string($conn, $data['purchase_history']);
    $shippingAddress = mysqli_real_escape_string($conn, $data['shipping_address']);
    $contact = mysqli_real_escape_string($conn, $data['contact']);
    $city = mysqli_real_escape_string($conn, $data['city']);
    $province = mysqli_real_escape_string($conn, $data['province']);
    $country = mysqli_real_escape_string($conn, $data['country']);
    $zipCode = mysqli_real_escape_string($conn, $data['zip_code']);

    $query = "INSERT INTO users (email, password, username, purchase_history, shipping_address, contact, city, province, country, zip_code) 
              VALUES ('$email', '$password', '$username', '$purchaseHistory', '$shippingAddress', '$contact', '$city', '$province', '$country', '$zipCode')";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(array('message' => 'User added successfully'));
    } else {
        echo json_encode(array('error' => 'Error adding user'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    // PUT operation: Update an existing user

    $data = json_decode(file_get_contents("php://input"), true);

    $id = mysqli_real_escape_string($conn, $data['id']);
    $newEmail = mysqli_real_escape_string($conn, $data['email']);
    $newPassword = mysqli_real_escape_string($conn, $data['password']);
    $newUsername = mysqli_real_escape_string($conn, $data['username']);
    $newPurchaseHistory = mysqli_real_escape_string($conn, $data['purchase_history']);
    $newShippingAddress = mysqli_real_escape_string($conn, $data['shipping_address']);
    $newContact = mysqli_real_escape_string($conn, $data['contact']);
    $newCity = mysqli_real_escape_string($conn, $data['city']);
    $newProvince = mysqli_real_escape_string($conn, $data['province']);
    $newCountry = mysqli_real_escape_string($conn, $data['country']);
    $newZipCode = mysqli_real_escape_string($conn, $data['zip_code']);

    $query = "UPDATE users SET 
              email='$newEmail', 
              password='$newPassword', 
              username='$newUsername', 
              purchase_history='$newPurchaseHistory', 
              shipping_address='$newShippingAddress', 
              contact='$newContact', 
              city='$newCity', 
              province='$newProvince', 
              country='$newCountry', 
              zip_code='$newZipCode' 
              WHERE id=$id";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(array('message' => 'User updated successfully'));
    } else {
        echo json_encode(array('error' => 'Error updating user'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // DELETE operation: Delete a user

    $data = json_decode(file_get_contents("php://input"), true);

    $id = mysqli_real_escape_string($conn, $data['id']);

    $query = "DELETE FROM users WHERE id=$id";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(array('message' => 'User deleted successfully'));
    } else {
        echo json_encode(array('error' => 'Error deleting user'));
    }
}

// Close the database connection
$conn->close();

?>
