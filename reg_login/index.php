<?php
$page_title = 'Home';
$page_header_title = 'Home Page';
include './includes/preferences.php';
include HEADER;
?>
<h3>
    Welcome <?php echo isset($_SESSION['full_name'])?'['.$_SESSION['full_name'].']':' guest'; ?>
</h3>
<?php include FOOTER; ?>