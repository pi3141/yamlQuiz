<?php

function init(){
  $qcm_name = $_GET["n"];
  $data = retrieveData($qcm_name);
  $html = buildQCM($data);
  dispPage($html);
}

function buildQCM($data){
  $html = <<<EOF
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>  
EOF;
  $html .= $data['title'];
  $html .= <<<EOF
  </title>

  <style>
    body {
    font-family: Arial, sans-serif;
    }
    .question {
    margin-bottom: 30px;
    }
    label {
    display: block;
    margin-top: 5px;
    }
    #results {
    margin-top: 30px;
    font-weight: bold;
    }
  </style>
</head>
<body>

EOF;
  $html .= "  <h1>".$data['title']."</h1>\n";
  $questionIndex = 0;
  $shuffledQuestionsData = $data['questions'];
  shuffle($shuffledQuestionsData);
  $html .= "    <form id=\"qcmForm\" method=\"POST\" action=\"".htmlspecialchars($_SERVER["PHP_SELF"])."?n=".$_GET['n']."\">\n";
  foreach ($shuffledQuestionsData as $question){
    $questionIndex++;
    $questionNumber=array_search($question,$data['questions']);
    $html .= "      <fieldset class=\"question\" data-type=\"".$question['answerType']."\">\n      <legend>Question ".$questionIndex."</legend>\n";
    $html .= "        <p id=\"statement\">".$question['statement']."</p>\n";
    switch ($question['answerType']){
      case 'choices':
          $isFirst = true;
          $choiceIndex = a;
          foreach ($question['choices'] as $choice){
            $html .= "        <label><input type=\"radio\" id=\"q".$questionNumber.$choiceIndex."\" name=\"q".$questionNumber."\" value=\"".$choice."\">".$choice."</label>\n";
            $choiceIndex++;
          }
        break;
      case 'multiChoices':
        $allChoices=array_merge($question['correctChoices'],$question['otherChoices']);
        shuffle($allChoices);
        foreach ($allChoices as $choice){
          $html .= "        <label><input type=\"checkbox\" id=\"q".$questionNumber.$choiceIndex."\" name=\"q".$questionNumber."\" value=\"".$choice."\">".$choice."</label>\n";
        }
      break;
      case 'numericalValue':
        $html .= "        <input type=\"text\" name=\"q".$questionNumber."\" class=\"numericalValue\">";
      break;
      case 'textEntry':
        $html .= "        <input type=\"text\"  name=\"q".$questionNumber."\" class=\"textEntry\">";
      break;
    }
    //$html .= "        <button onclick=\"checkAnswer(".$questionNumber.")\" data-q-nr=\"".$questionNumber."\" data-type=\"".$question['answerType']."\">Vérifier</button>\n";
    $html .= "      </fieldset>";
  }
  $html .= "<input type=\"hidden\" name=\"qcmName\" value=\"".$_GET['n']."\">";
  $html .= "      <button type=\"submit\">Valider</button>\n    </form>";
  $html .= <<<EOF
  <div id="result"></div>
  <script>
  var selections = [];
let i=0;
document.querySelectorAll('fieldset.question').forEach(
  (el)=>{
    var q = el.querySelector('#statement');
    var mc = [];
    var c = "";
    var selection = {}
    switch (el.dataset.type){
        case 'multiChoices':
            el.querySelectorAll('input[type="checkbox"]:checked').forEach((e)=>{mc.push(e.value);});
            selection={"question":q,"selectedAnswer":mc};
            break;
        case 'choice':
            c = el.querySelector('input[type="radio"]:checked').value;
            selection={"question":q,"selectedAnswer":c};
            break;
            //TODO : continue
        case 'textEntry':
            break;
        case 'numericalValue':
            break;
    }    
    selections.push(selection);
  }
)
  fetch('correction.php', {
    method: 'POST',
    body: JSON.stringify( selections )
  })
  //.then(response => response.json())
  .then(data => {
      console.log("Received data:", data);
  //    // do something with the received data
  })
  .catch((error) => {
    console.error('Error:', error);
  });
  
</script>
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