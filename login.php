<?php
session_start();
include('config/db_connect.php'); // Database connection


$message = "";
function generateCaptcha() {
    return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);
}

// Ensure CAPTCHA is always set
if (!isset($_SESSION['login_captcha']) || empty($_SESSION['login_captcha'])) {
    $_SESSION['login_captcha'] = generateCaptcha();
}

// Handle AJAX request for CAPTCHA refresh
if (isset($_GET['action']) && $_GET['action'] === 'refresh_captcha') {
    $_SESSION['login_captcha'] = generateCaptcha();
    echo $_SESSION['login_captcha'];
    exit;
}

// Redirect if already logged in
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === "Admin") {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['user_role'] === "Member") {
        header("Location: member/dashboard.php");
        exit;
    } elseif ($_SESSION['user_role'] === "Trainer") {
        header("Location: trainer/dashboard.php");
        exit;
    }
}
function submit_form($conn){
    $user_given_captcha_value = isset($_POST['captcha_input']) ? trim($_POST['captcha_input']): '';
    
    $user_identity = isset($_POST['email']) ? $_POST['email']: '';
    $secret = isset($_POST['email']) ? $_POST['password']: '';
    // Empty field check
    if (empty($user_identity) || empty($secret)) {
        $message = "<p style='color:red;'>All fields are required</p>";
        $_SESSION['login_captcha'] = generateCaptcha();
        return $message;
    }
    // CAPTCHA check
    elseif ($user_given_captcha_value != $_SESSION['login_captcha']) {
        $message = "<p style='color:red;'>Invalid CAPTCHA</p>";
        $_SESSION['login_captcha'] = generateCaptcha();
        return $message;
    }

    $email = mysqli_real_escape_string($conn, $user_identity);
    $password = mysqli_real_escape_string($conn, $secret);
    if(!isset($_POST['role'])) {
         $_POST['role'] = 'Admin';
    }
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Select correct table based on role
    if ($role === "Admin") {
        $table = "admins";
    } elseif ($role === "Member") {
        $table = "members";
    } else {
        $table = "trainers";
    }
      // Fetch user record
    $query = "SELECT * FROM $table WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password'])) {

            // Store session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $role; // store the role safely

            // Redirect based on role
            if ($role === "Admin") {
                header("Location: admin/dashboard.php");
            } elseif ($role === "Member") {
                header("Location: member/dashboard.php");
            } else {
                header("Location: trainer/dashboard.php");
            }
            exit;
        } else {
            $message = "<p style='color:red; text-align:center;'>Invalid Password!</p>";
            return $message;
        }
    } else {
        $message = "<p style='color:red; text-align:center;'>No account found with this email!</p>";
        return $message;
    }
    return $message;
}
// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message=submit_form($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <link rel="stylesheet" href="<?php echo $base_url;?>/css/login.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Fitness Management System</title>
    <script>
        function refreshCaptcha() {
            fetch("?action=refresh_captcha")
                .then(response => response.text())
                .then(data => {
                    document.getElementById('captchaText').innerText = data;
                });
        }
    </script>

</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>

            <!-- <label for="role">Select Role:</label>
            <select id="role" name="role" required>
                <option value="">-- Choose Role --</option>
                <option value="Admin">Admin</option>
                <option value="Member">Member</option>
                <option value="Trainer">Trainer</option>
            </select> -->

            <!-- CAPTCHA -->
            <div class="form-group captcha-box">
                <span class="captcha-text" id="captchaText">
                    <?php echo isset($_SESSION['login_captcha']) ? $_SESSION['login_captcha'] : generateCaptcha(); ?>
                </span>
                <input type="text" name="captcha_input" placeholder="Enter CAPTCHA" required>
                <span style="cursor:pointer" class="reload" onclick="refreshCaptcha()">🔄</span>
            </div>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register as Member</a></p>
<!-- Back to Index Button -->
<div class="back-btn-container">
    <a href="index.php" class="back-btn">Back to Index</a>
</div>

    </div>
</body>
</html>
