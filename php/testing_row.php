<?php
    //number of row based on form
    $queryNumberRow = "SELECT * FROM altitudes_holesice where ($firstYear - $secondYear) $oper $diff";
    $resultNumberRow = pg_query($queryNumberRow);    
    $rowsNumber = pg_num_rows($resultNumberRow);
       
?>