<?php
ini_set("display_errors", 1);
session_start();
$sessionId = $_COOKIE['adcstudy_id'];
$response = [
    'status' => 'success',
    'message' => '',
    'uploadedFiles' => []
];



if (!empty($_FILES) && isset($_POST['uploadPath'])) {
    $uploadPath = $_POST['uploadPath'];
    $fileArray = $_FILES['files'];

    if (isset($_SESSION['adc_ip'])) {
        $adcIp = $_SESSION['adc_ip'];
    } else {
        $adcIp = $_SERVER['REMOTE_ADDR'];
        $_SESSION['adc_ip'] = $adcIp;
    }

    include '../db.php';

    function mq($sql) {
        global $conn;
        return $conn->query($sql);
    }

    $recordCount = 0;
    $count_sql = "SELECT COUNT(*) AS recordCount FROM upload WHERE session_id = '$sessionId'";

    foreach ($fileArray['tmp_name'] as $key => $tmpFile) {
        $fileName = $fileArray['name'][$key];
	$extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $randomFileName = uniqid() . "." . $extension;
        $targetFile = $uploadPath . $randomFileName;

        $result = mq($count_sql);
        $row = $result->fetch_assoc();

        if ($row) {
            $recordCount = $row['recordCount'];
        }

        if (!is_dir($uploadPath) && !empty($tmpFile)) {
           mkdir($uploadPath, 0777);
	       chmod($uploadPath, 0777);
        }

       if($recordCount <100){
        if (move_uploaded_file($tmpFile, $targetFile)) {
            $sql = "INSERT INTO upload (session_id, upload_file, upload_ran_file, upload_path,upload_day ,upload_time, status, download_cnt, user_ip) VALUES ('$sessionId', '$fileName', '$randomFileName', '$uploadPath',CURDATE() ,CURTIME(), 'Analyzing', 0, '$adcIp')";
            $result = $conn->query($sql);

            if ($result) {
                $uploadNum = $conn->insert_id;
                $response['uploadedFiles'][] = [
                    'filePath' => $targetFile,
                    'uploadNum' => $uploadNum
                ];

                doWriteLog("File Upload successful(upload_check.php): tmpFile - $tmpFile, filePath - $targetFile");
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to save file information to the database.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'File Upload failed';
            $errorMessage = "File Upload failed(upload_check.php): tmpFile - $tmpFile, targetFile - $targetFile, error - " . $_FILES['files']['error'][$key];
            doWriteLog($errorMessage);
        }
        }/*하루 recordCount가 5미만인 경우*/
    }
    $conn->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error uploading files';
}

header('Content-Type: application/json');
echo json_encode($response);

function doWriteLog($str) {
    $logPath = '/var/www/html/logs/abc_log.txt';
    $fp = fopen($logPath, 'a');
    fwrite($fp, date('Y-m-d H:i:s') . ' ' . $str . PHP_EOL);
    fclose($fp);
}
?>
