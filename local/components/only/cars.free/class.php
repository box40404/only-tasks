<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}

class CarsFree extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult['CARS'] = $this->arParams['CARS'];

        $this->includeComponentTemplate();
    }
}
