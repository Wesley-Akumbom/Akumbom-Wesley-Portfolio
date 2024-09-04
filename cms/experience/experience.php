<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

// Fetch experiences
$stmt = $conn->prepare("SELECT * FROM experience WHERE profile_id = (SELECT id FROM Profile LIMIT 1) ORDER BY updated_at DESC");
$stmt->execute();
$experiences = $stmt->fetchAll(PDO::FETCH_OBJ);

// Handle experience deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = sanitizeInput($_POST['delete_id'], 'int');

        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("DELETE FROM experience WHERE id = :id");
            $stmt->execute([':id' => $deleteId]);
            $conn->commit();
            $message = "Experience deleted successfully!";

            // Redirect to the same page to refresh the list
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_all'])) {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("DELETE FROM experience WHERE profile_id = (SELECT id FROM Profile LIMIT 1)");
            $stmt->execute();
            $conn->commit();
            $message = "All experiences deleted successfully!";

            // Redirect to the same page to refresh the list
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php require_once "../includes/admin_header.php"; ?>

<div class="manage-experiences-header">
    <h1>Manage Experiences</h1>
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

<?php if (!empty($experiences)): ?>
    <table class="experience-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Skills</th>
                <th>Levels</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($experiences as $experience): ?>
                <tr>
                    <td><?php echo htmlspecialchars($experience->title); ?></td>
                    <td>
                        <?php
                        $skills = explode(',', $experience->skill);
                        foreach ($skills as $skill) {
                            echo htmlspecialchars(trim($skill)) . '<br>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $levels = explode(',', $experience->level);
                        foreach ($levels as $level) {
                            echo htmlspecialchars(trim($level)) . '<br>';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="update-experience.php?id=<?php echo $experience->id; ?>" class="btn btn-primary">Update</a>
                        <form method="POST" action="" class="delete-form">
                            <input type="hidden" name="delete_id" value="<?php echo $experience->id; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No experiences found. Please add some experiences.</p>
<?php endif; ?>

<div class="actions-container">
    <a href="add-experience.php" class="btn btn-success">Add Experience</a>
    <form method="POST" action="" class="delete-all-form">
        <button type="submit" name="delete_all" class="btn btn-danger" style="margin-left: 10px;">Delete All</button>
    </form>
</div>

<?php require_once "../includes/admin_footer.php"; ?>