<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("location: ".ADMINURL."");
    exit; // Ensure the script stops after redirection
}

// Fetch profile data
$stmt = $conn->prepare("SELECT * FROM Profile LIMIT 1");
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_OBJ);

// Handle profile deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_profile'])) {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("DELETE FROM Profile WHERE id = :id");
            $stmt->execute([':id' => $profile->id]);
            $conn->commit();
            $message = "Profile deleted successfully!";

            // Redirect to the create-profile.php page
            header("Location: create-profile.php");
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php require_once "../includes/admin_header.php"; ?>

<h1>Delete Profile</h1>

<?php if (!empty($errors)): ?>
    <div class="error-messages">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($message)): ?>
    <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php if ($profile): ?>
    <p>Are you sure you want to delete the profile for <strong><?php echo htmlspecialchars($profile->name); ?></strong>?</p>
    <form method="POST" action="">
        <input type="hidden" name="delete_profile" value="true">
        <button type="submit" class="btn btn-danger">Delete Profile</button>
        <a href="profile.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php else: ?>
    <p>No profile found to delete.</p>
    <a href="create-profile.php" class="btn btn-primary">Create Profile</a>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>