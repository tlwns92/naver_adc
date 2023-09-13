<?php
include '../db.php';

function mq($sql){
    global $conn;
    return $conn->query($sql);
}

$sessionId = session_id();

$sql = "SELECT * FROM upload WHERE session_id = '$sessionId' ORDER BY upload_num ASC";
$result = mq($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $uploadNum = $row['upload_num'];
        $uploadFile = $row['upload_file'];
        $uploadRanFile = $row['upload_ran_file'];
        $uploadPath = $row['upload_path'];
        $uploadTime = $row['upload_time'];
        $filePath = $uploadPath . $uploadRanFile;

        echo '<div class="record">';
        echo '<p>file Path: ' . $filePath . '</p>';
        echo '<div class="dicomViewerport" id="dicomViewerport-' . $uploadNum . '" style="height: 300px; width: 300px;"></div>';
        echo '</div>';
    }
} else {
    echo "Failed to fetch records.";
}

$conn->close();
?>
