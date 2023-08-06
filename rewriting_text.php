<?php
    $firstYearName = $_POST["firstAlt"];
    $secondYearName = $_POST["secondAlt"];
    $plusMinusName = $_POST["upDown"];
    $landUseName = $_POST['LLUC'];
    $diffName = $_POST["diffAlt"];
    $operName = $_POST["operAlt"];

    //change value of firstYear attribute
    if($firstYearName == '1'){
        $firstYearName = '1938';
    } elseif($firstYearName == '2'){
        $firstYearName ='1951';
    } else{
        $firstYearName ='2000';
    }

    //change value of secondYear attribute
    if($secondYearName == '2'){
        $secondYearName = '1951';
    } elseif($secondYearName == '3'){
        $secondYearName ='2000';
    } else{
        $secondYearName ='2020';
    }

    if($operName == '>'){
        $operName = 'větší';
        //$diffName = -$diffName;
    } else{
        $operName ='menší';
    }

    if($plusMinusName == 'minus'){
        $plusMinusName = 'pokles';
    } else{
        $plusMinusName ='navýšení';
    }  

    if($landUseName == 'withoutLanduse'){
        $landUseName = 'bez informace o využití území';
    } elseif($landUseName == 'oldLanduse'){
        $landUseName = 'Rozdělení dle využití území z období stabilního katastru';
    } else{
        $landUseName = 'Rozdělení dle novodobého využití území včetně porovnání se starším využití formou atributu';
    }
?>
