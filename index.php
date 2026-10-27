<?php
session_start();

$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Patient Portal - Check Phone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="page-home">
<div class="container">
    <h1>Hospital Patient Portal</h1>
    <p class="subtitle">Enter your 10-digit phone number to view your records or register as a new patient.</p>

    <?php if ($error === 'invalid_phone'): ?>
        <div class="alert alert-error">Please enter a valid 10-digit phone number.</div>
    <?php endif; ?>

    <?php if ($message === 'logged_out'): ?>
        <div class="alert alert-success">You have been logged out successfully.</div>
    <?php endif; ?>

    <form action="check.php" method="GET" id="checkForm">
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                placeholder="10-digit mobile number" 
                maxlength="10" 
                autocomplete="tel"
                value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>"
            >
            <span class="field-error" id="error-phone"></span>
        </div>

        <button type="submit" class="btn-primary">Check</button>
    </form>
</div>
<script src="validate.js"></script>
</body>
</html>
