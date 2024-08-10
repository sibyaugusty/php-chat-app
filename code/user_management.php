<?php
require_once '../code/connection.php';

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the action parameter
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Use switch case to handle different actions
    switch ($action) {
        case 'signUp':
            addNewUser($pdo);
            break;
        case 'signIn':
            loginUser($pdo);
            break;
        default:
            echo 'Unknown action.';
            break;
    }
} else {
    echo 'Invalid request.';
}

function addNewUser($pdo)
{
    // Parse the serialized form data
    parse_str($_POST['formData'], $formData);

    $firstName = isset($formData['firstname']) ? $formData['firstname'] : '';
    $lastName = isset($formData['lastname']) ? $formData['lastname'] : '';
    $mobileNumber = isset($formData['mobileNumber']) ? $formData['mobileNumber'] : '';
    $email = isset($formData['email']) ? $formData['email'] : '';
    $password = isset($formData['password']) ? $formData['password'] : '';
    $confirmPassword = isset($formData['confirmPassword']) ? $formData['confirmPassword'] : '';

    // Initialize response array
    $response = [];

    $userName = $firstName . ' ' . $lastName;

    // Validate password match
    if ($password !== $confirmPassword) {
        echo 'Passwords do not match.';
        return;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID); // or PASSWORD_BCRYPT

    try {
        // Prepare the SQL INSERT query
        $sql = "INSERT INTO `users` (`user_name`, `first_name`, `last_name`, `mobile_number`, `email`, `password`)
                VALUES (:user_name, :first_name, :last_name, :mobile_number, :email, :password)";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':user_name', $userName);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':mobile_number', $mobileNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        // Execute the statement
        $stmt->execute();

        $response['msg'] = 'User registered successfully.';
        $response['success'] = true;
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        $response['msg'] = 'Something Went Wrong.. Please Try Again Later.';
        $response['success'] = true;
    }
    echo json_encode($response);
}


function loginUser($pdo)
{

    // Parse the serialized form data
    parse_str($_POST['formData'], $formData);

    $email = isset($formData['email']) ? $formData['email'] : '';
    $password = isset($formData['password']) ? $formData['password'] : '';

    // Initialize response array
    $response = ['success' => false, 'msg' => ''];

    try {
        // Prepare the SQL SELECT query
        $sql = "SELECT * FROM `users` WHERE `email` = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $response['success'] = true;
            $response['msg'] = 'Login successful!';
            // Optionally, you can set session variables or other login management here
        } else {
            $response['msg'] = 'Invalid email or password.';
        }
    } catch (PDOException $e) {
        $response['msg'] = 'Error: ' . $e->getMessage();
    }

    echo json_encode($response);
}
