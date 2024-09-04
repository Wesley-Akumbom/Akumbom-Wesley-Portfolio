<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

// Initialize variables
$errors = [];
$message = '';

// Fetch the profile_id
$stmt = $conn->prepare("SELECT id FROM Profile LIMIT 1");
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_OBJ);

if (!$profile) {
    $errors[] = "No profile found. Please create a profile first.";
}

$profile_id = $profile ? $profile->id : null;

// Fetch the projects
$stmt = $conn->prepare("SELECT * FROM projects WHERE profile_id = :profile_id");
$stmt->execute([':profile_id' => $profile_id]);
$projects = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<h1>Projects</h1>

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

<?php if ($profile_id && !empty($projects)): ?>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>GitHub URL</th>
                <th>Live URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($project->image); ?>" alt="<?php echo htmlspecialchars($project->title); ?>" style="max-width: 100px;"></td>
                    <td><?php echo htmlspecialchars($project->title); ?></td>
                    <td><a href="<?php echo htmlspecialchars($project->github_url); ?>" target="_blank"><?php echo htmlspecialchars($project->github_url); ?></a></td>
                    <td><a href="<?php echo htmlspecialchars($project->website_url); ?>" target="_blank"><?php echo htmlspecialchars($project->website_url); ?></a></td>
                    <td>
                        <a href="update-project.php?id=<?php echo $project->id; ?>" class="btn btn-primary">Update</a>
                        <a href="delete-project.php?id=<?php echo $project->id; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div>
    <a href="delete-all-projects.php" class="btn btn-danger" style="margin-top: 20px;">Delete All Projects</a>
<?php endif; ?>

<a href="create-project.php" class="btn btn-primary" style="margin-top: 20px;">Add a new project</a>
    </div>

<?php if ($profile_id && empty($projects)): ?>
    <p>No projects found.</p>
<?php elseif (!$profile_id): ?>
    <p>Please create a profile before adding projects.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>