<?php
session_start();


$host = "localhost";
$user = "root";
$password = ""; // MySQL veritabanı parolanızı buraya ekleyin
$db = "user";

$data = mysqli_connect($host, $user, $password, $db);
if ($data === false) {
    die("Connection Error: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $login = "SELECT * FROM login WHERE username = ?";
    $stmt = mysqli_prepare($data, $login);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if ($row["password"] === $password) {
            if ($row["userType"] == "user") {
                $_SESSION["name"] = $row["name"];
                header("Location: view.php");
                exit();
            } elseif ($row["userType"] == "admin") {
                echo "admin";
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
    }
}

mysqli_close($data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
