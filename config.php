<?php
// config.php
define('SHORT_TERM_DB_HOST', 'localhost');
define('SHORT_TERM_DB_USER', 'root');
define('SHORT_TERM_DB_PASS', '');
define('SHORT_TERM_DB_NAME', 'shorttermdatabase');

define('LONG_TERM_DB_HOST', 'localhost');
define('LONG_TERM_DB_USER', 'root');
define('LONG_TERM_DB_PASS', '');
define('LONG_TERM_DB_NAME', 'longtermdatabase');

function getShortTermDB()
{
    $conn = new mysqli(SHORT_TERM_DB_HOST, SHORT_TERM_DB_USER, SHORT_TERM_DB_PASS, SHORT_TERM_DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function getLongTermDB()
{
    $conn = new mysqli(LONG_TERM_DB_HOST, LONG_TERM_DB_USER, LONG_TERM_DB_PASS, LONG_TERM_DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Helper function to generate OTP
function generateOTP()
{
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}