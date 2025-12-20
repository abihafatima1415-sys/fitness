<?php
// Include database connection file
include('config/db_connect.php');

// Initialize message variable for feedback
$message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user inputs safely using mysqli_real_escape_string
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new member into database
    $query = "INSERT INTO members (full_name, email, password, phone, gender) 
              VALUES ('$full_name', '$email', '$hashed_password', '$phone', '$gender')";

    if (mysqli_query($conn, $query)) {
        $message = "<p style='color:green; text-align:center;'>Registration Successful! You can now <a href='login.php'>Login</a>.</p>";
    } else {
        $message = "<p style='color:red; text-align:center;'>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration | Fitness Management System</title>

    <style>
        /* ------------------------------
           GLOBAL STYLES
        -------------------------------*/
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #f4f7fa; /* Light background for clean look */
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 450px; /* Form box width */
            background-color: #fff;
            margin: 80px auto; /* Center the box vertically */
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #00c8b3;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column; /* Stack inputs vertically */
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
        }

        input, select {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #00c8b3;
            outline: none;
        }

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
    </style>
</head>

<body>

    <div class="container">
        <h2>Member Registration</h2>

        <!-- Display success or error messages -->
        <?php echo $message; ?>

        <!-- Registration Form -->
        <form method="POST" action="register.php">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>

            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" placeholder="Enter your phone number">

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login Here</a></p>
    </div>

</body>
</html>
