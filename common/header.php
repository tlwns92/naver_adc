
<script>
  function selectCategory(link) {
    var links = document.querySelectorAll('#menu li');
    for (var i = 0; i < links.length; i++) {
      links[i].classList.remove('current_page_item');
    }
    var current_link = document.querySelector(`#menu a[href="${window.location.pathname}"]`).parentNode;
    current_link.classList.add('current_page_item');
  }
</script>

<div id="wrapper" class="header-fix">
  <div id="header-wrapper">
    <div id="header">
      <div id="logo" style="margin-left:20px;">
        <a href="index.php"><img class="main_logo" src="./images/img_logo.png" alt="" style="margin-left:-30px;">
        <img class="under_logo" src="./images/under_logo.png" style="width: 950px; height: 45px; margin-top: -20px;"></a>
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
  </div><!-- end #wrapper -->

