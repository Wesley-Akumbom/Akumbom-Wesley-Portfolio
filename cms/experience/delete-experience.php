<?php require "../includes/admin_header.php"; ?>
<?php require "../../config/config.php"; ?>

<?php 

    session_start(); // Start the session

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to the login page if not logged in
        header("location: ".ADMINURL."");
        exit; // Ensure the script stops after redirection
    }

    if(isset($_POST['delete'])){

        $id = $_POST['id'];

        $delete = $conn->prepare("DELETE FROM experience WHERE id='$id'");
        $delete->execute();
    }

?>

<?php require "../includes/admin_footer.php"; ?>
