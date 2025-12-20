<?php
// Start session to handle user logins if needed
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page title and meta information -->
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Management System</title>

    <!-- Embedded CSS for consistent styling across all project pages -->
    <style>
        /* ------------------------------
           GLOBAL STYLES (Applied to all pages)
        -------------------------------*/
        * {
            margin: 0; /* Remove default margins */
            padding: 0; /* Remove default padding */
            box-sizing: border-box; /* Include padding/border in element width */
            font-family: "Poppins", sans-serif; /* Clean professional font */
        }

        body {
            background-color: var(--bgcolor); /* Light gray background for clean look */
            color: #333; /* Dark text for readability */
            line-height: 1.6; /* Comfortable line spacing */
        }

        a {
            text-decoration: none; /* Remove underline from links */
            color: inherit; /* Inherit color from parent */
        }

        /* ------------------------------
           HEADER & NAVIGATION BAR
        -------------------------------*/
        header {
            background-color: #1b1f3b; /* Dark navy background */
            color: #fff; /* White text for contrast */
            padding: 20px 0; /* Vertical padding for header */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
        }

        .container {
            width: 90%; /* Keep content within readable width */
            margin: auto; /* Center container */
        }

        nav {
            display: flex; /* Use flexbox for horizontal layout */
            justify-content: space-between; /* Space between logo & links */
            align-items: center; /* Vertically center items */
        }

        .logo {
            font-size: 26px; /* Larger font for logo */
            font-weight: 700; /* Bold logo text */
            color: var(--primary-color);; /* Teal accent color */
        }

        .nav-links {
            list-style: none; /* Remove bullets */
            display: flex; /* Display links in a row */
            gap: 25px; /* Add space between links */
        }

        .nav-links a {
            color: #fff; /* White text for nav items */
            font-weight: 500; /* Semi-bold for clarity */
            transition: 0.3s; /* Smooth hover transition */
        }

        .nav-links a:hover {
            color: var(--primary-color);; /* Teal hover effect */
        }

/* ------------------------------
   HERO SECTION
-------------------------------*/
.hero {
    background-color: var(--primary-color);; /* Solid teal background color */
    height: 90vh; /* Full viewport height */
    display: flex; /* Use flexbox for centering */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    text-align: center; /* Center text alignment */
    color: #fff; /* White text for contrast */
    padding: 0 20px; /* Side padding for smaller screens */
}

.hero-content {
    max-width: 700px; /* Restrict content width for readability */
}

.hero-content h1 {
    font-size: 48px; /* Large heading font */
    margin-bottom: 20px; /* Space below heading */
}

.hero-content p {
    font-size: 18px; /* Paragraph font size */
    margin-bottom: 30px; /* Space below paragraph */
}

.hero-content a {
    background-color: #fff; /* White button */
    color: var(--primary-color);; /* Teal button text */
    padding: 12px 30px; /* Button padding */
    border-radius: 30px; /* Rounded button corners */
    font-weight: 600; /* Bold button text */
    transition: all 0.3s ease; /* Smooth hover effect */
}

.hero-content a:hover {
    background-color: #019e8f; /* Darker teal hover color */
    color: #fff; /* White text on hover */
}


        /* ------------------------------
           FEATURES SECTION
        -------------------------------*/
        .features {
            display: grid; /* Use grid layout */
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive columns */
            gap: 20px; /* Space between boxes */
            padding: 60px 0; /* Top and bottom spacing */
        }

        .feature-box {
            background-color: #fff; /* White box */
            border-radius: 10px; /* Rounded corners */
            padding: 30px; /* Internal padding */
            text-align: center; /* Center text */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
            transition: transform 0.3s; /* Smooth hover lift */
        }

        .feature-box:hover {
            transform: translateY(-5px); /* Slight lift on hover */
        }

        .feature-box h3 {
            color: var(--primary-color);; /* Teal titles */
            margin-bottom: 10px; /* Space below title */
        }

        /* ------------------------------
           FOOTER SECTION
        -------------------------------*/
        footer {
            background-color: #1b1f3b; /* Dark footer */
            color: #fff; /* White text */
            text-align: center; /* Center text */
            padding: 20px 0; /* Spacing */
            font-size: 14px; /* Small text */
            margin-top: 40px; /* Space above footer */
        }
    </style>
</head>

<body>

    <!-- =========================
         HEADER SECTION
    ========================== -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">Web-based Fitness Management System</div> <!-- Website logo -->
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- =========================
         HERO / LANDING SECTION
    ========================== -->
    <section class="hero">
        <div class="hero-content">
            <h1>Stay Fit. Stay Healthy. Stay Strong.</h1>
            <p>Track your workouts, follow your diet plan, and connect with professional trainers all in one place.</p>
            <a href="register.php">Get Started</a>
        </div>
    </section>

    <!-- =========================
         FEATURES SECTION
    ========================== -->
    <section class="container" id="features">
        <div class="features">
            <div class="feature-box">
                <h3>Easy Member Management</h3>
                <p>Register, renew, and track memberships effortlessly through an intuitive dashboard.</p>
            </div>
            <div class="feature-box">
                <h3>Trainer Scheduling</h3>
                <p>Trainers can manage workout plans and schedules for clients in real-time.</p>
            </div>
            <div class="feature-box">
                <h3>Progress Tracking</h3>
                <p>Monitor your fitness journey with detailed progress reports and analytics.</p>
            </div>
            <div class="feature-box">
                <h3>Secure Payments</h3>
                <p>Make secure online payments using trusted payment gateways and instant receipts.</p>
            </div>
        </div>
    </section>

    <!-- =========================
         FOOTER SECTION
    ========================== -->
    <footer id="contact">
        <p>© 2025 FitTrack | Web-Based Fitness Management System | Designed by Web-based Fitness Management System</p>
    </footer>

</body>
</html>
