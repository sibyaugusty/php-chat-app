<?php
// Start the session
session_start();

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
        case 'fetchUsers':
            fetchUsers($pdo);
            break;
        case 'logout':
            logoutUser($pdo);
            break;
        case 'chatHistory':
            chatHistory($pdo);
            break;
        case 'sendMessage':
            sendMessage($pdo);
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
    // Get the form data
    $firstName = $_POST['firstname'] ?? '';
    $lastName = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobileNumber = $_POST['mobileNumber'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Initialize response array
    $response = [];

    $userName = $firstName . ' ' . $lastName;

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $response = ['msg' => 'Passwords do not match.'];
        echo json_encode($response);
        exit;
    }

    $uploadDir = 'asset/profile/';
    $defaultProfileImage = 'user.png';

    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $fileExtension = pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION);

        // Create new file name (add unique identifier like timestamp or user ID if necessary)
        $fileName = $firstName . '_profile_image.' . $fileExtension;
        $uploadFilePath = $uploadDir . $fileName;

        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move the uploaded file to the specified location
        if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $uploadFilePath)) {
            $response = ['msg' => 'Image uploaded successfully!', 'filePath' => $uploadFilePath];
        } else {
            $response = ['msg' => 'Failed to move the uploaded file.'];
            echo json_encode($response);
            exit;
        }
    } else {
        $uploadFilePath = $uploadDir . $defaultProfileImage;
        $response = ['msg' => 'No profile image uploaded. Using default image.', 'filePath' => $uploadFilePath];
    }

    // Validate password match
    if ($password !== $confirmPassword) {
        echo 'Passwords do not match.';
        return;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID); // or PASSWORD_BCRYPT

    try {


        // Prepare the SQL INSERT query
        $sql = "INSERT INTO `users` (`user_name`, `first_name`, `last_name`, `mobile_number`, `email`, `password`,`profile_image`) VALUES (:user_name, :first_name, :last_name, :mobile_number, :email, :password,:profile_image)";

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':user_name', $userName);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':mobile_number', $mobileNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':profile_image', $uploadFilePath);

        // Execute the statement
        $stmt->execute();
        $userId = $pdo->lastInsertId();

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

    parse_str($_POST['formData'], $formData);

    $email = isset($formData['email']) ? $formData['email'] : '';
    $password = isset($formData['password']) ? $formData['password'] : '';


    $response = ['success' => false, 'msg' => ''];

    try {

        $sql = "SELECT * FROM `users` WHERE `email` = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $response['success'] = true;
            $response['msg'] = 'Login successful!';

            $sql = "UPDATE `users` SET online_status = 1 WHERE `user_id` = :user_id LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user['user_id']);
            $stmt->execute();

            // Prepare the SQL SELECT query
            $sql = "SELECT * FROM `users` WHERE `user_id` = :user_id LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user['user_id']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['online_status'] = $user['online_status'];
            $_SESSION['profile_image'] = $user['profile_image'];
        } else {
            $response['msg'] = 'Invalid email or password.';
        }
    } catch (PDOException $e) {
        $response['msg'] = 'Error: ' . $e->getMessage();
    }

    echo json_encode($response);
}


function fetchUsers($pdo)
{
    $userId = $_SESSION['user_id'] ?? null;
    try {
        $sql = "SELECT user_id, CONCAT(first_name, ' ', last_name) AS user_name, online_status, profile_image FROM `users` WHERE user_id <> $userId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'users' => $users]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
    }
}

function logoutUser($pdo)
{
    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
        $sql = "UPDATE `users` SET online_status = 0 WHERE `user_id` = :user_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    session_destroy();

    echo json_encode(['success' => true, 'msg' => 'Logged out successfully.']);
}


function chatHistory($pdo)
{
    $userId = $_SESSION['user_id'] ?? null;
    $receiverId = $_POST['receiverId'] ?? null;

    if ($userId && $receiverId) {
        // Prepare SQL to fetch messages between the two users
        $sql = " SELECT * FROM chat_messages WHERE (sender_id = :userId AND receiver_id = :receiverId) OR (sender_id = :receiverId AND receiver_id = :userId) ORDER BY created_at ASC ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':receiverId', $receiverId, PDO::PARAM_INT);

        $stmt->execute();

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($messages);
    }

    return [];
}


function sendMessage($pdo)
{
    $senderId = $_SESSION['user_id'] ?? null;
    $receiverId = $_POST['receiverId'] ?? null;
    $message = $_POST['message'] ?? '';

    if (!$senderId || !$receiverId || empty($message)) {
        echo json_encode(['success' => false, 'msg' => 'Invalid input.']);
        exit;
    }

    try {
        $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message, created_at, is_read) 
                VALUES (:sender_id, :receiver_id, :message, NOW(), 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':sender_id', $senderId, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $receiverId, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);

        $stmt->execute();

        echo json_encode(['success' => true, 'msg' => 'Message sent successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
    }
}
