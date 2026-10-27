<?php
session_start();
require_once 'db.php';

// Only process POST requests; redirect GET requests back to homepage
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$age     = trim($_POST['age'] ?? '');
$gender  = trim($_POST['gender'] ?? '');
$address = trim($_POST['address'] ?? '');
$reason  = trim($_POST['reason'] ?? '');

$errors = [];

// 1. Full name validation: required, letters and spaces only
if ($name === '') {
    $errors['name'] = 'Full name is required.';
} elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
    $errors['name'] = 'Full name must contain letters and spaces only.';
}

// 2. Phone validation: required, exactly 10 digits
if ($phone === '') {
    $errors['phone'] = 'Phone number is required.';
} elseif (!preg_match('/^\d{10}$/', $phone)) {
    $errors['phone'] = 'Phone number must be exactly 10 digits.';
}

// 3. Age validation: required, number between 0 and 120
if ($age === '') {
    $errors['age'] = 'Age is required.';
} elseif (!ctype_digit($age) || (int)$age < 0 || (int)$age > 120) {
    $errors['age'] = 'Age must be a number between 0 and 120.';
}

// 4. Gender validation: required, one of allowed options
$allowed_genders = ['Male', 'Female', 'Other'];
if ($gender === '') {
    $errors['gender'] = 'Please select a gender.';
} elseif (!in_array($gender, $allowed_genders, true)) {
    $errors['gender'] = 'Invalid gender selected.';
}

// 5. Address validation: required
if ($address === '') {
    $errors['address'] = 'Address is required.';
}

// 6. Reason for visit validation: required
if ($reason === '') {
    $errors['reason'] = 'Reason for visit is required.';
}

// 7. Check for unique phone number in database
if (empty($errors['phone'])) {
    $checkStmt = $pdo->prepare("SELECT id FROM patients WHERE phone = ?");
    $checkStmt->execute([$phone]);
    if ($checkStmt->fetch()) {
        $errors['phone'] = 'A patient with this phone number is already registered.';
    }
}

// If validation errors exist, re-render the form with error messages and filled inputs
if (!empty($errors)) {
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
        <p class="subtitle">Please correct the errors below and try again.</p>

        <form action="register.php" method="POST" id="patientForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" class="<?php echo isset($errors['name']) ? 'input-error' : ''; ?>">
                <span class="field-error" id="error-name"><?php echo htmlspecialchars($errors['name'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly class="<?php echo isset($errors['phone']) ? 'input-error' : ''; ?>">
                <span class="field-error" id="error-phone"><?php echo htmlspecialchars($errors['phone'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" min="0" max="120" value="<?php echo htmlspecialchars($age); ?>" class="<?php echo isset($errors['age']) ? 'input-error' : ''; ?>">
                <span class="field-error" id="error-age"><?php echo htmlspecialchars($errors['age'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" class="<?php echo isset($errors['gender']) ? 'input-error' : ''; ?>">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo $gender === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
                <span class="field-error" id="error-gender"><?php echo htmlspecialchars($errors['gender'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="<?php echo isset($errors['address']) ? 'input-error' : ''; ?>"><?php echo htmlspecialchars($address); ?></textarea>
                <span class="field-error" id="error-address"><?php echo htmlspecialchars($errors['address'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="reason">Reason for Visit</label>
                <input type="text" id="reason" name="reason" value="<?php echo htmlspecialchars($reason); ?>" class="<?php echo isset($errors['reason']) ? 'input-error' : ''; ?>">
                <span class="field-error" id="error-reason"><?php echo htmlspecialchars($errors['reason'] ?? ''); ?></span>
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
    <?php
    exit;
}

// Insert new patient into the database using a prepared statement
$stmt = $pdo->prepare("INSERT INTO patients (name, phone, age, gender, address, reason) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$name, $phone, (int)$age, $gender, $address, $reason]);

// Save new patient ID in session and mark as newly registered
$_SESSION['patient_id'] = $pdo->lastInsertId();
$_SESSION['is_registered'] = true;

// Redirect to details view page
header("Location: view.php");
exit;
