<?php
session_start();
require_once 'db.php';

$phone = trim($_GET['phone'] ?? '');

// Server-side validation for phone lookup
if ($phone === '' || !preg_match('/^\d{10}$/', $phone)) {
    header("Location: index.php?error=invalid_phone");
    exit;
}

// Prepared statement to search patient by phone number
$stmt = $pdo->prepare("SELECT * FROM patients WHERE phone = ?");
$stmt->execute([$phone]);
$patient = $stmt->fetch();

// If patient exists, store ID in session and redirect to details page
if ($patient) {
    $_SESSION['patient_id'] = $patient['id'];
    $_SESSION['is_existing'] = true;
    header("Location: view.php");
    exit;
}

// If patient does not exist, display the registration form with pre-filled phone
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Patient Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>New Patient Registration</h1>
    <p class="subtitle">No record found for <?php echo htmlspecialchars($phone); ?>. Please enter your details below.</p>

    <form action="register.php" method="POST" id="patientForm">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Letters and spaces only">
            <span class="field-error" id="error-name"></span>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly>
            <span class="field-error" id="error-phone"></span>
        </div>

        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" id="age" name="age" min="0" max="120" placeholder="0 - 120">
            <span class="field-error" id="error-age"></span>
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <span class="field-error" id="error-gender"></span>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" placeholder="Enter full address"></textarea>
            <span class="field-error" id="error-address"></span>
        </div>

        <div class="form-group">
            <label for="reason">Reason for Visit</label>
            <input type="text" id="reason" name="reason" placeholder="e.g. Health checkup, Headache">
            <span class="field-error" id="error-reason"></span>
        </div>

        <button type="submit" class="btn-primary">Register Patient</button>
    </form>

    <div class="links">
        <a href="index.php" class="link-secondary">Search another number</a>
    </div>
</div>
<script src="validate.js"></script>
</body>
</html>
