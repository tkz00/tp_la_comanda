<?php

    include_once __DIR__ . '/../resources/Enum.php';

    class OrderStateEnum extends Enum{
        const pending       =   0;
        const preparation   =   1;
        const ready         =   2;
        const finished      =   3;
    }

?>