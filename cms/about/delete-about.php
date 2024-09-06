<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

session_start(); // Start the session

// Initialize variables
$errors = [];
$message = '';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("location: ".ADMINURL."");
    exit; // Ensure the script stops after redirection
}

// Fetch the profile_id
$stmt = $conn->prepare("SELECT id FROM Profile LIMIT 1");
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_OBJ);

if (!$profile) {
    $errors[] = "No profile found. Please create a profile first.";
}

$profile_id = $profile ? $profile->id : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $profile_id) {
    try {
        $conn->beginTransaction();

        // Delete experience information
        $stmt = $conn->prepare("DELETE FROM about_exp WHERE profile_id = :profile_id");
        $stmt->execute([':profile_id' => $profile_id]);

        // Delete education information
        $stm = $conn->prepare("DELETE FROM about_edu WHERE profile_id = :profile_id");
        $stm->execute([':profile_id' => $profile_id]);

        $conn->commit();
        $message = "About information deleted successfully!";
    } catch (PDOException $e) {
        $conn->rollBack();
        $errors[] = "Database error: " . $e->getMessage();
    }
}

// Include the admin header and display the form
require_once "../includes/admin_header.php";
?>

<div class="container">
    <h1 class="text-center">Delete About Information</h1>

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

    <?php if ($profile_id): ?>
        <div class="card">
            <div class="card-body">
                <p>Are you sure you want to delete the About information?</p>
                <form method="POST" action="">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <a href="about.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center">Please create a profile before managing About information.</p>
    <?php endif; ?>
</div>

<?php require_once "../includes/admin_footer.php"; ?>