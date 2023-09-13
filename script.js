// 중복확인 버튼 클릭시 실행되는 함수
function selectCategory(category) {
  window.location.href = category;
}
// get the current page URL and extract the category from it
var currentPageURL = window.location.href;
var category = currentPageURL.substring(currentPageURL.lastIndexOf('/')+1);



Dropzone.options.demoUpload = {
  maxFilesize: 30,
  init: function() {
    var self = this;
    var uploadPath; // 업로드 경로 변수
    var model_request_list=[];
    var model_request_nums=[];

    // maxFiles 옵션 추가
    this.options.maxFiles = 30;

    this.on("addedfiles", function(files) {
      // 파일이 추가될 때 실행되는 이벤트 핸들러
      var fileArray = Array.from(files); // 파일 배열

      var validFiles = [];
      var invalidFiles = [];

      // 파일을 확인하여 유효한 파일과 유효하지 않은 파일로 분류
      fileArray.forEach(function(file) {
        var extension = file.name.split(".").pop().toLowerCase();
        console.log("filename : " + file.name);
        console.log("ext : " + extension);
        if (extension === "dicom" || extension === "dcm" || extension === "png" || extension === "tiff" || extension === "jpg" || extension === "jpeg") {
          // 파일 크기가 20MB를 초과하지 않는 경우에만 유효한 파일로 분류
          if (file.size <= self.options.maxFilesize * 1024 * 1024) {
            validFiles.push(file);
            console.log("valid dicom file : " + file.name);
          } else {
            var logMessage =
              "Upload failed(dropzone): file size over filename - " +
              file.name +
              " , filesize - " +
              (file.size / (1024 * 1024)).toFixed(2) +
              "MB";
            doWriteLog(logMessage);
            invalidFiles.push(file);
          }

        } else {
          invalidFiles.push(file);
          console.log("invalid dicom file : " + file.name);
        }
      });

      if (validFiles.length === 0) {
        // 유효한 파일이 없을 때
        var logMessage =
          "Upload failed(dropzone): It's not a Dicom file  filename - " +
          invalidFiles.map((file) => file.name).join(", ");
        doWriteLog(logMessage);
        self.removeAllFiles(); // 모든 파일 제거
        alert("Only" + self.options.maxFilesize +"MB dicom or image files can be uploaded.");
        return;
      }

      if (validFiles.length > 100) {
        // 5개 이상의 유효한 파일을 추가하려고 할 때
        var logMessage =
          "Upload failed(dropzone): Upload File Count Exceeded ";
        doWriteLog(logMessage);
        self.removeAllFiles(); // 모든 파일 제거
        alert("You can upload up to 5 files at a time.");
        return;
      }

      // 임의의 폴더 경로 생성
      var timestamp = Date.now().toString();
      var randomNum = Math.floor(Math.random() * 1000).toString();
      uploadPath = "../upload/" + timestamp + randomNum + "/";
      console.log("upload path : " + uploadPath);
      console.log("valid file : " + validFiles);

       model_request_list = [];
       model_request_nums = [];
      // FormData 객체 생성
      var formData = new FormData();
      formData.append("uploadPath", uploadPath); // 업로드 경로 추가
      validFiles.forEach(function(file) {
       console.log("File:", file); // 파일 정보 출력
        formData.append("files[]", file); // 유효한 파일 추가
      });
      console.log("formData:", formData);
      doWriteLog(
        "script to upload_check.php: success - " +
          validFiles.map((file) => file.name).join(", ") +
          ", fail - " +
          invalidFiles.map((file) => file.name).join(", ")
      );




      // AJAX 요청을 통해 폴더 생성과 파일 이동 또는 복사 작업 수행
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "/controller/upload_check.php");

      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            console.log("Files uploaded successfully");
            try {
              var response = JSON.parse(xhr.responseText);
              if (response.status === "success") {
                console.log("File information saved to the database.");
                changePage(1);

                 response.uploadedFiles.forEach(function(uploadedFile) {
                     model_request_list.push(uploadedFile.filePath.replace('../', '/var/www/html/'));
		     model_request_nums.push(uploadedFile.uploadNum);
                 });



                var model_data = {};
                for (var i = 0; i < model_request_list.length; i++) {
                    model_data[model_request_nums[i]] = model_request_list[i];
                 }

                 var model_json_data = JSON.stringify(model_data, null, 4);
                  console.log(model_json_data);


                 var xhrModel = new XMLHttpRequest();
                 xhrModel.open("POST", "http://192.168.45.63:8862/inference");

                 xhrModel.onreadystatechange = function() {
                 if (xhrModel.readyState === XMLHttpRequest.DONE) {
                     if (xhrModel.status === 200) {
                           console.log("Model results received:", xhrModel.responseText);
                           var model_response = JSON.parse(xhrModel.responseText);
                           console.log(model_response);
                           sendDataToServer(model_response);
                     } else {
                           console.error("Error processing model inference");
                     }
                 }
                 };

                 xhrModel.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                 xhrModel.send(model_json_data);
                 changePage(1);

              } else {
                console.error(
                  "Failed to save file information to the database."
                );
              }
            } catch (error) {
              console.error("Error parsing server response: ", error);
            }
          } else {
            console.log("Error uploading files");
          }
        }
      };

      xhr.send(formData);


      // 유효하지 않은 파일 알림 출력
      if (invalidFiles.length > 0) {
        var invalidFileNames = invalidFiles.map(function(file) {
          return file.name;
        });
        var alertMessage =
          "Only " + self.options.maxFilesize +"MB dicom or image files can be uploaded.\nExcluded files: " +
          invalidFileNames.join(", ");
        alert(alertMessage);
      }
    });

    this.on("complete", function() {
      self.removeAllFiles();
    });
  },
};


