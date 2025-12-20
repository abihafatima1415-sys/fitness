<?php
session_start();
include('config/db_connect.php'); // Database connection


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

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
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
        }
    } else {
        $message = "<p style='color:red; text-align:center;'>No account found with this email!</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Fitness Management System</title>
    <style>
/* ------------------------------
   Global Styles
------------------------------- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

body {
    background-color: #f4f7fa;
    color: #333;
}

/* ------------------------------
   Container
------------------------------- */
.container {
    width: 100%;
    max-width: 450px;
    background-color: #fff;
    margin: 80px auto;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* ------------------------------
   Headings
------------------------------- */
h2 {
    text-align: center;
    color: #00c8b3;
    margin-bottom: 25px;
}

/* ------------------------------
   Form Styles
------------------------------- */
form {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: 500;
    margin-bottom: 5px;
}

input,
select {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
    transition: border-color 0.3s;
}

input:focus,
select:focus {
    border-color: #00c8b3;
    outline: none;
}

/* ------------------------------
   Buttons
------------------------------- */
button {
    background-color: #00c8b3;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background-color: #019e8f;
}

/* ------------------------------
   Paragraph & Links
------------------------------- */
p {
    text-align: center;
    margin-top: 15px;
}

a {
    color: #00c8b3;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}


/* ------------------------------
   Back Button Styling
------------------------------- */
.back-btn-container {
    text-align: center;
    margin: 15px 0;
}

.back-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #00c8b3;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s;
}

.back-btn:hover {
    background-color: #019e8f;
}

    </style>
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

            <label for="role">Select Role:</label>
            <select id="role" name="role" required>
                <option value="">-- Choose Role --</option>
                <option value="Admin">Admin</option>
                <option value="Member">Member</option>
                <option value="Trainer">Trainer</option>
            </select>

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
