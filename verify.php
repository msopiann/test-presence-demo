<?php
// verify.php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_otp = $_POST['otp'];
    $db = getShortTermDB();

    $checkStmt = $db->prepare("SELECT * FROM temp_attendance WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $data = $checkResult->fetch_assoc();

    if ($data && $data['otp_code'] === $submitted_otp && strtotime($data['otp_expiry']) > time()) {
        // Transfer data to long-term database
        $longDb = getLongTermDB();
        $insertStmt = $longDb->prepare("INSERT INTO attendance_records (user_name, user_phone, presence_datetime) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $data['user_name'], $data['user_phone'], $data['presence_datetime']);

        if ($insertStmt->execute()) {
            // Delete from short-term database
            $deleteStmt = $db->prepare("DELETE FROM temp_attendance WHERE id = ?");
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();

            header("Location: success.php");
            exit();
        } else {
            $error = "Error processing attendance";
        }

        $insertStmt->close();
        $longDb->close();
    } else {
        if (!$data) {
            $error = "Invalid verification request";
        } elseif (strtotime($data['otp_expiry']) <= time()) {
            $error = "OTP has expired";
        } else {
            $error = "Invalid OTP code";
        }
    }

    $db->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Verify Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
        }

        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2>Verify Your Attendance</h2>
    <?php if (isset($error))
        echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <div class="form-group">
            <label for="otp">Enter OTP Code:</label>
            <input type="text" id="otp" name="otp" required maxlength="6">
            <small>Please enter the 6-digit OTP code (expires in 5 minutes)</small>
        </div>

        <button type="submit">Verify</button>
    </form>
</body>

</html>