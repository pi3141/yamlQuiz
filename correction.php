<?php
header("Content-type: application/json");

function init(){
  $qcmName = $_POST['n'];
  $data = retrieveData('intro');
  //$data = retrieveData($qcm_name);
  echo $data;
}

function retrieveData($qcm_name){
    //echo "qcms/".$qcm_name.".json" ;
    if (file_exists("qcms/".$qcm_name.".json")){
      $data=file_get_contents("qcms/".$qcm_name.".json");
      return $data;
      //$data=file_get_contents("qcms/".$qcm_name.".json");
    } else {
      echo "Erreur n°432. Pas de questionnaire associé.";
      echo "qcms/".$qcm_name.".json";
      exit(432);
    }
}

init();

?>
