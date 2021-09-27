<?php

namespace App\Services;


use App\Services\Exceptions\EmptyDozensException;
use App\Services\Exceptions\DozenNotAllowedException;

class Lottery {

    const MIN_RAND_DOZEN = 1;

    const MAX_RAND_DOZEN = 60;

    const DOZEN_RESULTS_QUANTITY = 6;

    const AVAILABLE_DOZENS_OPTIONS = [6,7,8,9,10];

    private $dozens;

    private $results = [];
    
    private $resultPerGame = [];

    private $gamesQuantity;

    private $games = [];

    public function __construct(int $dozens, int $gamesQuantity)
    {
        $this->setDozens($dozens);
        $this->setGamesQuantity($gamesQuantity);
    }

    public function setResult(array $results){
        $this->results = $results;
    }

    public function getResult(bool $join = false){
        if($join){
            return join(', ', $this->results);
        }
    }

    public function setDozens(int $dozens){
        $this->dozens = $dozens;
    }
    
    public function getDozens(){
        return $this->dozens;
    }

    public function setGamesQuantity(int $gamesQuantity){
        $this->gamesQuantity = $gamesQuantity;
    }
    
    public function getGamesQuantity(){
        return $this->gamesQuantity;
    }

    public function addGame(array $game){
        $this->games[] = $game;
    }

    public function getGames(bool $stringfy = false){
        if($stringfy){
            return array_map(function($value){
                return join(', ', $value);
            }, $this->games);
        }

        return $this->games;
    }

    public function addResult(array $result){
        $this->results[] = $result;
    }

    private function generateRandomDozens(int $quantity, bool $sort = true){
        
        $randomDozens = [];

        if(in_array($quantity, self::AVAILABLE_DOZENS_OPTIONS)){
            while(count($randomDozens) < $quantity){
                $randValue = rand(self::MIN_RAND_DOZEN, self::MAX_RAND_DOZEN);
                if(!in_array($randValue, $randomDozens)){
                    $randomDozens[] = $randValue;
                }
            }
        }else{
            throw new DozenNotAllowedException();
        }

        if($sort){
            sort($randomDozens);
        }

        return $randomDozens;
    }

    public function generateDozens(int $quantity){
        $randomDozens = [];

        if(!empty($quantity)){
            $randomDozens = $this->generateRandomDozens($quantity);
        }else{
            throw new EmptyDozensException('');
        }

        return $randomDozens;
    }

    public function generateGames(){
        $quantity = $this->getGamesQuantity();
        for($i = 0; $i < $quantity; $i++){
            $game = $this->generateDozens($quantity);
            $this->addGame($game);
        }
    }

    public function generateResults(){
        $quantity = self::DOZEN_RESULTS_QUANTITY;
        $results = $this->generateDozens($quantity);
        $this->setResult($results);
    }

    protected function defineResultsPerGame(){
        foreach($this->games as $index => $game){
            $intersects = array_intersect($game, $this->results);
            $this->resultPerGame[$index] = count($intersects);
        }
    }

    public function play(){
        $this->generateGames();
        $this->generateResults();
        $this->defineResultsPerGame();
    }

    public function exportResult(){
        $html  = '<!DOCTYPE html>';
        $html .= '<html lang="en">';
        $html .= '<head>';
        $html .= '    <meta charset="UTF-8">';
        $html .= '    <meta http-equiv="X-UA-Compatible" content="IE=edge">';
        $html .= '    <meta name="viewport" content="width=device-width, initial-scale=1.0">';
        $html .= '    <title>My Results</title>';
        $html .= '    <style>table, tr, td, th{border:1px solid black;text-align:center}</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '    <table>';
        $html .= '        <tr>';
        $html .= '            <th>Horário: '.date('d/m/Y H:i:s').'</th>';
        $html .= '            <th></th>';
        $html .= '            <th>Resultado: '.$this->getResult(true).'</th>';
        $html .= '        </tr>';
        $html .= '        <tr>';
        $html .= '            <th>Jogo</th>';
        $html .= '            <th>Dezenas</th>';
        $html .= '            <th>Resultado</th>';
        $html .= '        </tr>';

        foreach($this->getGames(true) as $index => $game){
            $html .= '        <tr>';
            $html .= '            <td>'.($index+1).'</td>';
            $html .= '            <td>'.$game.'</td>';
            $html .= '            <td>'.$this->resultPerGame[$index].' acerto(s)</td>';
            $html .= '        </tr>';
        }

        $html .= '    </table>';
        $html .= '</body>';
        $html .= '</html>';

        echo $html;
    }

}