<?php
$filename = $_GET['filename'];
$file = '../test/uploads/' . $filename;

if (file_exists($file)) {
  header('Content-Type: application/zip');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  readfile($file);
} else {
  // 파일이 존재하지 않을 때의 처리
  echo 'File not found.';
}
?>
