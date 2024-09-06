<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";

session_start(); // Start the session

$errors = [];
$message = '';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../profile/profile.php"); // Redirect to profile page if already logged in
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'], 'string');
    $email = sanitizeInput($_POST['email'], 'email');

    // Validate input
    if (empty($username) || empty($email)) {
        $errors[] = "Username and Email are required fields.";
    }

    if (empty($errors)) {
        try {
            // Check if the user exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND email = :email");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email
            ]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user->id; // Store user ID in session
                $_SESSION['username'] = $user->username; // Store username in session
                header("Location: ../profile/profile.php"); // Redirect to profile page
                exit;
            } else {
                $errors[] = "Invalid username or email.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php require_once "../includes/admin_header.php"; ?>

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
            <label for="username" class="font-weight-bold">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email" class="font-weight-bold">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Login</button>

            <p class="text-center mt-3">
       Don't have an account? <a href="admin-register.php">Register here</a>
    </p>
        </div>
    </form>
</div>

<?php require_once "../includes/admin_footer.php"; ?>