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

<div class="manage-profile-header">
    <h1>Manage Profile</h1>
</div>

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
    <div class="profile-details">
        <img src="<?php echo htmlspecialchars($profile->image); ?>" alt="Profile Image" class="profile-image">
        <h2><?php echo htmlspecialchars($profile->name); ?></h2>
        <p><?php echo htmlspecialchars($profile->role); ?></p>
        <p>Email: <?php echo htmlspecialchars($profile->email); ?></p>
        <p>LinkedIn: <a href="<?php echo htmlspecialchars($profile->linkedin_url); ?>" target="_blank"><?php echo htmlspecialchars($profile->linkedin_url); ?></a></p>
        <p>GitHub: <a href="<?php echo htmlspecialchars($profile->github_url); ?>" target="_blank"><?php echo htmlspecialchars($profile->github_url); ?></a></p>
        <p>Resume: <a href="<?php echo htmlspecialchars($profile->resume); ?>" target="_blank"><?php echo htmlspecialchars(basename($profile->resume)); ?></a></p>
        <div class="actions">
            <a href="update-profile.php" class="btn btn-primary">Update</a>
            <form method="POST" action="" class="delete-form">
                <input type="hidden" name="delete_profile" value="true">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <p>No profile found. Please <a href="create-profile.php">add a profile</a>.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>