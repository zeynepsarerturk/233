<?php
session_start();

if(!isset($_SESSION["name"])) {
   header("Location: login.php");
   exit();
}
else
{
    echo "Hoş geldiniz, " . $_SESSION["name"] . "!";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>View</title>
</head>
<body>
    <button id="newPostButton" class="new-post-button">
        <div class="plus-icon">+</div>
        <div class="button-text">Yeni İşe Alım Girişi</div>
    </button>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        var newPostButton = document.getElementById('newPostButton');
        newPostButton.addEventListener('click', function() {
        window.location.href = 'post.php';
    });
});
</script>


</body>
</html>
