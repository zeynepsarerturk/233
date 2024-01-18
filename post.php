<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION["name"])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$db = "user";

$data = mysqli_connect($host, $user, $password, $db);
if ($data === false) {
    die("Connection Error: " . mysqli_connect_error());
}

$query = "SELECT DISTINCT department_name FROM departments";
$result = mysqli_query($data, $query);
$departments = array();

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row["department_name"];
    }
}
$schoolQuery = "SELECT DISTINCT schoolANDdep FROM scoring ORDER BY schoolANDdep ASC";
$schoolResult = mysqli_query($data,$schoolQuery);

$schools = array();

if ($schoolResult && mysqli_num_rows($schoolResult) > 0) {
        while ($row = mysqli_fetch_assoc($schoolResult)) {
            $schools[] = $row["schoolANDdep"];
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $department = mysqli_real_escape_string($data, $_POST["department"]);
        $position = mysqli_real_escape_string($data, $_POST["position"]);
        $school = mysqli_real_escape_string($data, $_POST["school"]);
    
        $insertQuery = "INSERT INTO posts (department, position, schoolANDdep) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($data, $insertQuery);
        mysqli_stmt_bind_param($stmt, "sss", $department, $position, $school);
        $result = mysqli_stmt_execute($stmt);
        if ($result) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => mysqli_error($data)]);
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Post Oluşturma</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"> </script>
</head>
<body>
    <h2>Yeni İşe Alım Girişi</h2>
    <form id="postForm">
        <label for="department">Departman Seçimi:</label>
        <select id="department" name="department" required>
            <option value="">Departman Seçin</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo htmlspecialchars($department); ?>">
                    <?php echo htmlspecialchars($department); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="position">Pozisyon Seçimi:</label>
        <select id="position" name="position" required>
            <option value="">Pozisyon Seçin</option>
        </select>
        <label for="school">Okul Seçimi:</label>
        <select id="school" name="school" required>
            <option value="">Okul Seçin</option>
            <?php foreach ($schools as $school): ?>
                <option value="<?php echo htmlspecialchars($school); ?>">
                    <?php echo htmlspecialchars($school); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Gönder</button>
    </form>

    <script>
        $(document).ready(function() {
            $("#department").change(function() {
                var selectedDepartment = $(this).val();
                $.ajax({
                    url: "get_positions.php",
                    method: "POST",
                    data: { department: selectedDepartment },
                    dataType: "json",
                    success: function(data) {
                        $("#position").empty();
                        if (data.error) {
                            $("#position").append($('<option>', {
                                value: '',
                                text: 'Pozisyon bulunamadı'
                            }));
                        } else {
                            $.each(data, function(index, value) {
                                $("#position").append($('<option>', {
                                    value: value.position_id,
                                    text: value.position_name
                                }));
                            });
                        }
                    }
                });
            });

            $("#postForm").submit(function(event) {
                event.preventDefault();
                var SelectedDepartment = $("#department").val(); 
                var SelectedPosition = $("#position").val(); 
                var SelectedSchool = $("#school").val(); 

                $.ajax({
                    url: "post.php",
                    type: "POST",
                    data: { department: SelectedDepartment, position:SelectedPosition, school: SelectedSchool},
                    dataType: "json",
                    success: function(data) {
                        if (data.success) {
                            window.location.href = 'login.php';
                        } else {
                            console.log("Error: " + data.error);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log("Error " + textStatus, errorThrown);
                    }
                });
            });
        });
    </script>
</html>
