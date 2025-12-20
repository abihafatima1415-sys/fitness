<?php
// -----------------------------------------------------------
// Start session and verify admin authentication
// -----------------------------------------------------------
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// -----------------------------------------------------------
// Include database connection
// -----------------------------------------------------------
include('../config/db_connect.php');

// -----------------------------------------------------------
// Delete trainer functionality
// -----------------------------------------------------------
if (isset($_GET['delete'])) {
    $trainer_id = $_GET['delete'];
    $delete_query = "DELETE FROM trainers WHERE id = '$trainer_id'";
    mysqli_query($conn, $delete_query);
    header("Location: manage_trainers.php");
    exit;
}

// -----------------------------------------------------------
// Fetch all trainers from database
// -----------------------------------------------------------
$query = "SELECT * FROM trainers ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trainers | Admin Panel</title>

    <style>
        /* =====================================================
           GLOBAL STYLE (Color theme #00c8b3)
        ====================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: var(--bgcolor);
            color: #333;
        }

        header {
            background-color: var(--primary-color);;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto 80px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: var(--primary-color);;
            margin-bottom: 25px;
        }

        .add-trainer {
            text-align: right;
            margin-bottom: 15px;
        }

        .add-trainer a {
            background-color: var(--primary-color);;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }

        .add-trainer a:hover {
            background-color: #019e8f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        table th {
            background-color: var(--primary-color);;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .action-btn {
            text-decoration: none;
            padding: 7px 12px;
            border-radius: 5px;
            font-size: 14px;
            color: #fff;
            display: inline-block;
            margin: 2px;
        }

        .edit-btn { background-color: var(--primary-color);; }
        .delete-btn { background-color: #e74c3c; }
        .shift-btn { background-color: #3498db; }

        .edit-btn:hover { background-color: #019e8f; }
        .delete-btn:hover { background-color: #c0392b; }
        .shift-btn:hover { background-color: #217dbb; }

        /* =====================================================
           FOOTER STYLE
        ====================================================== */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: var(--primary-color);;
            color: #fff;
            text-align: center;
            padding: 12px 0;
            font-size: 15px;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
    </style>
</head>
<body>

    <!-- ================= HEADER ================= -->
    <header><a href="<?php echo $base_url;?>/admin/dashboard.php">Dashboard</a>Manage Trainers</header>

    <!-- ================= CONTENT ================= -->
    <div class="container">
        <h2>Registered Gym Trainers</h2>

        <!-- Add New Trainer Button -->
        <div class="add-trainer">
            <a href="add_trainer.php">Add New Trainer</a>
        </div>

        <!-- Trainers Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Shift</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['shift']); ?></td>
                            <td>
                                <a href="edit_trainer.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="modify_trainer_shift.php?id=<?php echo $row['id']; ?>" class="action-btn shift-btn">Modify Shift</a>
                                <a href="manage_trainers.php?delete=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this trainer?');">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5">No trainers found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer>
        &copy; <?php echo date("Y"); ?> Fitness Management System | Admin Panel
    </footer>

</body>
</html>
