<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $targetDir = '/var/www/html/images/';
    $targetFile = $targetDir . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        echo "File uploaded successfully.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
