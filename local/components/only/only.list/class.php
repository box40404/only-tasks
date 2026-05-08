<?php

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\IblockTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}

class OnlyList extends CBitrixComponent
{
    public function executeComponent()
    {
        if ($this->startResultCache())
        {
            $this->prepareParams();

            $this->arResult = [];

            $queryParams = [
                'filter' => ['=IBLOCK_TYPE_ID' => $this->arParams['IBLOCK_TYPE']],
                'select' => ['ID']
            ];
            if ($this->arParams['IBLOCK_ID']) {
                $queryParams['filter']['=ID'] = $this->arParams['IBLOCK_ID'];
            }

            $iBlocks = IblockTable::getList($queryParams)->fetchAll();
            foreach ($iBlocks as $iBlock) {
                $elementQueryFilter = [
                    'filter' => ['=IBLOCK_ID' => $iBlock['ID']]
                ];
                $elementQueryFilter['filter'] = array_merge($elementQueryFilter['filter'], $this->arParams['USER_FILTER']);

                $iBlockElements = ElementTable::getList($elementQueryFilter)->fetchAll();
                $this->arResult['ITEMS'][$iBlock['ID']] = $iBlockElements;
            }             

            $this->includeComponentTemplate();
        }
    }
    
    private function prepareParams(): void
    {
        $this->arParams['IBLOCK_TYPE'] = trim($this->arParams['IBLOCK_TYPE'] ?? '');
        if (empty($this->arParams['IBLOCK_TYPE'])) {
            ShowError(\Bitrix\Main\Localization\Loc::getMessage('IBLOCK_TYPE_NOT_DEFINED'));
        }

        $this->arParams['IBLOCK_ID'] = (int) trim($this->arParams['IBLOCK_ID'] ?? '');

        if (empty($this->arParams["USER_FILTER"]) || !is_array($this->arParams["USER_FILTER"])) {
            $this->arParams["USER_FILTER"] = [];
        }
        foreach ($this->arParams["USER_FILTER"] as $key=>$val) {
            $val = trim($val ?? '');
            if (empty($val)) {
                unset($this->arParams["USER_FILTER"][$key]);
            } else {
                $this->arParams["USER_FILTER"][$key] = $val;
            }
        }
    }
}
