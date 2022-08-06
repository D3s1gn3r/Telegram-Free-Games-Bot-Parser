<?php
    $cur_dir = dirname(__FILE__);

    require $cur_dir . '/config/config.php';
    require $cur_dir . '/libs/rb.php'; // ReadBean for DB
    require $cur_dir . '/database/db.php'; // Connect to db
    require $cur_dir . '/libs/simple_html_dom.php'; // Libraty to parse
    require $cur_dir . '/helpers/emoji.php'; // ReadBean for DB
    require $cur_dir . "/vendor/autoload.php"; // Telegram BOT

    $bot = new \TelegramBot\Api\Client(BOT_TOKEN);

    
    // notification to admin that bot is working
    $date = date('H:i');
    if (date('H:i') == '15:40') {
        try {
            $bot->sendMessage(ADMIN_TELEGRAM_CHAT_ID, 'Is working!😎');
        } catch (Exception $e) {
            echo 'Admin telegram chatId wrong.';
        }
    }

    $html = file_get_html('https://freesteam.ru');

    $ret = $html->find('article');
    $ret = array_reverse($ret);

    $users = R::getAll('SELECT * FROM `users` ORDER BY `id`');

    foreach ($ret as $value) {
        $link = $value->children[0]->children[0]->attr['href'];
        $checklink = R::exec('SELECT * FROM `freegames` WHERE `link` = "' . $link . '"');
        if (empty($checklink)) {
            $photo = $value->children[0]->children[0]->children[0]->attr['data-src'];
            $platform = $value->children[0]->children[2]->children[0]->children[0]->children[0]->plaintext;

            $answer = getSomeEmoji() . "_Platform_ - *" . $platform . "*\nDistribution link👇";
            
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                [
                    [
                        ['text' => 'GAME' . getSomeEmoji(), 'url' => $link],
                    ]
                ]
            );

            $book = R::dispense('freegames');
            $book->link = $link;
            $book->photo = $photo;
            $book->platform = $platform;
            $id = R::store($book);

            foreach ($users as $val) {
                try {
                    if ($photo) {
                        $msg = $bot->sendPhoto($val['userid'], $photo, $answer, null, $keyboard, false, "Markdown");
                    } else {
                        $msg = $bot->sendMessage($val['userid'], $answer, "Markdown", false, null, $keyboard);
                    }
                } catch (Exception $e) {
                    
                }
            }
        }
    }
?>