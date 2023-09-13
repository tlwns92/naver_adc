<?php
include '/var/www/html/db.php';

function mq($sql){
    global $conn;
    return $conn->query($sql);
}

$sessionId = $_POST['sessionId'];

$sql = "SELECT * FROM upload WHERE session_id = '$sessionId' AND upload_day = CURDATE() ORDER BY upload_num DESC";
$result = mq($sql);

if ($result) {
    $output = '<div id="select-file">';
    $output .= '<nav aria-label="Page navigation example" style="padding-bottom: 1px; justify-content: center; padding: 0;">';
    $output .= '<ul class="pagination" style="display: flex; justify-content: center;">';

    $totalButtons = $result->num_rows;
    $buttonsPerPage = 5;
    $currentPage = 1;
    $totalPages = ceil($totalButtons / $buttonsPerPage);

    if (isset($_POST['page'])) {
        $currentPage = $_POST['page'];
    }

    $startButton = ($currentPage - 1) * $buttonsPerPage;
    $endButton = $startButton + $buttonsPerPage - 1;

    $output .= ($currentPage > 1) ? '<li class="page-item"><a class="page-link" onclick="changePage(' . ($currentPage - 1) . ')">&laquo;</a></li>' : '';

    for ($i = 0; $i < $startButton; $i++) {
        $row = $result->fetch_assoc();
    }

    for ($i = $startButton; $i <= $endButton; $i++) {
        if ($row = $result->fetch_assoc()) {
            $uploadNum = $row['upload_num'];
            $uploadFile = $row['upload_file'];
            $uploadRanFile = $row['upload_ran_file'];
            $uploadPath = $row['upload_path'];
            $uploadTime = $row['upload_time'];
            $filePath = $uploadPath . $uploadRanFile;

            $truncatedFileName = (strlen($uploadFile) > 15) ? substr($uploadFile, 0, 15) . '...' : $uploadFile;

            $buttonClass = ($i === $startButton) ? 'btn file-button current-file' : 'btn file-button';

            $output .= '<li class="page-item"><a class="page-link" style="padding:4.5px; 12px; display: block;">';
            $output .= '<button class="' . $buttonClass . '" id="' . $uploadNum . '" style="width: 100%; height: 100%; font-size: 15px; background-color: white; font-weight: bold;" onclick="markCurrentButton(this)">' . $truncatedFileName . '</button>';
            $output .= '</a></li>';
        } else {
            break;
        }
    }

    $output .= ($currentPage < $totalPages) ? '<li class="page-item"><a class="page-link" onclick="changePage(' . ($currentPage + 1) . ')">&raquo;</a></li>' : '';

    $output .= '</ul>';
    $output .= '</nav>';
    $output .= '</div>';
    if ($totalButtons > 0){
        $output .= '<div class="record" id="record">';
        $output .= '</div>';
    }
    $output .= '<input type="hidden" id="currentPage" value="' . $currentPage . '">';

} else {
    $output = "Failed to save file information to the database.";
}

echo $output;
?>

