<?php
// Veritabanı bağlantısı
$host = "localhost";
$user = "root";
$password = ""; // MySQL veritabanı parolanızı buraya ekleyin
$db = "user";

$data = mysqli_connect($host, $user, $password, $db);
if ($data === false) {
    die("Connection Error: " . mysqli_connect_error());
}

header('Content-Type: application/json'); // İçerik türünü JSON olarak belirt

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["department"])) {
    $selectedDepartment = mysqli_real_escape_string($data, $_POST["department"]);

    $query = "SELECT DISTINCT position_name, position_id FROM department_positions
              INNER JOIN positions ON department_positions.position_id = positions.position_id
              INNER JOIN departments ON department_positions.department_id = departments.department_id
              WHERE departments.department_name = ?";
    $stmt = mysqli_prepare($data, $query);
    mysqli_stmt_bind_param($stmt, "s", $selectedDepartment);

    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(array("error" => mysqli_error($data)));
        exit;
    }

    $result = mysqli_stmt_get_result($stmt);
    $positions = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $positions[] = $row;
    }

    if (count($positions) > 0) {
        echo json_encode($positions);
    } else {
        echo json_encode(array("message" => "No positions found for the selected department."));
    }
} else {
    echo json_encode(array("error" => "Invalid request"));
}

mysqli_close($data);



