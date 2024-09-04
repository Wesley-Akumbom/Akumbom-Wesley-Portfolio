<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

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

<h1>Delete About Information</h1>

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

<?php if ($profile_id): ?>
    <form method="POST" action="">
        <p>Are you sure you want to delete the About information?</p>
        <button type="submit">Delete</button>
    </form>
<?php else: ?>
    <p>Please create a profile before managing About information.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>