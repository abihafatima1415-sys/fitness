<?php
// -----------------------------------------------------------
// Start session and verify trainer authentication
// -----------------------------------------------------------
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Trainer') {
    header("Location: ../login.php");
    exit;
}

// -----------------------------------------------------------
// Include database connection
// -----------------------------------------------------------
include('../config/db_connect.php');

// Get trainer ID from session
$trainer_id = $_SESSION['user_id'];

// -----------------------------------------------------------
// Handle Add or Edit Diet Plan
// -----------------------------------------------------------
$message = "";
$edit_plan = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = intval($_POST['member_id']);
    $plan_name = mysqli_real_escape_string($conn, $_POST['plan_name']);
    $meal_details = mysqli_real_escape_string($conn, $_POST['meal_details']);

    if (!empty($_POST['diet_id'])) {
        // Update existing plan
        $diet_id = intval($_POST['diet_id']);
        $query = "UPDATE diet_plans 
                  SET member_id='$member_id', plan_name='$plan_name', meal_details='$meal_details', updated_at=NOW()
                  WHERE id='$diet_id' AND trainer_id='$trainer_id'";
        $msg_text = "updated";
    } else {
        // Add new plan
        $query = "INSERT INTO diet_plans (trainer_id, member_id, plan_name, meal_details, created_at) 
                  VALUES ('$trainer_id', '$member_id', '$plan_name', '$meal_details', NOW())";
        $msg_text = "added";
    }

    if (mysqli_query($conn, $query)) {
        $message = "<p style='color:green; text-align:center;'>Diet plan $msg_text successfully!</p>";
    } else {
        $message = "<p style='color:red; text-align:center;'>Error: " . mysqli_error($conn) . "</p>";
    }
}

// -----------------------------------------------------------
// Handle Delete
// -----------------------------------------------------------
if (isset($_GET['delete'])) {
    $diet_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM diet_plans WHERE id='$diet_id' AND trainer_id='$trainer_id'");
    $message = "<p style='color:red; text-align:center;'>Diet plan deleted successfully!</p>";
}

// -----------------------------------------------------------
// Handle Edit Load
// -----------------------------------------------------------
if (isset($_GET['edit'])) {
    $diet_id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM diet_plans WHERE id='$diet_id' AND trainer_id='$trainer_id'");
    $edit_plan = mysqli_fetch_assoc($result);
}

// -----------------------------------------------------------
// Fetch all diet plans assigned by this trainer
// -----------------------------------------------------------
$result = mysqli_query($conn, "
    SELECT d.id, d.plan_name, d.meal_details, d.created_at, m.full_name AS member_name
    FROM diet_plans d
    JOIN members m ON d.member_id = m.id
    WHERE d.trainer_id = '$trainer_id'
    ORDER BY d.created_at DESC
");

// Fetch all members for assigning diet plans
$members_result = mysqli_query($conn, "SELECT id, full_name FROM members ORDER BY full_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Diet Plans | Trainer Dashboard</title>
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
    background: var(--bgcolor);
    color: #333;
}

/* ------------------------------
   Container
------------------------------- */
.container {
    width: 90%;
    max-width: 1000px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

/* ------------------------------
   Headings
------------------------------- */
h2 {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 25px;
}

/* ------------------------------
   Messages
------------------------------- */
.message {
    margin-bottom: 15px;
    text-align: center;
}

/* ------------------------------
   Table Styling
------------------------------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

table th,
table td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
}

table th {
    background: var(--primary-color);
    color: #fff;
}

table tr:nth-child(even) {
    background: #f9f9f9;
}

/* ------------------------------
   Action Buttons
------------------------------- */
.action-btn {
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 5px;
    color: #fff;
    display: inline-block;
    font-size: 14px;
    transition: 0.3s;
}

.edit-btn {
    background: var(--primary-color);
}

.delete-btn {
    background: #e74c3c;
}

.edit-btn:hover {
    background: #019e8f;
}

.delete-btn:hover {
    background: #c0392b;
}

/* ------------------------------
   Forms & Inputs
------------------------------- */
form {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

input,
select,
textarea {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
}

input:focus,
select:focus,
textarea:focus {
    border-color: var(--primary-color);
    outline: none;
}

button {
    background: var(--primary-color);
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #019e8f;
}

/* Cancel link */
a.cancel {
    color: #e74c3c;
    text-decoration: none;
    font-size: 14px;
    margin-left: 5px;
}

a.cancel:hover {
    text-decoration: underline;
}

/* ------------------------------
   Back Button
------------------------------- */
.back-btn-container {
    text-align: center;
    margin-top: 20px;
}

.back-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: var(--primary-color);
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
    <h2>Assign / Edit Diet Plans</h2>

    <!-- Status message -->
    <div class="message"><?= $message; ?></div>

    <!-- Diet Plan Form -->
    <form method="POST" action="">
        <input type="hidden" name="diet_id" value="<?= $edit_plan['id'] ?? ''; ?>">
        <select name="member_id" required>
            <option value="">Select Member</option>
            <?php while($m = mysqli_fetch_assoc($members_result)): ?>
                <option value="<?= $m['id']; ?>" <?= isset($edit_plan) && $edit_plan['member_id']==$m['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($m['full_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="plan_name" placeholder="Diet Plan Name" value="<?= $edit_plan['plan_name'] ?? ''; ?>" required>
        <textarea name="meal_details" placeholder="Meal & Nutrition Details" rows="4" required><?= $edit_plan['meal_details'] ?? ''; ?></textarea>
        <button type="submit"><?= isset($edit_plan) ? 'Update Diet Plan' : 'Save Diet Plan'; ?></button>
        <?php if(isset($edit_plan)): ?>
            <a href="diet_plans.php" class="cancel">Cancel</a>
        <?php endif; ?>
    </form>

    <!-- Existing Diet Plans Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Plan Name</th>
                <th>Member</th>
                <th>Meal Details</th>
                <th>Assigned On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['plan_name']); ?></td>
                        <td><?= htmlspecialchars($row['member_name']); ?></td>
                        <td><?= htmlspecialchars($row['meal_details']); ?></td>
                        <td><?= date("d M Y", strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="?edit=<?= $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                            <a href="?delete=<?= $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this diet plan?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No diet plans assigned yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
<div class="back-btn-container">
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
</div>
</div>

</body>
</html>
