<?php
session_start();

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

$query = "SELECT department_id, department_name FROM departments";
$result = mysqli_query($data, $query);
$departments = array();

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
}

$schoolQuery = "SELECT school_id, schoolANDdep FROM scoring ORDER BY schoolANDdep ASC";
$schoolResult = mysqli_query($data, $schoolQuery);

$schools = array();

if ($schoolResult && mysqli_num_rows($schoolResult) > 0) {
    while ($row = mysqli_fetch_assoc($schoolResult)) {
        $schools[] = $row;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departmentId = mysqli_real_escape_string($data, $_POST["department"]);
    $positionId = mysqli_real_escape_string($data, $_POST["position"]);
    $schoolId = mysqli_real_escape_string($data, $_POST["okul"]);

    $insertQuery = "INSERT INTO posts (department_id, position_id, school_id) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($data, $insertQuery);
    mysqli_stmt_bind_param($stmt, "iii", $departmentId, $positionId, $schoolId);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: view.php");
        exit();
    } else {
        // Form verileri kaydedilemedi. Hata mesajını göster.
        echo "Veritabanına kayıt sırasında bir hata oluştu: " . mysqli_error($data);
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#department").change(function() {
                var selectedDepartment = $(this).val();
                $.ajax({
                    url: "get_positions.php", // Pozisyonları çekecek PHP dosyasının yolunu doğrulayın
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

                var formData = {
                    department: $("#department").val(),
                    position: $("#position").val(),
                    okul: $("#okul").val()
                };

                $.ajax({
                    type: "POST",
                    url: "post.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    console.log(data); 
                    window.location.href = 'view.php';
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.log("AJAX çağrısı başarısız: " + textStatus, errorThrown);
                });
            });
        });
    </script>
</head>
<body>
    <h2>Yeni İşe Alım Girişi</h2>
    <form id="postForm">
        <label for="department">Departman Seçimi:</label>
        <select id="department" name="department" required>
            <option value="">Departman Seçin</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo htmlspecialchars($department['department_id']); ?>">
                    <?php echo htmlspecialchars($department['department_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
         <option value="">Pozisyon Seçin</option>
        <label for="position">Pozisyon Seçimi:</label>
        <select id="position" name="position" required>
            <option value="">Pozisyon Seçin</option>
        </select>
        <label for="okul">Okul Seçimi:</label>
        <select id="okul" name="okul" required>
            <option value="">Okul Seçin</option>
            <?php foreach ($schools as $school): ?>
                <option value="<?php echo htmlspecialchars($school['school_id']); ?>">
                    <?php echo htmlspecialchars($school['school_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Gönder</button>
    </form>
</body>
</html>
