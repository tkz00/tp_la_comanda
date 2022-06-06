<?php

    include_once __DIR__ . '/../resources/Enum.php';

    class EmployeeTypeEnum extends Enum{
        const bartender =   0;
        const brewer    =   1;
        const cook      =   2;
        const waiter    =   3;
        const partner   =   4;
    }

?>