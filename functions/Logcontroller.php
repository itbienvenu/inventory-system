<?php
require_once(__DIR__ . "/../config/config.php");

function sanitise_string($string) {
    $string = htmlentities($string, ENT_QUOTES, "UTF-8");
    return trim($string);
}

function register($names, $email, $phone, $password, $role) {
    global $conn;

    // Sanitize inputs
    $names = sanitise_string($names);
    $email = sanitise_string($email);
    $phone = sanitise_string($phone);
    $password = password_hash($password, PASSWORD_DEFAULT); // Secure hashing
    $role = sanitise_string($role);

    // Check if user already exists
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); // Required to use num_rows

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "User already registered";
    } else {
        $stmt->close(); // Close previous statement before reusing

        $stmt = mysqli_prepare($conn, "INSERT INTO users (names, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $names, $email, $phone, $password, $role);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "User registered successfully";
        } else {
            $_SESSION['message'] = "Registration failed: " . $stmt->error;
        }
    }

    $stmt->close();
}

function login($email, $password){
    global $conn;
    $email = sanitise_string($email);
    // Do NOT sanitize the password here!
    // $password = sanitise_string($password);

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        if(password_verify($password, $row["password"])){
            $_SESSION["message"] = "Logged in well";
        } else {
            $_SESSION["message"] = "Incorrect Password or email";
        }
    } else {
        $_SESSION['message'] = "    i   ncorect Password or Email";
        return false;   
    }
}
?>
