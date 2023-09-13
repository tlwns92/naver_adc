<?php
include '/var/www/html/db.php';

function mq($sql){
    global $conn;
    return $conn->query($sql);
}

if (isset($_POST['uploadNum'])) {
    $uploadNum = $_POST['uploadNum'];
    $sessionId = $_POST['sessionId'];
    $sql = "SELECT * FROM upload WHERE upload_num = '$uploadNum' and session_id = '$sessionId'";
    $result = mq($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $age = $row['age'];
	    $gender = $row['gender'];
            $uploadRanFile = $row['upload_ran_file'];
            $uploadFile = $row['upload_file'];
            $uploadRan = pathinfo($uploadRanFile, PATHINFO_FILENAME);
            $uploadPath = $row['upload_path'];
            $DICOMPath = $uploadPath . $uploadRan . '_output/'.$uploadRan.'_dicom_image.png';
            $AnalyzedPah = $uploadPath . $uploadRan . '_output/'.$uploadRan.'_overlapedCB.png';
	    $modalDICOMPath = $uploadPath . $uploadRan . '_output/'.$uploadRan.'_modal_dicom_image.png';
	    $modalAnalyzedPah = $uploadPath . $uploadRan . '_output/'.$uploadRan.'_modal_overlapedCB.png';
            $status = $row['status'];
	    $z_score_token = $row['z_score_token'];
            $Z_score = $row['z_score'];
            $result_Value = $row['result_value'];
            $reanlyze_path= str_replace("../", "/var/www/html/", $uploadPath). $uploadRanFile;

            if  ($status == "Analyzing") {
                echo '<div class="view-container1" id="view-container1" style="height: 400px; width: 980px; text-align: center; display: flex; justify-content: center; align-items: center;">';
                echo '<div class="dicom-div" id="dicom-div" style="height: 100%; width: 100%; text-align: center;">';
                echo '<img src="../images/analyzing_icon.gif" alt="analyzing GIF" style="width: 300px;">';
                echo '<h2 style="margin-top:-30px;">Analyzing</h2>';
                echo '<h3>Please wait a moment.</h3>';
                echo '</div>';
                echo '</div>';
            }
            else if ($status == "Fail") {
                echo '<div class="view-container1" id="view-container1" style="height: 450px; width: 980px; text-align: center; display: flex; justify-content: center; align-items: center;">';
                echo '<div class="dicom-div" id="dicom-div" style="height: 100%; width: 100%; text-align: center;">';
                echo '<h2 style="margin-top:150px; color: red;">Analysis failed.</h2>';
                echo '<h3>I don\'t think this file is a dicom file or a chest dicom.</h3>';
                echo '</div>';
                echo '</div>';

            }
            else if ($status == "Complete") {
		if ($z_score_token === "mm") {
                	echo '<div class="view-container1" id="view-container1" style="height: 1500px; width: 980px; ">';
		}elseif ($z_score_token === "N") {
    			echo '<div class="view-container1" id="view-container1" style="height: 1530px; width: 980px; ">';
		}

		echo '<div class="dicom-div" id="dicom-div" style="height: 550px; width: 470px;  justify-content: center; align-items: center;  display: inline-block; margin-right:5px;">';
                echo '<h2 style="display: block;">&lt;Dicom Image&gt;</h2>';
                echo '<img src="' . $DICOMPath . '" alt="Image" class="img" style="width: 470px; height: 470px;" onclick="openModal(\'' . $modalDICOMPath . '\')">';
                echo '</div>';

                echo '<div class="analyzed-div" id="analyzed-div" style="height: 550px; width: 470px; text-align: center;  display: inline-block; margin-left:5px;">';
                echo '<h2 style="display: block;">&lt;Analyzed Image&gt;</h2>';
                echo '<img src="' . $AnalyzedPah . '" alt="Image" class="img"  style="width: 470px; height: 470px;" onclick="openModal(\'' . $modalAnalyzedPah . '\')">';
                echo '</div>';


		echo '<div id="myModal" class="modal" style="display: none;" onclick="closeModal()">';
		echo '<div id="modal-wrapper">';
		echo '<div class="close-css" onclick="closeModal()"><img src="./images/close_button.png" style="width:80px; height:80px;"></div>';
		echo '<img class="modal-content" id="modalImg"></div></div>';




                echo '<div class="graph-div" id="graph-div" style="justify-content: center; align-items: center;  display: inline-block;">';
                echo '<div id="z-score-container" data-z-score="' . htmlspecialchars($Z_score) . '"></div>';
                echo '<div id="z-score-age" data-age="' . htmlspecialchars($age) . '"></div>';
		echo '<div id="z-score-gender" data-gender="' . htmlspecialchars($gender) . '"></div>';
                echo '<div id="z-score-path" data-reanalyze-path="' . htmlspecialchars($reanlyze_path) . '"></div>';



/*echo '<h2 style="display: block;">&lt;Information/Interpretation&gt</h2>';
echo '<p style="font-size:18px;"><strong>- 50/F with atrial septal defect </strong></p>';
echo '<p style="font-size:18px;"><strong>- Enlarged pulmonary conus (z-score 1.26) </strong></p>';
echo '<p style="font-size:18px;"><strong>- Decreased size of aortic knob (z-score -1.61) </strong></p><br>';*/




                echo '<h2 style="display: block;">&lt; Z - Score &gt;</h2>';


		if ($z_score_token === "N") {
    			echo '<p id="zscore-type-N"; style="font-size:18px; font-weight: bold;">&#8251;&nbsp; The outputs of uploaded file are provided as a Secondary measure(Normalized CB).</p>';
		}





		echo '<div id="zscore-form" style="align-items: center; height:120px;  display: inline-block; ">';
		echo ' <h4>You can search the zscore value for your desired age and gender.</h4>';
		echo '    <div id="zscore-age" class="input-group input-group-sm" style="align-items: center; width:200px; display: inline-block; bottom:10px;  margin-left:50px; ">';
		echo '        <div class="input-group-text" id="inputGroup-sizing-sm" style="border: none; display: inline-block;">Age</div>';
		echo '        <input type="text" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" style="width:70px; display: inline-block;">';
		echo '    </div>';
		echo '    <div id="zscore-gender" style="width:100px; margin-left:-30px;  margin-right: 50px; display: inline-block;">';
		echo '        <div class="form-check form-check-inline">';
		echo '            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="M">';
		echo '            <label class="form-check-label" for="inlineRadio1">Male</label>';
		echo '        </div>';
		echo '        <div class="form-check form-check-inline">';
		echo '            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="F">';
		echo '            <label class="form-check-label" for="inlineRadio2">Female</label>';
		echo '        </div>';
		echo '    </div>';
		echo '    <button id="btn-reanalyze" onclick="reanalyze()" class="btn btn-primary" style=" margin-bottom:30px;  margin-right:120px; display: inline-block;">Reanalyze</button>';
		echo '</div>';

                echo '<canvas id="graphCanvas" width="700" height="450"></canvas>';
                echo '</div>';

               $result_value_array = json_decode($result_Value, true);


                echo '<div class="table-div" id="tablediv" style="justify-content: center; align-items: center;  display: inline-block;">';
                echo '<h2 style="display: block;">&lt; Measurements &gt;</h2>';
                echo '<table style="width: 900px;">';
		echo '<thead>';
		echo '<tr>';
		echo '<th style="width: 200px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CT_ratio</th>';
		if ($z_score_token === "mm") {
			echo '<th style="width: 200px;">&nbsp;&nbsp;&nbsp;&nbsp;Rt upper CB(<span style="text-transform: lowercase;">mm</span>)</th>';
			echo '<th style="width: 200px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rt lower CB(<span style="text-transform: lowercase;">mm</span>)</th>';
			echo '<th style="width: 200px;">&nbsp;&nbsp;&nbsp;Aortic knob(<span style="text-transform: lowercase;">mm</span>)</th>';
		}elseif($z_score_token === "N"){
                        echo '<th style="width: 200px;">&nbsp;&nbsp;&nbsp;&nbsp;Rt upper CB(<span style="text-transform: lowercase;">ratio</span>)</th>';
                        echo '<th style="width: 200px;">&nbsp;&nbsp;Rt lower CB(<span style="text-transform: lowercase;">ratio</span>)</th>';
                        echo '<th style="width: 200px;">&nbsp;&nbsp;&nbsp;Aortic knob(<span style="text-transform: lowercase;">ratio</span>)</th>';
		}
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
                echo '<td>' . ($result_value_array[0] === null ? '-' : number_format((float)$result_value_array[0], 2)) . '</td>';
                echo '<td>' . ($result_value_array[1] === null ? '-' : number_format((float)$result_value_array[1], 2)) . '&nbsp;</td>';
                echo '<td>' . ($result_value_array[2] === null ? '-' : number_format((float)$result_value_array[2], 2)) . '&nbsp;</td>';
                echo '<td>' . ($result_value_array[3] === null ? '-' : number_format((float)$result_value_array[3], 2)) . '&nbsp;</td>';
		echo '</tr>';
		echo '</tbody>';
                echo '</table>';
                echo '<table class="measure_table" style="width : 900px; margin-top:-0.01px;">';
		echo '<thead>';
		echo '<tr>';
                if ($z_score_token === "mm") {
			echo '<th>Pulmonary Conus(<span style="text-transform: lowercase;">mm</span>)</th>';
			echo '<th>&nbsp;&nbsp;&nbsp;LAA(<span style="text-transform: lowercase;">mm</span>)</th>';
			echo '<th>&nbsp;&nbsp;Lt Lower CB(<span style="text-transform: lowercase;">mm</span>)</th>';
			echo '<th>&nbsp;&nbsp;&nbsp;DAO(<span style="text-transform: lowercase;">mm</span>)</th>';
		}elseif($z_score_token === "N"){
                        echo '<th>Pulmonary Conus(<span style="text-transform: lowercase;">ratio</span>)</th>';
                        echo '<th>LAA(<span style="text-transform: lowercase;">ratio</span>)</th>';
                        echo '<th>&nbsp;&nbsp;Lt Lower CB(<span style="text-transform: lowercase;">ratio</span>)</th>';
                        echo '<th>&nbsp;&nbsp;&nbsp;DAO(<span style="text-transform: lowercase;">ratio</span>)</th>';
		}
		echo '<th>Carina angle(<span style="text-transform: lowercase;">degree</span>)</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
                echo '<td>' . ($result_value_array[4] === null ? '-' : number_format((float)$result_value_array[4], 2)) . '&nbsp;</td>';
                echo '<td>&nbsp;&nbsp;' . ($result_value_array[5] === null ? '-' : number_format((float)$result_value_array[5], 2)) . '</td>';
                echo '<td>' . ($result_value_array[6] === null ? '-' : number_format((float)$result_value_array[6], 2)) . '&nbsp;</td>';
                echo '<td>&nbsp;&nbsp;' . ($result_value_array[7] === null ? '-' : number_format((float)$result_value_array[7], 2)) . '&nbsp;</td>';
                echo '<td>' . ($result_value_array[8] === null ? '-' : number_format((float)$result_value_array[8], 2)) . '&nbsp;</td>';
		echo '</tr>';
		echo '</tbody>';
		echo '</table>';
                echo '</div>';
                echo '</div>';
            }
        }
    }
}

?>


