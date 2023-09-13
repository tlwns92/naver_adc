<!DOCTYPE html>
<html>
<head>
    <title>ADC STUDY</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <base href="/" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="http://fonts.googleapis.com/css?family=Abel" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
<?php



    if(isset($_GET['category'])) {
      $category = $_GET['category'];
      $filename = $category . ".php";

      if (file_exists($filename)) {
        include('common/header.php');
        echo "<div id='content-wrapper'>";
        include($filename);
        echo "</div>";


      } else {
        include('common/header.php');
        include('error.php');

      }
    } else {
      include('common/header.php');
      echo "<div id='content-wrapper'>";
      include('page/adcstudy.php');
      echo "</div></div></div>";
    }

?>
<br><br><br><br>
<?php include('common/footer.php') ?>




  <script src="script.js"></script>

</body>
</html>
