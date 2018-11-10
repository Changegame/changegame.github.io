<?php
include "vk_api.php"; //Подключаем библиотеку для работы с api vk

//**********CONFIG**************
const VK_KEY = "aa9ce2fa46d08311248d24e9b522933239b0292cfa6e2924bd924b310f3415d228ff960930642993a3ebe"; //тот самый длинный ключ доступа сообщества
const ACCESS_KEY = "2c28f63f"; //например c40b9566, введите свой
const VERSION = "5.87"; //ваша версия используемого api
//******************************

const BTN_SALMON = [["animals" => 'Pink_salmon'], "Хочу похудеть", "green"]; // Код кнопки 'Горбуша'
const BTN_GOLDFISH = [["animals" => 'Goldfish'], "Ретушь фото", "white"]; // Код кнопки 'Золотая рыбка'

$vk = new vk_api(VK_KEY, VERSION); // создание экземпляра класса работы с api, принимает ключ и версию api
$data = json_decode(file_get_contents('php://input')); //Получает и декодирует JSON пришедший из ВК

if ($data->type == 'confirmation') { //Если vk запрашивает ключ
	exit(ACCESS_KEY); //Завершаем скрипт отправкой ключа
}

$vk->sendOK(); //Говорим vk, что мы приняли callback

if (isset($data->type) and $data->type == 'message_new') { //Проверяем, если это сообщение от пользователя
	$id = $data->object->from_id; //Получаем id пользователя, который написал сообщение
	$message = $data->object->text;

	if (isset($data->object->peer_id))
        $peer_id = $data->object->peer_id; // Получаем peer_id чата, откуда прилитело сообщение
    else
        $peer_id = $id;
	
	if (isset($data->object->payload)){  //получаем payload
        	$payload = json_decode($data->object->payload, True);
   	} else {
      		$payload = null;
   	}
  
	if ($payload != null) { // если payload существует
			switch ($payload['animals']) { //Смотрим что в payload кнопках
				case 'Fish': //Если это Fish
					$vk->sendButton($peer_id, 'Вот такие, выбирай', [ //Отправляем кнопки пользователю
						[BTN_SALMON, BTN_GOLDFISH],
						[BTN_BACK]
					]);
					break;
				case 'Pink_salmon': //Если это Горбуша
					$vk->sendMessage($peer_id, "Квест создан!"); //отправляем сообщение
					$vk->sendImage($peer_id, "img/pink_salmon.jpg"); //отправляем картинку
					break;
				case 'Goldfish': //Если это Золотая рыбка
					$vk->sendMessage($peer_id, "Заполните форму по ссылке");
					$vk->sendImage($peer_id, "img/goldfish.jpg");
					break;
				default:
					break;
			}
		}
	
}
?>