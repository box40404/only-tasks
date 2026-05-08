<?php
CModule::IncludeModule("iblock");

$dbIBlockType = CIBlockType::GetList(
    array("sort" => "asc"),
    array("ACTIVE" => "Y")
);
while ($arIBlockType = $dbIBlockType->Fetch()) {
    if ($arIBlockTypeLang = CIBlockType::GetByIDLang($arIBlockType["ID"], LANGUAGE_ID)) {
        $arIblockType[$arIBlockType["ID"]] = "[".$arIBlockType["ID"]."] ".$arIBlockTypeLang["NAME"];
    }
}

$arIBlocks = [];
$iblockFilter = [
	'ACTIVE' => 'Y',
];
if (!empty($arCurrentValues['IBLOCK_TYPE']))
{
	$iblockFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
}
$db_iblock = CIBlock::GetList(["SORT"=>"ASC"], $iblockFilter);
while($arRes = $db_iblock->Fetch())
{
	$arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
}

$arComponentParameters = array(
    "GROUPS" => array(
        "PARAMS" => array(
            "NAME" => "Настройки параметров",
            "SORT" => 300
        ),
    ),
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => "Тип инфоблока",
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arIblockType,
            "REFRESH" => "Y"
        ),
        "IBLOCK_ID" => [
			"PARENT" => "DATA_SOURCE",
			"NAME" => "ID Инфоблока",
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
            "DEFAULT" => '',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		],
        "CACHE_TIME" => array(),
    )
);
