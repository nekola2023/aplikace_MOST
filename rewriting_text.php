<?php
    $firstYearName = $_POST["firstAlt"];
    $secondYearName = $_POST["secondAlt"];
    $operName = $_POST["diffAlt"];
    $plusMinusName = $_POST["upDown"];
    $landUseName = $_POST['LLUC'];

    if($firstYearName == 'altitude_1938'){
        $firstYearName = '1938';
    } elseif($firstYearName == 'altitude_1950'){
        $firstYearName ='1951';
    } else{
        $firstYearName ='2000';
    }

    if($secondYearName == 'altitude_1950'){
        $secondYearName = '1951';
    } elseif($secondYearName == 'altitude_2000'){
        $secondYearName ='2000';
    } else{
        $secondYearName ='2020';
    }

    if($operName == '>'){
        $operName = 'menší';
    } else{
        $operName ='větší';
    }

    if($plusMinusName == 'minus'){
        $plusMinusName = 'pokles';
    } else{
        $plusMinusName ='navýšení';
    }  
?>