<?php

    include_once __DIR__ . '/../resources/Enum.php';

    class TableStateEnum extends Enum{
        const waiting   =   0;
        const eating    =   1;
        const paying    =   2;
        const closed    =   3;
    }

?>