<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Travel Notes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="error-popup">
            <h2>Error</h2>
            <p><?php 
                if(isset($_SESSION['error'])) {
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                } else {
                    echo "An unknown error occurred.";
                }
            ?></p>
            <a href="index.php" class="btn">Go Back</a>
        </div>
    </div>
</body>
</html> 