<?php

 const TOKEN = "PHPAPIMETA";
 const WEBHOOK_URL = "https://misitioweb8508.com/webhook.php";

 function verificarToken($req,$res){
    try{
        $token = $req['hub_verify_token'];
        $challenge = $req['hub_challenge'];

        if (isset($challenge) && isset($token) && $token === TOKEN) {
            $res->send($challenge);
        } else {
            $res->status(400)->send();
        }

    }catch(Exception $e){
        $res ->status(400)->send();
    }
}

function recibirMensajes($req, $res) {
    
    try {
        
        $entry = $req['entry'][0];
        $changes = $entry['changes'][0];
        $value = $changes['value'];
        $mensaje = $value['messages'][0];
        
        $comentario = $mensaje['text']['body'];
        $numero = $mensaje['from'];
        
        $id = $mensaje['id'];
        
        $archivo = "log.txt";
        
        if (!verificarTextoEnArchivo($id, $archivo)) {
            $archivo = fopen($archivo, "a");
            $texto = json_encode($id).",".$numero.",".$comentario;
            fwrite($archivo, $texto);
            fclose($archivo);
            
            EnviarMensajeWhastapp($comentario,$numero);
        }

        $res->header('Content-Type: application/json');
        $res->status(200)->send(json_encode(['message' => 'EVENT_RECEIVED']));

    } catch (Exception $e) {
        $res->header('Content-Type: application/json');
        $res->status(200)->send(json_encode(['message' => 'EVENT_RECEIVED']));
    }
}

