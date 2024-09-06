<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("location: ".ADMINURL."");
    exit; // Ensure the script stops after redirection
}

// Handle experience deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all'])) {
    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("DELETE FROM experience WHERE profile_id = (SELECT id FROM Profile LIMIT 1)");
        $stmt->execute();
        $conn->commit();
        $message = "All experiences deleted successfully!";
    } catch (PDOException $e) {
        $conn->rollBack();
        $errors[] = "Database error: " . $e->getMessage();
    }

    // Redirect to the experiences management page
    header("Location: delete-experience.php");
    exit;
}

require_once "../includes/admin_header.php";
?>

<h1>Delete All Experiences</h1>

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

<p>Are you sure you want to delete all experiences?</p>

<form method="POST" action="">
    <input type="hidden" name="delete_all" value="1">
    <button type="submit" class="btn btn-danger">Yes, Delete All</button>
    <a href="delete-experience.php" class="btn">Cancel</a>
</form>

<?php require_once "../includes/admin_footer.php"; ?>