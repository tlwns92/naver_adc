<?php
$logMessage = $_POST['logMessage'];
$logFilePath = '/var/www/html/logs/abc_log.txt';
$formattedLogMessage = date('Y-m-d H:i:s') . ' ' . $logMessage;

// 로그 파일에 메시지를 추가합니다.
file_put_contents($logFilePath, $formattedLogMessage . PHP_EOL, FILE_APPEND);

// 응답을 보냅니다.
http_response_code(200);
?>
