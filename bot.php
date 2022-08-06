<?php 
    $cur_dir = dirname(__FILE__);
   
    require $cur_dir . '/config/config.php';
    require $cur_dir . '/libs/rb.php'; // ReadBean for DB
    require $cur_dir . '/database/db.php'; // Connect to db
    require $cur_dir . '/helpers/emoji.php'; // ReadBean for DB
    require $cur_dir . "/vendor/autoload.php"; // Telegram BOT

    $bot = new \TelegramBot\Api\Client(BOT_TOKEN);

    function getLastThreeGames($userid, $bot){
        $games = R::getAll("SELECT * FROM `freegames` ORDER BY `id` DESC LIMIT 3");
        $games = array_reverse($games);
        foreach ($games as $value) {
            $answer = getSomeEmoji() . "_Platform_ - *" . $value['platform'] . "*\nDistribution link👇";

            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                [
                    [
                        ['text' => 'GAME' . getSomeEmoji(), 'url' => $value['link']],
                    ]
                ]
            );
            try {
                if($value['photo']){
                    $bot->sendPhoto($userid, $value['photo'], $answer, null, $keyboard, false, "Markdown");
                }
                else{
                    $bot->sendMessage($userid, $answer, "Markdown", false, null, $keyboard);
                }
            } catch (Exception $e) {
                
            }
        }
    }
    
    $bot->command('start', function ($message) use ($bot) {
        $userid = $message->getChat()->getId();


        $username = $message->getChat()->getUsername();
        $firstname = $message->getChat()->getFirstName();
        $lastname = $message->getChat()->getLastName();

        $ids = R::exec('SELECT * FROM `users` WHERE `userid` = "' . $userid . '"');
        if(empty($ids)){
            $answer = "You have successfully subscribed to the mailing list of game giveaways.";
            $book = R::dispense( 'users' );
            $book->userid = $userid;
            $book->username = '@'.$username;
            $book->userrealname = trim($firstname.' '.$lastname);
            $id = R::store( $book );
        }
        else{
            $answer = "You are already subscribed.";
        }
        $bot->sendMessage($userid, $answer);
        if(empty($ids)){
            getLastThreeGames($userid, $bot);
        }

    });
    
    $bot->command('help', function ($message) use ($bot) {
        $countGames = count(R::getAll("SELECT * FROM `freegames`"));
        $answer = "From the start of the bot there were $countGames distribution(s).";
        $bot->sendMessage($message->getChat()->getId(), $answer);
    });

    $bot->command('get', function ($message) use ($bot) {
        getLastThreeGames($message->getChat()->getId(), $bot);
    });

    $bot->run();
    

?>