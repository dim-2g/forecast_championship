<?php

require_once 'data.php';

function match($c1, $c2) {
    global $data;
    if (!array_key_exists($c1, $data) || !array_key_exists($c2, $data)) {
        return false;
    }
    $aAvgGoals = getAverageGoals($data);
    $aCommandFirst = $data[$c1];
    $aCommandSecond = $data[$c2];
    //рассчитываем мощность атаки и защиты    
    $dFirstTeamPower = getTeamPower($aCommandFirst, $aAvgGoals);
    $dSecondTeamPower = getTeamPower($aCommandSecond, $aAvgGoals);
    //коэф-нт вероятности забить гол
    $dRatioFirstTeamGoal = $dFirstTeamPower['attack'] * $dSecondTeamPower['defense'] * $aAvgGoals['total'];
    $dRatioSecondTeamGoal = $dSecondTeamPower['attack'] * $dFirstTeamPower['defense'] * $aAvgGoals['total'];
    //строим интервалы вероятности по кол-ву голов
    $aFirstPuassonRange = getPuassonRange($dRatioFirstTeamGoal);
    $aSecondPuassonRange = getPuassonRange($dRatioSecondTeamGoal);
    //добавляем случайность
    $iRandom = rand(0, 10000);
    $iFirstForecastGoals = getForecastCoals($aFirstPuassonRange, $iRandom);
    //заново генерируем случайность, чтобы слабая команда имела возможность выиграть
    $iRandom = rand(0, 10000);
    $iSecondForecastGoals = getForecastCoals($aSecondPuassonRange, $iRandom);
    
    return array(
        $iFirstForecastGoals,
        $iSecondForecastGoals    
    );
}

function getPuassonRange($dLyamda) {
    $aPuassonIntervals = array();
    $dSumma = 0;
    $iLimitGoals = 5;
    for ($i=0; $i<=$iLimitGoals; $i++) {
        $dSumma += pow($dLyamda, $i) / factorial($i) * exp(-1*$dLyamda) * 10000;
        $aPuassonIntervals[$i] = $dSumma;
    }
    return $aPuassonIntervals;
}

function factorial($number) {
    //return gmp_fact($number);
    if($number <= 1) {
        return 1;
    } else return ($number * factorial($number - 1));
}

function getForecastCoals($aIntervals, $iRand) {
    $iGoals = 0;
    foreach ($aIntervals as $key => $value) {
        if ($iRand > $value) {
            $iGoals = $key;
        } else {
            break;
        }
    }
    return $iGoals;
}

function getAverageGoals($data) {
    $tmp = array(
        'scored' => 0,
        'skiped' => 0,
        'games' => 0,
    );
    foreach ($data as $aCommand) {
        if (empty($aCommand['goals']['scored']) || 
            empty($aCommand['goals']['skiped']) || 
            empty($aCommand['games'])) {
                continue;
        }
        $tmp['scored'] += $aCommand['goals']['scored'];
        $tmp['skiped'] += $aCommand['goals']['skiped'];
        $tmp['games'] += $aCommand['games'];
    }

    return array(
        'scored' => $tmp['scored'] / $tmp['games'],
        'skiped' => $tmp['skiped'] / $tmp['games'],
        'total' => ($tmp['scored'] + $tmp['skiped']) / $tmp['games'] / 2,
    );
}

function getTeamPower($aCommand, $aAvarageGoals) {
    return array(
        'attack' => $aCommand['goals']['scored'] / $aCommand['games'] / $aAvarageGoals['scored'],
        'defense' => $aCommand['goals']['skiped'] / $aCommand['games'] / $aAvarageGoals['skiped'],
    );
}