<?php
header('Content-Type: text/html; charset=utf-8'); // на всякий случай досообщим PHP, что все в кодировке UTF-8

$site_dir = dirname(dirname(__FILE__)).'/'; // корень сайта
$bot_token = "Псиать не буду, все равно домену не хотят ssl давать";
$data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
$data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив

        


// Для отладки, добавим запись полученных декодированных данных в файл message.txt, 
// который можно смотреть и понимать, что происходит при запросе к боту
// file_put_contents(__DIR__ . '/message.txt', print_r($data, true));

// Основной код: получаем сообщение, что юзер отправил боту и 
// заполняем переменные для дальнейшего использования
if (!empty($data['message']['text'])) {
    $chat_id = $data['message']['from']['id'];
    $user_name = $data['message']['from']['username'];
    $first_name = $data['message']['from']['first_name'];
    $last_name = $data['message']['from']['last_name'];
    $text = trim($data['message']['text']);
    $text_array = explode(" ", $text);
    
    if ($text == '/start') {
        $text_return = "Hi, $first_name $last_name, Here are some commands i know: 
/start - show the list of commands
/kurs - bitcoin to usd
";
        message_to_telegram($bot_token, $chat_id, $text_return);
    }
    elseif ($text == '/kurs') {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://mybtcproject.ru.host1879893.serv18.hostland.pro');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_responce = curl_exec($ch);
        curl_close($ch);
        $tag_begin = strpos($server_responce, '<p>')-1;
        $tag_end = strpos($server_responce, '</p>');
        $text_return = substr($server_responce,$tag_begin, $tag_end-$tag_begin);

        message_to_telegram($bot_token, $chat_id, $text_return);
    } else {
        message_to_telegram($bot_token, $chat_id, "Вы написали {$data['message']['text']}");
    }

}

// функция отправки сообщени в от бота в диалог с юзером
function message_to_telegram($bot_token, $chat_id, $text, $reply_markup = '')
{
    $ch = curl_init();
    $ch_post = [
        CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/sendMessage',
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_POSTFIELDS => [
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML',
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]
    ];

    curl_setopt_array($ch, $ch_post);
    curl_exec($ch);
}