function sendDataToServer(responseData) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "controller/save_response.php");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log("Response data saved successfully");
                 changePage(1);

            } else {
                console.error("Error saving response data");
            }
        }
    };

    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr.send(JSON.stringify(responseData));
}


function reanalyze() {
    var zscore_tag = document.getElementById("z-score-path");
    var zscore_path = "";
    if (zscore_tag) {
          zscore_path = zscore_tag.getAttribute("data-reanalyze-path");
    }
    var ageInput = $("#zscore-form input[type='text']");
    var age = parseInt(ageInput.val());
    var genderRadio = $("#zscore-form input[type='radio']:checked");
    var gender = genderRadio.val();

    if (isNaN(age) || age < 0 || age > 100) {
        alert("Please enter a valid age between 0 and 100.");
        ageInput.focus();
        return;
    }

    if (!genderRadio.length) {
        alert("Please select a gender.");
        return;
    }


    var requestData = {
        "1": [
            zscore_path,
            age,
            gender
        ]
    };


   console.log(requestData);

$.ajax({
        type: "POST",
        url: "http://192.168.45.63:8862/zscore",
        contentType: "application/json",
        data: JSON.stringify(requestData),
        success: function(respons) {
	var responseKey = Object.keys(respons)[0];
	var zscoreTypeNTag = document.getElementById("zscore-type-N");
	if (zscoreTypeNTag) {
                var RT_upper_CB = respons[responseKey].zscore.RtUpperCB_N ;
                var RT_lower_CB = respons[responseKey].zscore.RtLowerCB_N ;
                var Aortic_knob = respons[responseKey].zscore.AorticKnob_N ;
                var Pulmonary_conus = respons[responseKey].zscore.PulmonaryConus_N ;
                var LA_appendage = respons[responseKey].zscore.LAA_N ;
                var LT_lower_CB = respons[responseKey].zscore.LtLowerCB_N ;
                var DAO = respons[responseKey].zscore.DAO_N ;
	}else{
		var RT_upper_CB = respons[responseKey].zscore.RtUpperCB_mm ;
		var RT_lower_CB = respons[responseKey].zscore.RtLowerCB_mm ;
		var Aortic_knob = respons[responseKey].zscore.AorticKnob_mm ;
		var Pulmonary_conus = respons[responseKey].zscore.PulmonaryConus_mm ;
		var LA_appendage = respons[responseKey].zscore.LAA_mm ;
		var LT_lower_CB = respons[responseKey].zscore.LtLowerCB_mm ;
		var DAO = respons[responseKey].zscore.DAO_mm ;
        }
        var CT_ratio = respons[responseKey].zscore.CT_ratio;
	var Carina_angle = respons[responseKey].zscore.Carina_angle ;
	var zscore = [];

	zscore.push(CT_ratio);
	zscore.push(RT_upper_CB);
	zscore.push(RT_lower_CB);
	zscore.push(Aortic_knob);
	zscore.push(Pulmonary_conus);
	zscore.push(LA_appendage);
	zscore.push(LT_lower_CB);
	zscore.push(DAO);
	zscore.push(Carina_angle);

var graphCanvas = document.getElementById("graphCanvas");
if (graphCanvas) {
    graphCanvas.remove();
}

// 새로운 canvas 태그 생성 및 속성 설정


   var newCanvas = document.createElement("canvas");
   newCanvas.id = "graphCanvas";
   newCanvas.width = 700;
   newCanvas.height = 450;

// 새로운 canvas를 원하는 위치에 추가
var container = document.getElementById("graph-div");
if (container) {
    container.appendChild(newCanvas);
}

        console.log(respons);
        console.log("graph:"+zscore);
        drawGraph(zscore,age,gender);


        },
        error: function(xhr, status, error) {
            console.error("POST request failed");
            console.error(status, error);
        }
    });

}



