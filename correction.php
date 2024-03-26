<?php

$qcmName = $_POST['name'] ?? null;

if ($qcmName && file_exists('qcms/' . $qcmName . '.json')) {
    $data = json_decode(file_get_contents("qcms/" . $_POST['name'] . ".json"), true);
    unset($_POST['name']);

    $response = [];

    foreach ($_POST as $questionName => $answer) {
        $correctionQuestions = (array_filter($data['questions'], function($question) use ($questionName) {
            return 'q' . $question['id'] === $questionName;
        }));

        $correctionQuestion = $correctionQuestions ? array_pop($correctionQuestions) : null;

        switch ($correctionQuestion['answerType']) {
            case 'textEntry':
                $response[$questionName] = $correctionQuestion && in_array($answer, $correctionQuestion['answer']);
                continue 2;
            case 'numericalValue':
                $answer = (int)$answer;
                break;
            case 'multiChoices':
                asort($answer);
                asort($correctionQuestion['answer']);
                $response[$questionName] = $correctionQuestion && array_values($correctionQuestion['answer']) === array_values($answer);
                continue 2;
        }

        $response[$questionName] = $correctionQuestion && $correctionQuestion['answer'] === $answer;
    }

    echo json_encode($response);
}
