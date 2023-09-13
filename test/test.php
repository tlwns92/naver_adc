<!doctype html>
<html lang="ko">
  <head>
    <meta charset="utf-8">
    <title>PHP</title>
  </head>
  <body>
<?php
  if ( $_POST[ 'action' ] == "Upload" ) {
    print_r( $_FILES[ 'myfile' ] );
    echo "<br>";
    echo $_FILES[ 'myfile' ][ 'name' ];
    echo "<br>";
    echo $_FILES[ 'myfile' ][ 'type' ];
    echo "<br>";
    echo $_FILES[ 'myfile' ][ 'size' ];
    echo "<br>";
    echo $_FILES[ 'myfile' ][ 'tmp_name' ];
    echo "<br>";
    echo $_FILES[ 'myfile' ][ 'error' ];

    $uploaded_file_name_tmp = $_FILES[ 'myfile' ][ 'tmp_name' ];
    $uploaded_file_name = $_FILES[ 'myfile' ][ 'name' ];
    $upload_folder = "uploads/";
    move_uploaded_file( $uploaded_file_name_tmp, $upload_folder . $uploaded_file_name );
    echo "<p>" . $uploaded_file_name . "을(를) 업로드하였습니다.</p>";
  }
?>
<form action="" method="POST" enctype="multipart/form-data">
  <p><input type="file" name="myfile"></p>
  <p><input type="submit" name="action" value="Upload"></p>
</form>

<button>upload.php</button>
<button>upload_check.php</button>
<button>paging.php</button>
<button>viewer.php</button>


  </body>
</html>
