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

<div id="wrapper" class="header-fix">
  <div id="header-wrapper">
    <div id="header">
      <div id="logo" style="margin-left:20px;">
        <a href="index.php"><img class="main_logo" src="./images/img_logo.png" alt="" style="margin-left:-30px;">
      </div>

      <!--
      <div class="language-group" role="group" >
        <input type="radio" class="language-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
        <label class="language language-outline-primary" for="btnradio1">eng</label>

        <input type="radio" class="language-check" name="btnradio" id="btnradio2" autocomplete="off">
        <label class="language language-outline-primary" for="btnradio2">kor</label>
       </div>
       -->
        <button type="button" class="button_signup demoupload" onClick="location.href='index.php?category=page/upload'">Demo Upload</button>


    </div><!-- end #header -->

  </div><!-- end #header-wrapper -->

  <div id="menu">
    <ul id="menu-ul">
      <li class="<?php echo (isset($_GET['category']) && ($_GET['category'] === 'page/home.php' || $_GET['category'] === '') ? 'current_page_item' : (!isset($_GET['category']) ? 'current_page_item' : '')); ?>"><a href="index.php" onclick="selectCategory('index.php')" style="width:150px;">ADC Study</a></li>
      <li class="<?php echo (isset($_GET['category']) && ($_GET['category'] === 'page/upload')) ? 'current_page_item' : ''; ?>"><a href="index.php?category=page/upload" >Try your X-ray</a></li>
      <li class="<?php echo (isset($_GET['category']) && ($_GET['category'] === 'page/case')) ? 'current_page_item' : ''; ?>"><a href="index.php?category=page/case" >Case Interpretation</a></li>
      <li class="<?php echo (isset($_GET['category']) && ($_GET['category'] === 'page/archives')) ? 'current_page_item' : ''; ?>"><a href="index.php?category=page/archives" >Case Archives</a></li>
      <li class="<?php echo (isset($_GET['category']) && ($_GET['category'] === 'page/public')) ? 'current_page_item' : ''; ?>"><a href="index.php?category=page/public" >Publications</a></li>

    </ul>
  </div><!-- end #menu -->
  </div>


<div id="wrapper">
<img src="./upload/1694142985130710/64fa920a2624eoutput/overlapedCB.png">
    </div>

</body>
</html>

