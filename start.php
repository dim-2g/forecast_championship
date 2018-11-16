<?php
require_once 'index.php';

$iCount = count($data);
for ($i=100;$i--;) {

    list($iTeamFirst, $iTeamSecond) = getTeams($iCount-1);
    for ($j=25;$j--;) {
        $aResult = match($iTeamFirst, $iTeamSecond);
        echo "<h3 style=\"text-align: center;\">{$data[$iTeamFirst]['name']} - {$aResult['0']}:{$aResult['1']} - {$data[$iTeamSecond]['name']}</h3>";
    }
}

function getTeams($iCount) {
    $iTeamFirst = rand(0, $iCount);
    $iTeamSecond = rand(0, $iCount);
    $iter = 0;
    while ($iTeamFirst==$iTeamSecond) {
        $iTeamSecond = rand(0, $iCount);
        $iter++;
    }
    return array(
        $iTeamFirst,
        $iTeamSecond   
    );
}
?>