<?php

namespace App\Services;


use App\Services\Exceptions\EmptyDozensException;
use App\Services\Exceptions\DozenNotAllowedException;

class Lottery {

    const MIN_RAND_DOZEN = 1;
    const MAX_RAND_DOZEN = 60;
    const DOZEN_RESULTS_QUANTITY = 6;
    const AVAILABLE_DOZENS_OPTIONS = [6,7,8,9,10];

    /**
     *
     * @var int
     */
    private $dozens;
    /**
     *
     * @var array
     */
    private $results = [];
    /**
     *
     * @var array
     */    
    private $resultPerGame = [];
    /**
     *
     * @var int
     */
    private $gamesQuantity;
    /**
     *
     * @var array
     */
    private $games = [];

    /**
     * __construct
     *
     * @param integer $dozens
     * @param integer $gamesQuantity
     */
    public function __construct(int $dozens, int $gamesQuantity)
    {
        $this->setDozens($dozens);
        $this->setGamesQuantity($gamesQuantity);
    }
    /**
     * setResult
     *
     * @param array $results
     * @return void
     */
    public function setResult(array $results):void
    {
        $this->results = $results;
    }
    /**
     * getResult
     *
     * @param boolean $join
     * @return string|array
     */
    public function getResult(bool $join = false)
    {
        if($join){
            return join(', ', $this->results);
        }

        return $this->results;
    }

    /**
     * setDozens
     *
     * @param integer $dozens
     * @return void
     */
    public function setDozens(int $dozens):void
    {
        $this->dozens = $dozens;
    }
    
    /**
     * getDozens
     *
     * @return integer
     */
    public function getDozens(): int
    {
        return $this->dozens;
    }

    /**
     * setGamesQuantity
     *
     * @param integer $gamesQuantity
     * @return void
     */
    public function setGamesQuantity(int $gamesQuantity): void
    {
        $this->gamesQuantity = $gamesQuantity;
    }
    /**
     * getGamesQuantity
     *
     * @return integer
     */
    public function getGamesQuantity():int
    {
        return $this->gamesQuantity;
    }
    /**
     * addGame
     *
     * @param array $game
     * @return void
     */
    public function addGame(array $game):void
    {
        $this->games[] = $game;
    }

    /**
     * getGames
     *
     * @param boolean $stringfy
     * @return array|string
     */
    public function getGames(bool $stringfy = false)
    {
        if($stringfy){
            return array_map(function($value){
                return join(', ', $value);
            }, $this->games);
        }

        return $this->games;
    }
    /**
     * addResult
     *
     * @param array $result
     * @return void
     */
    public function addResult(array $result): void
    {
        $this->results[] = $result;
    }
    /**
     * generateRandomDozens
     *
     * @param integer $quantity
     * @param boolean $sort
     * @return array
     */
    private function generateRandomDozens(int $quantity, bool $sort = true): array
    {
        
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
    /**
     * generateDozens
     *
     * @param integer $quantity
     * @return array
     */
    public function generateDozens(int $quantity): array
    {
        $randomDozens = [];

        if(!empty($quantity)){
            $randomDozens = $this->generateRandomDozens($quantity);
        }else{
            throw new EmptyDozensException('');
        }

        return $randomDozens;
    }
    /**
     * generateGames
     *
     * @return void
     */
    public function generateGames():void
    {
        $quantity = $this->getGamesQuantity();
        for($i = 0; $i < $quantity; $i++){
            $game = $this->generateDozens($quantity);
            $this->addGame($game);
        }
    }
    /**
     * generateResults
     *
     * @return void
     */
    public function generateResults():void
    {
        $quantity = self::DOZEN_RESULTS_QUANTITY;
        $results = $this->generateDozens($quantity);
        $this->setResult($results);
    }
    /**
     * defineResultsPerGame
     *
     * @return void
     */
    protected function defineResultsPerGame():void
    {
        foreach($this->games as $index => $game){
            $intersects = array_intersect($game, $this->results);
            $this->resultPerGame[$index] = count($intersects);
        }
    }
    /**
     * play
     *
     * @return void
     */
    public function play(){
        $this->generateGames();
        $this->generateResults();
        $this->defineResultsPerGame();
    }
    /**
     * exportResult
     *
     * @return void
     */
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