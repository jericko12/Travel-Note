<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_password'])) {
        // Handle Signup
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($password !== $confirm_password) {
            $error = "Passwords do not match";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username already exists";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $hashed_password]);
                
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit();
            }
        }
    } else {
        // Handle Login
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup - Travel Notes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="container">
        <div class="auth-container" id="authContainer">
            <div class="auth-flipper">
                <!-- Login Form -->
                <div class="auth-form login">
                    <div class="form-content">
                        <h2>Login</h2>
                        <?php if (isset($error)): ?>
                            <div class="error-message"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="login.php" method="POST">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn">Login</button>
                        </form>
                    </div>
                    <p class="auth-link">Don't have an account? <a href="#" class="flip-btn" onclick="flipCard()">Sign up</a></p>
                </div>

                <!-- Signup Form -->
                <div class="auth-form signup">
                    <div class="form-content">
                        <h2>Sign Up</h2>
                        <form action="login.php" method="POST">
                            <div class="form-group">
                                <label for="signup-username">Username:</label>
                                <input type="text" id="signup-username" name="username" required>
                            </div>

                            <div class="form-group">
                                <label for="signup-password">Password:</label>
                                <input type="password" id="signup-password" name="password" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>

                            <button type="submit" class="btn">Sign Up</button>
                        </form>
                    </div>
                    <p class="auth-link">Already have an account? <a href="#" class="flip-btn" onclick="flipCard()">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function flipCard() {
        document.getElementById('authContainer').classList.toggle('flip');
    }

    // If there's an error on signup, flip the card to show signup form
    <?php if (isset($error) && isset($_POST['confirm_password'])): ?>
        document.getElementById('authContainer').classList.add('flip');
    <?php endif; ?>
    </script>
</body>
</html> 