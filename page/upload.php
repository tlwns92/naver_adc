<?php
$cookieName = 'adcstudy_id';
$cookieExpiry = time() + (60 * 60 * 24 * 30);

$adcstudyId = isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName] : '';

if (empty($adcstudyId)) {
    $adcstudyId = uniqid();
    setcookie($cookieName, $adcstudyId, $cookieExpiry, '/');
}

include '/var/www/html/db.php';

function mq($sql){
    global $conn;
    return $conn->query($sql);
}

$sql = "SELECT COUNT(*) AS recordCount FROM upload WHERE session_id = '$adcstudyId' AND upload_day = CURDATE()";
$result = mq($sql);
$row = $result->fetch_assoc();
$recordCount = $row['recordCount'];

$maxUploadCount = 100;

if ($recordCount >= $maxUploadCount) {
    $uploadDisabled = true;
    $uploadMessage = "You have reached the maximum upload limit.";
} else {
    $uploadDisabled = false;
    $uploadMessage = "";
}



?>

<style>
    h2 {
        font-size: 32px;
    }

    li {
        font-size: 22px;
        margin-bottom: 10px;
    }
</style>

<div id="content-wrapper">
    <div id="page">
        <div id="content">
            <h2><strong>Upload criteria</strong></h2>
            <ul>
                <li style="font-size:20px;"> Uploadable extensions: dcm, dicom, tiff, png, jp(e)g<br></li>
		<li  style="font-size:20px;"> If it's not a DICOM file, obtaining the diameter is not possible; therefore, values normalized by the thoracic diameter (e.g., CT ratio, left lower CB ratio) and the corresponding z-score based on it are provided.</li>
                <li  style="font-size:20px;">Up to 5 30mb files can be uploaded at once.</li>
                <li  style="font-size:20px;"> <strong>Delete</strong> uploaded files after <strong>1 hour.</strong></li>
            </ul><br>

            <div id="dropzone">
                <form action="/controller/upload_check.php" class="dropzone needsclick" id="demo-upload">
                    <div class="dz-message needsclick">
                        <span class="text">
                            <img src="../images/dicom_img.png" />
                            <strong>Drop Chest DICOM files here or click to upload.</strong><br>
                            <!--<strong>today upload : <?php echo $recordCount; ?>/<?php echo $maxUploadCount; ?></strong>-->
                        </span>
                        <span class="plus">+</span>
                    </div>
                </form>
                <div id="select-file" style="text-align: center;"></div>
            </div>
        </div></div>

<script>

function sendPostRequest(url, data, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", url, true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    callback(xhr.responseText);
                } else {
                    console.error("POST request failed with status:", xhr.status);
                }
            }
        };
        xhr.send(data);
    }


 function changePage(page) {
        var sessionId = <?php echo json_encode($adcstudyId); ?>;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/controller/paging.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    document.getElementById("select-file").innerHTML = xhr.responseText;

                    // 첫 번째 버튼의 ID 값을 viewer.php로 전송하여 record에 출력
                    var firstButton = document.querySelector(".file-button");
                    if (firstButton) {
                        var uploadNum = firstButton.id;
                        //var data = "uploadNum=" + uploadNum;
                        var data = "uploadNum=" + uploadNum + "&sessionId=" + sessionId;
                        sendPostRequest("../controller/viewer.php", data, function (response) {
                            document.getElementById("record").innerHTML = response;
                             var zScoreContainer = document.getElementById("z-score-container");
			     var ageElement = document.getElementById('z-score-age');
		             var genderElement = document.getElementById('z-score-gender');
                             if (zScoreContainer) {
                                var zScoreValue = zScoreContainer.getAttribute("data-z-score");
				var age =  ageElement.dataset.age;
                                var gender =  genderElement.dataset.gender;

                                drawGraph(JSON.parse(zScoreValue),age,gender);
                             } else {
                             }

                        });
                    }
                } else {
                    console.error("페이지 업데이트에 실패했습니다. 상태 코드:", xhr.status);
                }
            }
        };
        xhr.send("sessionId=" + sessionId + "&page=" + page);
    }

changePage(1);



document.addEventListener("click", function (event) {
    if (event.target.classList.contains("file-button")) {
        var uploadNum = event.target.id;
        var sessionId = <?php echo json_encode($adcstudyId); ?>;
        var data = "uploadNum=" + uploadNum + "&sessionId=" + sessionId;
        sendPostRequest("../controller/viewer.php", data, function (response) {
            document.getElementById("record").innerHTML = response;
	     var zScoreContainer = document.getElementById("z-score-container");
             var ageElement = document.getElementById('z-score-age');
             var genderElement = document.getElementById('z-score-gender');
             if (zScoreContainer) {
                var zScoreValue = zScoreContainer.getAttribute("data-z-score");
                var age =  ageElement.dataset.age;
                var gender =  genderElement.dataset.gender;


                drawGraph(JSON.parse(zScoreValue),age,gender);
            } else {
            }
        });
    }

});

function markCurrentButton(button) {
    // Remove 'current-file' class from all buttons
    var buttons = document.getElementsByClassName('file-button');
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove('current-file');
    }

    // Add 'current-file' class to the clicked button
    button.classList.add('current-file');
}

function disableDropzone() {
var demoUploadForm = document.getElementById("demo-upload");
demoUploadForm.classList.add("disabled");

// Dropzone의 기능을 제거하는 코드 작성
// 예시: Dropzone 인스턴스를 얻어서 destroy 메소드 호출
var dropzoneInstance = Dropzone.forElement("#demo-upload");
dropzoneInstance.destroy();

// 비활성화 메시지 표시
var dropzoneMessage = document.querySelector("#demo-upload .text strong");
dropzoneMessage.innerText = "You have reached the maximum upload limit.";
}



//window.addEventListener("load", function() {
  //var recordCount = <?php echo $recordCount; ?>; // PHP에서 가져온 $recordCount 값을 JavaScript 변수에 할당

  //if (recordCount >= 5) {
    //var demoUploadForm = document.getElementById("demo-upload");
    //demoUploadForm.classList.add("disabled");
    //disableDropzone();

    // 비활성화 메시지 표시
    //var dropzoneMessage = document.querySelector("#demo-upload .text strong");
//  }
//});


</script>

