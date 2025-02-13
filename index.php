<?php
// index.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['user_name'];
    $phone = $_POST['user_phone'];
    $presence_datetime = date('Y-m-d H:i:s');
    $otp = generateOTP();
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $db = getShortTermDB();
    $stmt = $db->prepare("INSERT INTO temp_attendance (user_name, user_phone, presence_datetime, otp_code, otp_expiry) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $phone, $presence_datetime, $otp, $otp_expiry);

    if ($stmt->execute()) {
        $id = $db->insert_id;
        header("Location: verify.php?id=" . $id);
        exit();
    } else {
        $error = "Error saving attendance";
    }

    $stmt->close();
    $db->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Attendance Form</title>
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
    <h2>Attendance Form</h2>
    <?php if (isset($error))
        echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <div class="form-group">
            <label for="user_name">Name:</label>
            <input type="text" id="user_name" name="user_name" required>
        </div>

        <div class="form-group">
            <label for="user_phone">Phone Number:</label>
            <input type="text" id="user_phone" name="user_phone" required>
        </div>

        <button type="submit">Submit Attendance</button>
    </form>
</body>

</html>