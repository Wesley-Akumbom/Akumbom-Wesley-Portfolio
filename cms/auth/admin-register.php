<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";

session_start(); // Start the session

require_once "../includes/admin_header.php";

// Initialize variables
$errors = [];
$message = '';

// Handle form submission for registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'], 'string');
    $email = sanitizeInput($_POST['email'], 'email');
    $password = sanitizeInput($_POST['password'], 'string');

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = "Username, Email, and Password are required fields.";
    }

    // Check for existing username or email
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email
            ]);
            $existingUser = $stmt->fetch(PDO::FETCH_OBJ);

            if ($existingUser) {
                $errors[] = "Username or email already exists.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user into the users table
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $hashedPassword
                ]);
                $message = "Registration successful! You can now log in.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    header("Location: admin-login.php");
    exit;
}
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Admin Register</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Register</button>
        </div>
    </form>

    <p class="text-center mt-3">
        Already registered? <a href="admin-login.php">Login here</a>
    </p>
</div>

<?php require_once "../includes/admin_footer.php"; ?>