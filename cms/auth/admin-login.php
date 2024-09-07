<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";

session_start(); // Start the session

require_once "../includes/admin_header.php";

// Initialize variables
$errors = [];

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../profile/profile.php"); // Redirect to profile page if already logged in
    exit;
}

// Handle form submission for login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'], 'email');
    $password = sanitizeInput($_POST['password'], 'string');

    // Validate input
    if (empty($email) || empty($password)) {
        $errors[] = "Email and Password are required fields.";
    }

    if (empty($errors)) {
        try {
            // Check if the user exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            if ($user && password_verify($password, $user->password)) {
                // Set session variables
                $_SESSION['user_id'] = $user->id; // Store user ID in session
                $_SESSION['username'] = $user->username; // Store username in session
                header("Location: ../profile/profile.php"); // Redirect to profile page
                exit;
            } else {
                $errors[] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Admin Login</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="w-50 mx-auto">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>

    <p class="text-center mt-3">
        Don't have an account? <a href="admin-register.php">Register here</a>
    </p>
</div>

<?php require_once "../includes/admin_footer.php"; ?>