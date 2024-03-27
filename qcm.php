<?php

function init(){
  $qcm_name = $_GET["n"];
  $data = retrieveData($qcm_name);
  $html = buildQCM($data, $qcm_name);
  dispPage($html);
}

function buildQCM($data, $qcm_name){
  $html = <<<EOF
<!DOCTYPE html>
<html lang="fr">
<head> 
  <meta charset="UTF-8">
  <link rel="stylesheet" href="style.css">
  <script src="script.js"></script>
  <title>  
EOF;
  $html .= $data['title'];
  $html .= <<<EOF
  </title>
</head>
<body>

EOF;
  $html .= "  <h1>".$data['title']."</h1>\n";
  $questionIndex = 0;
  $shuffledQuestionsData = $data['questions'];
  shuffle($shuffledQuestionsData);
  $html .= "    <form id=\"qcmForm\" data-name=\"" . $qcm_name . "\">\n";
  foreach ($shuffledQuestionsData as $question){ 
    $questionIndex++;
    $questionNumber = $question['id'];
    $questionId = 'q' . $questionNumber;
    $html .= "      <fieldset class=\"question\" data-name=\"".$questionId."\">\n      <legend>Question ".$questionIndex."</legend>\n";
    $html .= "        <p>".$question['statement']."</p>\n";
    switch ($question['answerType']){
      case 'choices':
          $isFirst = true;
          $allChoices=array_merge([$question['answer']],$question['otherChoices']);
          foreach ($allChoices as $choiceIndex => $choice){
            $html .= "        <label><input type=\"radio\" id=\"q".$questionNumber.$choiceIndex."\" name=\"".$questionId."\" value=\"".$choice."\">".$choice."</label>\n";
          }
        break;
      case 'multiChoices':
        $allChoices=array_merge($question['answer'],$question['otherChoices']);
        shuffle($allChoices);
        foreach ($allChoices as $choiceIndex => $choice){
          $html .= "        <label><input type=\"checkbox\" id=\"q".$questionNumber.$choiceIndex."\" name=\"".$questionId."[]\" value=\"".$choice."\">".$choice."</label>\n";
        }
      break;
      case 'numericalValue':
        $html .= "        <input type=\"text\" name=\"".$questionId."\" class=\"numericalValue\">";
      break;
      case 'textEntry':
        $html .= "        <input type=\"text\"  name=\"".$questionId."\" class=\"textEntry\">";
      break;
    }
    //$html .= "        <button onclick=\"checkAnswer(".$questionNumber.")\" data-q-nr=\"".$questionNumber."\" data-type=\"".$question['answerType']."\">Vérifier</button>\n";
    $html .= "      </fieldset>";
  }
  $html .= "      <button>Valider</button>\n    </form>";
  $html .= <<<EOF
  <div id="result"></div>
</body>
</html>
EOF;
  return $html;
}

function retrieveData($qcm_name){
  if (file_exists("qcms/".$qcm_name.".json")){
    $data=json_decode(file_get_contents("qcms/".$qcm_name.".json"),true);
    return $data;
  } else {
    echo "Erreur n°432. Pas de questionnaire associé.";
    echo "qcms/".$qcm_name.".json";
    exit(432);
  }
}

function dispPage($html){
  echo $html;
}

$qcm_name = init();
?>