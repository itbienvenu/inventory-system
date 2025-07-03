<?php
require_once(__DIR__ . "/../config/config.php");
require_once __DIR__."/../send_otp_email.php";

// Sanitize helper
function sanitise_string($string) {
    $string = htmlentities($string, ENT_QUOTES, "UTF-8");
    return trim($string);
}

// ✅ Log user activity
function log_user_action($user_id, $action, $module = null) {
    global $conn;

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $stmt = mysqli_prepare($conn, "
        INSERT INTO user_activity_logs (user_id, action, module, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $user_id, $action, $module, $ip, $agent);
    $stmt->execute();
    $stmt->close();
}

// ✅ Register function
function register($names, $email, $phone, $password, $role) {
    global $conn;

    $names = sanitise_string($names);
    $email = sanitise_string($email);
    $phone = sanitise_string($phone);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $role = sanitise_string($role);

    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "User already registered";
    } else {
        $stmt->close();
        $stmt = mysqli_prepare($conn, "INSERT INTO users (names, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $names, $email, $phone, $password, $role);

        if ($stmt->execute()) {
            $_SESSION['message'] = "User registered successfully";

            // Log registration action
            $new_user_id = $stmt->insert_id;
            log_user_action($new_user_id, "Registered a new account", "User Management");
        } else {
            $_SESSION['message'] = "Registration failed: " . $stmt->error;
        }
    }

    $stmt->close();
}

// ✅ Login function
function login($email, $password){
    global $conn;
    $email = sanitise_string($email);

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $user = $result->fetch_assoc()) {
        if(password_verify($password, $user["password"])) {
            // ✅ Send OTP before completing login
            $otp = rand(100000, 999999);
            $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // Insert into otp_codes table
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO otp_codes (user_id, otp_code, expires_at) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("iss", $user["id"], $otp, $expires);
            $insert_stmt->execute();

            // // Send email (basic PHP mail, or PHPMailer if available)
            $to = $user['email'];
            // $subject = "Your OTP Code";
            // $message = "Your login OTP code is: $otp. It will expire in 10 minutes.";
            // $headers = "From: no-reply@yourdomain.com\r\n";
            // mail($to, $subject, $message, $headers); // Replace with real email setup
            send_otp_email($to, $otp);;
            // Set session to wait for OTP
            $_SESSION['pending_user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            $_SESSION['message'] = "An OTP has been sent to your email. Please verify.";
            header("Location: verify_otp.php");
            exit;
        } else {
            $_SESSION["message"] = "Incorrect Password or Email";
        }
    } else {
        $_SESSION['message'] = "Incorrect Password or Email";
        return false;
    }
}

?>
