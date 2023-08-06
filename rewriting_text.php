<?php

    //převzetí proměnných ze strany klienta pro následné přepsání pro účely textového výstupu
    $firstYearName = $_POST["firstAlt"];
    $secondYearName = $_POST["secondAlt"];
    $plusMinusName = $_POST["upDown"];
    $landUseName = $_POST['LLUC'];
    $diffName = $_POST["diffAlt"];
    $operName = $_POST["operAlt"];

    //změna hodnoty proměnné prvního roku
    if($firstYearName == '1'){
        $firstYearName = '1938';
    } elseif($firstYearName == '2'){
        $firstYearName ='1951';
    } else{
        $firstYearName ='2000';
    }

    //změna hodnoty proměnné druhého roku
    if($secondYearName == '2'){
        $secondYearName = '1951';
    } elseif($secondYearName == '3'){
        $secondYearName ='2000';
    } else{
        $secondYearName ='2020';
    }

    //Změna proměnné vyjadřující větší či menší hodnoty, než je prahová hodnota
    if($operName == '>'){
        $operName = 'větší';
    } else{
        $operName ='menší';
    }

    //Změna proměnné vyjadřující pokles či navýšení nadmořských výšek
    if($plusMinusName == 'minus'){
        $plusMinusName = 'pokles';
    } else{
        $plusMinusName ='navýšení';
    }  

    //Změna textového řetězce pro snazší pochopení požadovaného výstupu týkajícího se konkrétní varianty využití území
    if($landUseName == 'withoutLanduse'){
        $landUseName = 'bez informace o využití území';
    } elseif($landUseName == 'oldLanduse'){
        $landUseName = 'Rozdělení dle využití území z období stabilního katastru';
    } else{
        $landUseName = 'Rozdělení dle novodobého využití území včetně porovnání se starším využití formou atributu';
    }
?>