function doWriteLog(logMessage) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "/controller/write_log.php");
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log("Log written successfully");
      } else {
        console.log("Error writing log");
      }
    }
  };

  xhr.send("logMessage=" + encodeURIComponent(logMessage));
}



function drawGraph(zScoreValue,age,gender) {
    var canvas = document.getElementById("graphCanvas");

    if (canvas) {
        var context = canvas.getContext("2d");


        // 그래프 크기 및 여백 설정
        var graphWidth = canvas.width - 200;
        var graphHeight = canvas.height - 40;
        var marginLeft = 150;
        var marginBottom = 40;
        var y_position = [170,130,90,50,10,-30,-70,-110,-150];
        var points = [];

        for(var i=0; i<9; i++){
          var x = zScoreValue[i];
          var y = y_position[i];

          if (x !== null){
            points.push({ x: x, y: y });
          }
        }
        context.fillStyle = "blue";
        context.strokeStyle = "black";
        context.font = "bold 16px Arial";
        context.fillStyle = "black";

	context.textAlign = "start";
	context.fillText("AGE: " + (age ? age : "Unknown"), canvas.width - 450, 20);
        context.fillText("GENDER: " + (gender ? gender : "Unknown"), canvas.width - 300, 20);



        // x 축 그리기
        context.beginPath();
        context.moveTo(marginLeft, canvas.height - marginBottom);
        context.lineTo(marginLeft + graphWidth, canvas.height - marginBottom);
        context.stroke();

        // x 축 눈금 표시
        context.textAlign = "center";

	 for (var i = -1; i <= 3; i++) {
                var x = marginLeft + (graphWidth / 4) * (i + 1);
                context.fillText(i.toString(), x, canvas.height - marginBottom + 20);
        }

	 // x 축 눈금 표시
        context.textAlign = "center";
        for (var i = -2; i <= 6; i++) {
            var x = marginLeft + (graphWidth / 8) * (i + 2);
            context.fillText((i * 0.5).toString(), x, canvas.height - marginBottom + 20);
        }

        // y 축 그리기 (중앙에 위치)
        context.beginPath();
        context.setLineDash([2, 2]);
        context.moveTo(marginLeft + 125, marginBottom);
        context.lineTo(marginLeft + 125, canvas.height - marginBottom );
        context.stroke();

         context.textAlign = "start";
        context.fillText("CT ratio", canvas.width - 700, 60);
        context.fillText("Rt upper CB", canvas.width - 700, 100);
        context.fillText("Rt lower CB", canvas.width - 700, 140);
        context.fillText("Aortic knob", canvas.width - 700, 180);
        context.fillText("Pulmonary conus", canvas.width - 700, 220);
        context.fillText("LA appendage", canvas.width - 700, 260);
        context.fillText("Lt lower CB", canvas.width - 700, 300);
        context.fillText("DAO", canvas.width - 700, 340);
        context.fillText("Carina angle", canvas.width - 700, 385);


        // 좌표값에 따라 점 그리기
	for (var i = 0; i < points.length; i++) {
    	    var point = points[i];
    	    var x = marginLeft + (graphWidth / 4) * (Math.min(Math.max(point.x, -1), 3) + 1);
            var y = canvas.height / 2 - (graphHeight / 200) * (point.y / 2);

    	    context.beginPath();
    	    if (point.x < -1) {
        	context.fillStyle = "red";
    	    } else if (point.x > 3) {
        	context.fillStyle = "blue";
    	    } else {
        	context.fillStyle = "black";
            }

    	    // 좌표 점 그리기
    	    context.arc(x, y, 5, 0, 2 * Math.PI);
    	    context.fill();
    	    context.strokeStyle = "black";
    	    context.stroke();

            context.textAlign = "center";
 	  if (point.x !== null) {
             context.fillText(point.x.toFixed(2), canvas.width - 20, y +4);

          } else {

          }
        }

    }
}


function disableRightClick() {
            $(document).bind("contextmenu", function(e) {
                return false;
            });
}

$(document).ready(function() {
            disableRightClick();
});





