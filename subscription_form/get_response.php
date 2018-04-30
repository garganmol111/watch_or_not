<?php
require_once("config.php");
if((isset($_POST['your_name'])&& $_POST['your_name'] !='') && (isset($_POST['your_email'])&& $_POST['your_email'] !=''))
{
    #require_once("contact_mail.php");

$yourName = $conn->real_escape_string($_POST['your_name']);
    $yourEmail = $conn->real_escape_string($_POST['your_email']);
    
    $sql="INSERT INTO subscription (name, email) VALUES ('".$yourName."','".$yourEmail."')";


    if(!$result = $conn->query($sql)){
        die('There was an error running the query [' . $conn->error . ']');
    }
    else
    {
        echo "Thank you! You will now receive emails from us!";
    }
}
else
{
    echo "Please fill Name and Email";
}
?>