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

// Fetch existing about data
$stmt = $conn->prepare("SELECT * FROM about_exp WHERE profile_id = :profile_id");
$stmt->execute([':profile_id' => $profile_id]);
$experience = $stmt->fetch(PDO::FETCH_OBJ);

$stm = $conn->prepare("SELECT * FROM about_edu WHERE profile_id = :profile_id");
$stm->execute([':profile_id' => $profile_id]);
$education = $stm->fetch(PDO::FETCH_OBJ);
?>

<h1>About Information</h1>

<?php if (!empty($errors)): ?>
    <div class="error-messages">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($profile_id): ?>
    <?php if ($experience && $education): ?>
        <div class="about-info">
            <h2>Experience</h2>
            <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($experience->exp_years); ?></p>
            <p><strong>Field of Experience:</strong> <?php echo htmlspecialchars($experience->exp_field); ?></p>
            <p><strong>About Me:</strong> <?php echo htmlspecialchars($experience->about_me); ?></p>

            <h2>Education</h2>
            <p><strong>Education Level:</strong> <?php echo htmlspecialchars($education->level); ?></p>
            <p><strong>Certificate:</strong> <?php echo htmlspecialchars($education->certificate); ?></p>
            <p><strong>Year:</strong> <?php echo htmlspecialchars($education->years); ?></p>

            <div class="actions">
                <a href="update-about.php" class="btn">Update</a>
                <a href="delete-about.php" class="btn">Delete</a>
            </div>
        </div>
    <?php else: ?>
        <p>No about information found. <a href="create-about.php" class="btn">Add</a></p>
    <?php endif; ?>
<?php else: ?>
    <p>Please create a profile before managing About information.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>