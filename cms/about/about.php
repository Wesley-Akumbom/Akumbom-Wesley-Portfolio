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

<div class="container mt-5">
    <h1 class="text-center">About Information</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-messages alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($profile_id): ?>
        <?php if ($experience && $education): ?>
            <div class="about-info">
                <!-- Experience Section -->
                <h2 class="text-primary mt-4">Experience</h2>
                <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($experience->exp_years); ?></p>
                <p><strong>Field of Experience:</strong> <?php echo htmlspecialchars($experience->exp_field); ?></p>
                <p><strong>About Me:</strong> <?php echo htmlspecialchars($experience->about_me); ?></p>

                <!-- Education Section -->
                <h2 class="text-primary mt-4">Education</h2>
                <p><strong>Education Level:</strong> <?php echo htmlspecialchars($education->level); ?></p>
                <p><strong>Certificate:</strong> <?php echo htmlspecialchars($education->certificate); ?></p>
                <p><strong>Year:</strong> <?php echo htmlspecialchars($education->years); ?></p>

                <!-- Action Buttons -->
                <div class="actions mt-4">
                    <a href="update-about.php" class="btn btn-primary mr-2">Update</a>
                    <a href="delete-about.php" class="btn btn-danger">Delete</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <p>No about information found. <a href="create-about.php" class="btn btn-secondary">Add</a></p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-danger">
            <p>Please create a profile before managing About information.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once "../includes/admin_footer.php"; ?>
