<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}

\Bitrix\Main\Loader::includeModule('iblock');

$el = new CIBlockElement;
$IBLOCK_ID = 4;

if (($handle = fopen("vacancy.csv", "r")) !== FALSE) {
    fgetcsv($handle, 1000, ",");
    while (($data = fgetcsv($handle, 1000, ",", '"', '\\')) !== FALSE) {
        $PROP = [];
        $rawData = [];

        $rawData['ACTIVITY'] = $data[9];
        $rawData['FIELD'] = $data[11];
        $rawData['OFFICE'] = $data[1];
        $rawData['LOCATION'] = $data[2];
        $rawData['REQUIRE'] = explode(';', $data[4]);
        $rawData['DUTY'] = explode(';', $data[5]);
        $rawData['CONDITIONS'] = explode(';', $data[6]);
        $rawData['EMAIL'] = $data[12];
        $rawData['TYPE'] = $data[8];
        $rawData['SCHEDULE'] = $data[10];

        $salaryValue = $data[7];

        if (empty($salaryValue) or $salaryValue === '-') {
            $salaryType = '';
        } else {
            $salaryType = match (explode(' ', $salaryValue)[0]) {
                'от' => 'ОТ',
                'до' => 'ДО',
                'по' => 'Договорная',
                default => '='
            };
        }

        $rawData['SALARY_TYPE'] = $salaryType;
        $rawData['SALARY_VALUE'] = $salaryValue;

        $preparedData = prepare($rawData);

        foreach ($preparedData as $key => $value) {
            if (empty($value)) {
                continue;
            }
            if (is_array($value)) {
                $PROP[$key] = $value;
                continue;
            }
            if ($key === 'LOCATION') {
                $value .= '%';
            }

            $property = CIBlockPropertyEnum::GetList(arFilter: ["IBLOCK_ID" => $IBLOCK_ID, "VALUE" => $value]);
            if ($arEnum = $property->Fetch()) {
                $PROP[$key] = $arEnum['ID'];
            } else {
                $PROP[$key] = $value;
            }
        }

        $PROP['DATE'] = date('d.m.Y');

        $arFields = [
            "MODIFIED_BY" => $USER->GetID(),
            'IBLOCK_ID' => $IBLOCK_ID,
            'NAME' => $data[3],
            'PROPERTY_VALUES' => $PROP
        ];

        if($PRODUCT_ID = $el->Add($arFields))
            echo "New ID: ".$PRODUCT_ID;
        else
            echo "Error: ".$el->LAST_ERROR;
    }

    fclose($handle);
}

function prepare(array $data)
{
    // Общая очистка спецсимволов
    foreach ($data as $key => $value) {
        $value = preg_replace('/[\x00-\x1F\x7F\•]/u', '', $value);
        if (is_array($value)) {
            foreach ($value as $arKey => $arValue) {
                $arValue = trim($arValue, " \n\r\t\v\0.");
                $value[$arKey] = $arValue;
            }
        } else {
            $value = trim($value);
        }
        $data[$key] = $value;
    }

    // Форматирование для конкретных строк
    $data['OFFICE'] = preg_replace(
        [
            '/(\w)\(/u', '/\s+\)/u'
        ], 
        [
            '$1 (', ')'
        ], 
        $data['OFFICE']);

    $data['SALARY_VALUE'] = preg_replace('/^\D+/u', '', $data['SALARY_VALUE']);
    
    return $data;
}
