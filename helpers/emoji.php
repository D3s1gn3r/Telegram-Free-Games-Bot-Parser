<?php

	// prepare several emoji to send to user
    function getSomeEmoji()
    {
        $emojiStr = '';
        $countEmoji = count(EMOJI) - 1;
        for ($i = 0; $i < 2; $i++) {
            $emojiStr .= EMOJI[rand(0, $countEmoji)];
        }
        return $emojiStr;
    }