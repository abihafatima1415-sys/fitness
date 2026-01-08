<?php
session_start();
include('config/connection.php'); // Database connection


$message = "";

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

function verifyCaptcha() {
    if (!isset($_SESSION['login_captcha'])) {
        return 'Captcha expired. Please refresh.';
    }
    if (!isset($_POST['captcha_input'])) {
        return 'Please enter captcha';
    }
    if (strcasecmp($_SESSION['login_captcha'], trim($_POST['captcha_input'])) !== 0) {
        return 'Invalid CAPTCHA value given';
    }
    // Invalidate after success
    unset($_SESSION['login_captcha']);
    return 'ok';
}

function submit_form($conn){
    $user_given_captcha_value = isset($_POST['captcha_input']) ? trim($_POST['captcha_input']): '';
    
    $user_identity = isset($_POST['email']) ? $_POST['email']: '';
    $secret = isset($_POST['email']) ? $_POST['password']: '';
    // Empty field check
    if (empty($user_identity) || empty($secret)) {
        $message = "All fields are required";
        return $message;
    }
    $message = verifyCaptcha();
    if($message != 'ok') return $message;

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
            $message = "Invalid Password!";
            return $message;
        }
    } else {
        $message = "No account found with this email!";
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
            document.getElementById('captcha_image').src = 'captcha_image.php?ts=' + new Date().getTime();
        }
    </script>

</head>
<body>
    <div class="container">
        <h2>Login</h2>
       
        "<p style='color:red; text-align:center;'><?php echo $message; ?></p>
        <form method="POST" action="">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>

            <label for="role">Select Role:</label>
            <select id="role" name="role" required>
                <option value="Admin">Admin</option>
                <option value="Member">Member</option>
                <option value="Trainer">Trainer</option>
            </select>

            <!-- CAPTCHA -->
            <div class="form-group captcha-box">
                <img id="captcha_image" alt="Captcha">
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
        <script>
        refreshCaptcha();
        document.getElementById('role').value='<?php 
        if(isset($_POST['role'])) echo $_POST['role']; 
        else echo 'Admin'; ?>';
        </script>
    </div>
</body>
</html>
