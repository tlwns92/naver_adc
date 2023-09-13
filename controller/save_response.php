<?php
include '../db.php';

$jsonData = json_decode(file_get_contents("php://input"), true);

foreach ($jsonData as $key => $data) {
    $status = $data['progress']['status'];
    $inputPath = $data['progress']['Input'];
     $absolute_folderPath = dirname($inputPath);
     $FolderPath = str_replace("/var/www/html/", "../", $absolute_folderPath);
     $FolderPath = rtrim($FolderPath, '/') . '/';
     $fileName = basename($inputPath);



    if ($status === "Success") {
        $age = $data['info']['age'];
	$gender = $data['info']['gender'];
	$z_score_token = $data['info']['z_token'];
	$CT_ratio = $data['zscoreCB']['CT_ratio'];
	$Carina_angle = $data['zscoreCB']['Carina_angle'];

        if ($z_score_token === "mm") {
        	$RT_upper_CB = $data['zscoreCB']['RtUpperCB_mm'];
        	$RT_lower_CB = $data['zscoreCB']['RtLowerCB_mm'];
        	$Aortic_knob = $data['zscoreCB']['AorticKnob_mm'];
        	$Pulmonary_conus = $data['zscoreCB']['PulmonaryConus_mm'];
        	$LA_appendage = $data['zscoreCB']['LAA_mm'];
        	$LT_lower_CB = $data['zscoreCB']['LtLowerCB_mm'];
        	$DAO = $data['zscoreCB']['DAO_mm'];
	}else if ($z_score_token === "N") {
		$RT_upper_CB = $data['zscoreCB']['RtUpperCB_N'];
                $RT_lower_CB = $data['zscoreCB']['RtLowerCB_N'];
                $Aortic_knob = $data['zscoreCB']['AorticKnob_N'];
                $Pulmonary_conus = $data['zscoreCB']['PulmonaryConus_N'];
                $LA_appendage = $data['zscoreCB']['LAA_N'];
                $LT_lower_CB = $data['zscoreCB']['LtLowerCB_N'];
                $DAO = $data['zscoreCB']['DAO_N'];
	}


        $CT_ratio = ($CT_ratio === '-') ? null : $CT_ratio;
	$RT_upper_CB = ($RT_upper_CB === '-') ? null : $RT_upper_CB;
	$RT_lower_CB = ($RT_lower_CB === '-') ? null : $RT_lower_CB;
	$Aortic_knob = ($Aortic_knob === '-') ? null : $Aortic_knob;
	$Pulmonary_conus = ($Pulmonary_conus === '-') ? null : $Pulmonary_conus;
	$LA_appendage = ($LA_appendage === '-') ? null : $LA_appendage;
	$LT_lower_CB = ($LT_lower_CB === '-') ? null : $LT_lower_CB;
	$DAO = ($DAO === '-') ? null : $DAO;
	$Carina_angle = ($Carina_angle === '-') ? null : $Carina_angle;

        $z_score = array(
            $CT_ratio,
            $RT_upper_CB,
            $RT_lower_CB,
            $Aortic_knob,
            $Pulmonary_conus,
            $LA_appendage,
            $LT_lower_CB,
            $DAO,
            $Carina_angle
	);

        $encoded_z_score = json_encode($z_score);

	if ($z_score_token === "mm") {
        	$value_CT_ratio = $data['indicatorCB']['CT_ratio'];
        	$value_RtUpperCB_mm = $data['indicatorCB']['RtUpperCB_mm'] ;
        	$value_RtLowerCB_mm = $data['indicatorCB']['RtLowerCB_mm'] ;
        	$value_AorticKnob_mm = $data['indicatorCB']['AorticKnob_mm'] ;
        	$value_PulmonaryConus_mm = $data['indicatorCB']['PulmonaryConus_mm'] ;
        	$value_LAA_mm = $data['indicatorCB']['LAA_mm'] ;
        	$value_LtLowerCB_mm = $data['indicatorCB']['LtLowerCB_mm'] ;
        	$value_DAO_mm = $data['indicatorCB']['DAO_mm'] ;
        	$value_Carina_angle = $data['indicatorCB']['Carina_angle'] ;
	}else if ($z_score_token === "N") {
                $value_CT_ratio = $data['indicatorCB']['CT_ratio'];
                $value_RtUpperCB_mm = $data['indicatorCB']['RtUpperCB_N'] ;
                $value_RtLowerCB_mm = $data['indicatorCB']['RtLowerCB_N'] ;
                $value_AorticKnob_mm = $data['indicatorCB']['AorticKnob_N'] ;
                $value_PulmonaryConus_mm = $data['indicatorCB']['PulmonaryConus_N'] ;
                $value_LAA_mm = $data['indicatorCB']['LAA_N'] ;
                $value_LtLowerCB_mm = $data['indicatorCB']['LtLowerCB_N'] ;
                $value_DAO_mm = $data['indicatorCB']['DAO_N'] ;
                $value_Carina_angle = $data['indicatorCB']['Carina_angle'] ;
	}


        $value_CT_ratio = ($value_CT_ratio === '-') ? null : $value_CT_ratio;
        $value_RtUpperCB_mm = ($value_RtUpperCB_mm === '-') ? null : $value_RtUpperCB_mm;
        $value_RtLowerCB_mm = ($value_RtLowerCB_mm === '-') ? null : $value_RtLowerCB_mm;
        $value_AorticKnob_mm = ($value_AorticKnob_mm === '-') ? null : $value_AorticKnob_mm;
        $value_PulmonaryConus_mm = ($value_PulmonaryConus_mm === '-') ? null : $value_PulmonaryConus_mm;
        $value_LAA_mm = ($value_LAA_mm === '-') ? null : $value_LAA_mm;
        $value_LtLowerCB_mm = ($value_LtLowerCB_mm === '-') ? null : $value_LtLowerCB_mm;
        $value_DAO_mm = ($value_DAO_mm === '-') ? null : $value_DAO_mm;
        $value_Carina_angle = ($value_Carina_angle === '-') ? null : $value_Carina_angle;

       $result_value = array(
                $value_CT_ratio,
                $value_RtUpperCB_mm,
                $value_RtLowerCB_mm,
                $value_AorticKnob_mm,
                $value_PulmonaryConus_mm,
                $value_LAA_mm,
                $value_LtLowerCB_mm,
                $value_DAO_mm,
                $value_Carina_angle

	);

	$encoded_result_value = json_encode($result_value);



        $query = "UPDATE upload
                  SET z_score_token = '$z_score_token' , z_score = '$encoded_z_score' , result_value = '$encoded_result_value'  , status = 'Complete' , age = '$age' , gender = '$gender'
                  WHERE upload_path = '$FolderPath' AND upload_ran_file = '$fileName'";

        if ($conn->query($query) !== TRUE) {
            echo "DB 업데이트 실패: " . $conn->error;
        }
    }else if ($status === "Fail") {
       $error_log = $data['progress']['error'];
       $logs = $data['progress']['logs'];
       $notice = $data['progress']['note'];

       $query = "UPDATE upload
                  SET  status = 'Fail' , logs = '$logs' , notice = '$notice'
                  WHERE upload_path = '$FolderPath' AND upload_ran_file = '$fileName'";
       if ($conn->query($query) !== TRUE) {
            echo "DB 업데이트 실패: " . $conn->error;
        }
    }
}

$conn->close();

?>