$(document).ready(function($) {

        $(".side-item-css").click(function(event){

                event.preventDefault();
		var targetOffset = $(this.hash).offset().top ;
                $('html,body').animate({scrollTop:targetOffset}, 500);

        });

});




const sidebar = document.getElementById("sidebar");

if(sidebar){
const sidebar = document.getElementById("sidebar");
const header = document.getElementById("header");
const footer = document.getElementById("footer");

let lastScrollPosition = 0;
const sidebarHeight = sidebar.clientHeight;
const footerHeight = footer.clientHeight;
const headerHeight = header.clientHeight;
let windowHeight = window.innerHeight;

const fixedTop = 1;

function handleScroll() {
    const scrollY = window.scrollY;

    if (scrollY >= headerHeight) {
        sidebar.style.position = "fixed";
        sidebar.style.top = `${fixedTop}px`;
    } else {
        sidebar.style.position = "static";
    }

    if (scrollY + windowHeight > windowHeight - footerHeight) {
        sidebar.style.top = `${windowHeight - sidebarHeight - footerHeight - fixedTop - 900 }px`;
    }

    lastScrollPosition = scrollY;
}

function handleResize() {
    windowHeight = window.innerHeight;
    handleScroll();
}

if (sidebar) {
    window.addEventListener("scroll", handleScroll);
    window.addEventListener("resize", handleResize); 
}

handleScroll();
}





/*
window.onload = function () {
    var img = document.getElementById("myImage");

    if (img) {
        console.log("hi");
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("modalImg");
        var imgElement = document.querySelector("img[alt='Image']");
        const span = document.querySelector(".close");

        imgElement.addEventListener('click', () => {
            modalDisplay("block");
            modalImg.src = imgElement.src;
        });

        span.addEventListener('click', () => {
            modalDisplay("none");
        });

        modal.addEventListener('click', () => {
            modalDisplay("none");
        });

        function modalDisplay(text) {
            modal.style.display = text;
        }
    }
};
*/



function openModal(imagePath) {
    var modalImg = document.getElementById("modalImg");
    modalImg.src = imagePath;
    var modal = document.getElementById("myModal");
    modal.style.display = "block";
}

function closeModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}




/*
let currentIndex = 0;
const images = document.querySelectorAll('.image');
const buttons = document.querySelectorAll('.button');

function showImage(index) {
    currentIndex += index;
    if (currentIndex < 0) {
        currentIndex = 0;
    } else if (currentIndex >= images.length) {
        currentIndex = images.length - 1;
    }

    images.forEach((image, i) => {
        if (i === currentIndex) {
            image.style.display = 'block';
        } else {
            image.style.display = 'none';
        }
    });

    buttons.forEach((button, i) => {
        if (i === currentIndex) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
}

// 초기에 첫 번째 이미지를 보이도록 설정
showImage(0);
*/

let currentGroup = 1; // 초기에 두 번째 그룹을 보이도록 설정
const images = document.querySelectorAll('.image');
const buttons = document.querySelectorAll('.button');

function showImage(index) {
    currentIndex = index;

    images.forEach((image, i) => {
        if (i === currentIndex) {
            image.style.display = 'block';
        } else {
            image.style.display = 'none';
        }
    });

    buttons.forEach((button, i) => {
        if (i === currentIndex) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
}

function showPrevGroup() {
    if (currentGroup === 2) {
        document.getElementById('button4').style.display = 'none';
 	document.getElementById('archive-prev').style.display = 'none';
        document.getElementById('button1').style.display = 'inline-block';
        document.getElementById('button2').style.display = 'inline-block';
        document.getElementById('button3').style.display = 'inline-block';
        document.getElementById('archive-next').style.display = 'none';
	showImage(0);
        currentGroup = 1;
    }
}

function showNextGroup() {
    if (currentGroup === 1) {
        document.getElementById('button1').style.display = 'none';
        document.getElementById('button2').style.display = 'none';
        document.getElementById('button3').style.display = 'none';
        document.getElementById('archive-next').style.display = 'none';
        document.getElementById('button4').style.display = 'inline-block';
        document.getElementById('archive-prev').style.display = 'inline-block';
        showImage(3); // 버튼 4번을 클릭한 것처럼 이미지 표시
        currentGroup = 2;
    }
}

const galleryElement = document.querySelector('.gallery');

// 요소가 존재하는지 확인합니다.
if (galleryElement) {
    // 요소가 존재하면 실행할 코드를 여기에 작성합니다.
    showNextGroup();
    showPrevGroup();
}