function EnviarMensajeWhastapp($comentario,$numero){
    $comentario = strtolower($comentario);

    if (strpos($comentario,'hola') !==false){
        $data = json_encode([
            "messaging_product" => "whatsapp",    
            "recipient_type"=> "individual",
            "to" => $numero,
            "type" => "text",
            "text"=> [
                "preview_url" => false,
                "body"=> "¿Hola, en que podemos ayudarte? "
            ]
        ]);
    }else if ($comentario=='1') {
        $data = json_encode([
            "messaging_product" => "whatsapp",    
            "recipient_type"=> "individual",
            "to" => $numero,
            "type" => "text",
            "text"=> [
                "preview_url" => false,
                "body"=> "La Fundación Universitaria San Alfonso es una institución educativa de nivel superior ubicada en Colombia. Se centra en ofrecer programas académicos y de formación en diversas disciplinas, incluyendo carreras técnicas, tecnológicas y profesionales. La fundación se dedica a proporcionar una educación de calidad y fomentar el desarrollo académico y profesional de sus estudiantes. Además, puede tener una orientación religiosa debido a su nombre, en referencia a San Alfonso María de Ligorio, un santo de la Iglesia Católica"
            ]
        ]);
    }else if ($comentario=='2') {
        $data = json_encode([
            "messaging_product" => "whatsapp",    
            "recipient_type"=> "individual",
            "to" => $numero,
            "type" => "location",
            "location"=> [
                "latitude" => "4.626545",
                "longitude" => "-74.075867",
                "name" => "Fundación universitaria San Alfonso",
                "address" => "Bogotá"
            ]
        ]);
    }else if ($comentario=='3') {
        $data = json_encode([
            "messaging_product" => "whatsapp",    
            "recipient_type"=> "individual",
            "to" => $numero,
            "type" => "document",
            "document"=> [
                "link" => "https://www.sanalfonso.edu.co/wp-content/uploads/reglamento_estudiantil-1.pdf",
                "caption" => "Manual de convivencia"
            ]
        ]);
    }else if ($comentario=='4') {
        $data = json_encode([
            "messaging_product" => "whatsapp",    
            "recipient_type"=> "individual",
            "to" => $numero,
            "type" => "audio",
            "audio"=> [
                "link" => "https://filesamples.com/samples/audio/mp3/sample1.mp3",
            ]
        ]);
    }else if ($comentario=='5') {
        $data = json_encode([
            "messaging_product" => "whatsapp",
            "to" => $numero,
            "text" => array(
                "preview_url" => true,
                "body" => "Introducción al aplicativo web https://youtu.be/mG4LQ9OVuAo?si=B6C0CPXAmX6iVJES"
            )
        ]);
    }else if ($comentario=='6') {
        $data = json_encode([
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $numero,
            "type" => "text",
            "text" => array(
                "preview_url" => false,
                "body" => "🤝 En breve me pondré en contacto contigo. 🤓"
            )
        ]);
    }else if ($comentario=='7') {
        $data = json_encode([
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $numero,
            "type" => "text",
            "text" => array(
                "preview_url" => false,
                "body" => "📅 Horario de Atención: Lunes a Viernes. \n🕜 Horario: 9:00 a.m. a 5:00 p.m. 🤓"
            )
        ]);
    }else if (strpos($comentario,'gracias') !== false) {
        $data = json_encode([
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $numero,
            "type" => "text",
            "text" => array(
                "preview_url" => false,
                "body" => "Gracias a ti por contactarme. 🤩"
            )
        ]);
    }else if (strpos($comentario,'adios') !== false || strpos($comentario,'bye') !== false || strpos($comentario,'nos vemos') !== false || strpos($comentario,'adiós') !== false){
        $data = json_encode([
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $numero,
            "type" => "text",
            "text" => array(
                "preview_url" => false,
                "body" => "Hasta luego. 🌟"
            )
        ]);
    }else if (strpos($comentario,'gchatgpt:')!== false){
        $texto_sin_gchatgpt = str_replace("gchatgpt: ", "", $comentario);

        $apiKey = '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';

        $data = [
            'model' => 'text-davinci-003',
            'prompt' => $texto_sin_gchatgpt,
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

        $data = json_encode([
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $numero,
            "type" => "text",
            "text" => array(
                "preview_url" => false,
                "body" => $responseArr['choices'][0]['text']
            )
        ]);
    }else{
        $data = json_encode([
            "messaging_product" => "whatsapp",    
            "recipient_type"=> "individual",
            "to" => $numero,
            "type" => "text",
            "text"=> [
                "preview_url" => false,
                "body"=> "🚀 Hola, escoge una de las siguiente opciones para seguir con tu solicitud.\n \n📌Por favor, ingresa un número #️⃣ para recibir información.\n \n1️⃣. Información del aplicativo. ❔\n2️⃣. Ubicación del local. 📍\n3️⃣. Enviar temario en pdf. 📄\n4️⃣. Audio explicando del aplicativo. 🎧\n5️⃣. Video de Introducción. ⏯️\n6️⃣. Hablar con un asesor. 🙋‍♂️\n7️⃣. Horario de Atención. 🕜"
            ]
        ]);
    }

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-type: application/json\r\nAuthorization: Bearer EAARwgQoUsQcBOyRKuPjl7A3Gopmv4toP32cyrPXeQWrhMN04kFPaAflf6XdDfCkZBjWvZAQ4CJECwCRZBzdMvgczZAxqv9MCoDvtgRZBGC5Hhyf6rIJtAjuz0CXT97vzB9gMwXYu1wu5JVECrwtUgTkI9EDqygZCpj70RFby8aSf2kyeP3d2UTAksb4Y9aTN2RR2RJq1XLFxzXZA7CxAj4jUtHVDXqRFt2ZAIBwZD\r\n",
            'content' => $data,
            'ignore_errors' => true
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents('https://graph.facebook.com/v17.0/152142161317156/messages', false, $context);

    if ($response === false) {
        echo "Error al enviar el mensaje\n";
    } else {
        echo "Mensaje enviado correctamente\n";
    }
}

function verificarTextoEnArchivo($texto, $archivo) {
    $contenido = file_get_contents($archivo);
    
    if (strpos($contenido, $texto) !== false) {
        return true; // El texto ya existe en el archivo
    } else {
        return false; // El texto no existe en el archivo
    }
}


if ($_SERVER['REQUEST_METHOD']==='POST'){
    $input = file_get_contents('php://input');
    $data = json_decode($input,true);

    recibirMensajes($data,http_response_code());
    
}else if($_SERVER['REQUEST_METHOD']==='GET'){
    if(isset($_GET['hub_mode']) && isset($_GET['hub_verify_token']) && isset($_GET['hub_challenge']) && $_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === TOKEN_ANDERCODE){
        echo $_GET['hub_challenge'];
    }else{
        http_response_code(403);
    }
}
  if(isset($_GET['hub_mode']) && isset($_GET['hub_verify_token']) && isset($_GET['hub_challenge']) && $_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === TOKEN){
      echo $_GET['hub_challenge'];
  }else{
      http_response_code(403);
  }
?>