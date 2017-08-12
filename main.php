<?php

	require_once("config.php");
	set_time_limit(0);

	$responses = array(
		"Qué horror!",
		"Es malísimo",
		"Ya te vale",
		"Lo llamas chistes y no lo son",
		"El límite del humor",
		"Ains...",
        "Te superas cada día",
        "Tito, STAHP!",
        "RAUR?",
        "¡Qué bueno, Tito! Se lo paso a mi hijo de 5 años a ver si él si le hace gracia",
        ":facepalm:",
		);

	$lastUpdate = 0;
  	if (file_exists(dirname(__FILE__)."/titobot_index.txt")) {
    	$lastUpdate = file_get_contents(dirname(__FILE__)."/titobot_index.txt");
    	//if (is_numeric($lastUpdate)) {
      	//	$lastUpdate = intval($lastUpdate);
    	//}
  	}

	$urlGetUpdates = "https://api.telegram.org/bot" . $botID . "/getUpdates?offset=" . $lastUpdate;
	$result = json_decode(file_get_contents($urlGetUpdates),true);

	//print_r($result);

	$incrementLastUpdate = false;
    $titoJokes = array();

	foreach ($result["result"] as $key => $value) {   

        $username = isset($value["message"]["from"]["username"]) ? 
                          $value["message"]["from"]["username"] : 
                          $value["message"]["from"]["first_name"];

        echo $username . ": ";
        if (isset($value["message"]["text"])) {
            echo $value["message"]["text"];
        } else if (isset($value["message"]["sticker"])){
            echo "[sticker]";
        } else if (isset($value["message"]["photo"])){
            echo "[photo]";
        }
        echo "\n";
    	
    	if ($lastUpdate<=$value["update_id"]){
        	$lastUpdate = $value["update_id"];
        	$incrementLastUpdate = true;
    	} 	

    	if (isset($value["message"]["photo"]) && in_array($value["message"]["from"]["id"], $titoID)) {
    		$titoJokes[] = $value["message"]["message_id"];
    	}
    }
	
	if($incrementLastUpdate) {
		$lastUpdate++;
	}
    file_put_contents(dirname(__FILE__)."/titobot_index.txt", $lastUpdate);

    if (count($titoJokes)>0) {

        //print_r($titoJokes);

    	$response = $responses[rand(0, count($responses)-1)];
    	$messageID = $titoJokes[rand(0, count($titoJokes)-1)];

        $urlSendMessage = "https://api.telegram.org/bot" . $botID . "/sendMessage?text=".urlencode($response) . 
                          "&chat_id=" . $chatID . 
                          "&reply_to_message_id=" . $messageID;
        echo $urlSendMessage . "\n";
    	file_get_contents($urlSendMessage);
    }

