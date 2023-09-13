<?php
// 데이터베이스 정보
$servername = "localhost";
$username = "dbusersj";
$password = "alwjr1emdrmq!";
$dbname = "adcstudy";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 에러 처리
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

