<?php require "../includes/admin_header.php"; ?>
<?php require "../../config/config.php"; ?>

<?php 

    if(isset($_POST['delete'])){

        $id = $_POST['id'];

        $delete = $conn->prepare("DELETE FROM experience WHERE id='$id'");
        $delete->execute();
    }

?>

<?php require "../includes/admin_footer.php"; ?>
