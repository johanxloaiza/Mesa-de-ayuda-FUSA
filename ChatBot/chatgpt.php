<?php
    $apiKey = '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';

    $data = [
        'model' => 'text-davinci-002',
        'prompt' => 'comenta algo de colombia',
        'temperature' => 0.7,
        'max_tokens' => 300,
        'n' => 1,
        'stop' => ['\n']
    ];

    $ch = curl_init('https://api.openai.com/v1/completions');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ));

    $response = curl_exec($ch);
    $responseArr = json_decode($response, true);

    echo $responseArr['choices'][0]['text'];
?>