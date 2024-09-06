<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("location: ".ADMINURL."");
    exit; // Ensure the script stops after redirection
}

require_once "../includes/admin_header.php"; // Include the header which contains the sidebar

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
    // Create objects for new data
    $about_exp = new stdClass();
    $about_edu = new stdClass();

    // Sanitize and assign new values
    $about_exp->exp_years = sanitizeInput($_POST['exp_years'], 'int');
    $about_exp->exp_field = sanitizeInput($_POST['exp_field'], 'string');
    $about_exp->about_me = sanitizeInput($_POST['about_me'], 'string');

    $about_edu->level = sanitizeInput($_POST['level'], 'string');
    $about_edu->certificate = sanitizeInput($_POST['certificate'], 'string');
    $about_edu->years = sanitizeInput($_POST['years'], 'string');

    try {
        $conn->beginTransaction();

        // Insert experience information
        $stmt = $conn->prepare("INSERT INTO about_exp (exp_years, exp_field, about_me, profile_id) VALUES (:exp_years, :exp_field, :about_me, :profile_id)");
        $stmt->execute([
            ':exp_years' => $about_exp->exp_years,
            ':exp_field' => $about_exp->exp_field,
            ':about_me' =>  $about_exp->about_me,
            ':profile_id' => $profile_id
        ]);

        // Insert education information
        $stm = $conn->prepare("INSERT INTO about_edu (level, certificate, years, profile_id) VALUES (:level, :certificate, :years, :profile_id)");
        $stm->execute([
            ':level' => $about_edu->level,
            ':certificate' => $about_edu->certificate,
            ':years' => $about_edu->years, 
            ':profile_id' => $profile_id
        ]);

        $conn->commit();
        $message = "About information created successfully!";
    } catch (PDOException $e) {
        $conn->rollBack();
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Create About Information</h1>

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
        <form method="POST" action="" class="w-75 mx-auto">
            <div class="form-group">
                <label for="exp_years">Years of Experience:</label>
                <input type="number" id="exp_years" name="exp_years" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="exp_field">Field of Experience:</label>
                <input type="text" id="exp_field" name="exp_field" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="about_me">About Me:</label>
                <textarea id="about_me" name="about_me" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="level">Education Level:</label>
                <input type="text" id="level" name="level" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="certificate">Certificate:</label>
                <input type="text" id="certificate" name="certificate" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="years">Year:</label>
                <input type="text" id="years" name="years" class="form-control" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">
            <p>Please create a profile before managing About information.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once "../includes/admin_footer.php"; ?>