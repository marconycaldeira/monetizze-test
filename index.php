<?php

require_once(__DIR__ . '/Services/Exceptions/DozenNotAllowedException.php');
require_once(__DIR__ . '/Services/Exceptions/EmptyDozensException.php');
require_once(__DIR__ .'/Services/Lottery.php');


use App\Services\Lottery;


$game = new Lottery(6, 8);
$game->play();
$game->exportResult();