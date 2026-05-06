<?php

namespace Only\Site\Handlers;

use CIBlock;
use CIBlockElement;
use CIBlockSection;
use Bitrix\Main\Diag\Debug;

class Iblock
{
    protected static $isProcessing = false;

    public static function addLog(&$arFields)
    {
        if (self::$isProcessing) {
            return;
        }

        if (!$arFields['RESULT']) {
            return "Error on add/update element ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").";
        }

        $targetBlockCode = 'LOG';
        $dbTargetBlockRes = CIBlock::GetList([], ['CODE' => $targetBlockCode]);
        if ($arBlock = $dbTargetBlockRes->GetNext()) {
            $targetBlockId = $arBlock['ID'];
        } else {
            return "Target IBlock '$targetBlockCode' not found";
        }

        $iBlockId = $arFields['IBLOCK_ID'];
        if ($iBlockId == $targetBlockId) {
            return;
        }

        $dbParentBlockRes = CIBlock::GetByID($arFields['IBLOCK_ID']);
        if ($parentBlockData = $dbParentBlockRes->GetNext()) {
            $iBlockName = $parentBlockData['NAME'];
            $iBlockCode = $parentBlockData['CODE'];
        } else {
            return "IBlock {$arFields['IBLOCK_ID']} not found";
        }

        $elName = $arFields['NAME'] ?? null;
        $elId = $arFields['ID'];

        if (!isset($elName)) {
            $elResById = CIBlockElement::GetByID($elId);
            $elName = $elResById->Fetch()['NAME'];
        }

        $dbSectionsRes = CIBlockSection::GetList(arFilter: [
            'NAME' => $iBlockName,
            'CODE' => $iBlockCode
        ]);
        if ($arSection = $dbSectionsRes->GetNext()) {
            $targetSectionId = $arSection['ID'];
        } else {
            $bs = new CIBlockSection;

            $targetSectionId = $bs->Add([
                'IBLOCK_ID' => $targetBlockId,
                'NAME' => $iBlockName,
                'CODE' => $iBlockCode
            ]);
            if (!$targetSectionId) {
                return $bs->LAST_ERROR;
            }
        }

        $el = new CIBlockElement;
        $previewText = $iBlockName . ' -> ';

        $IBlockSectionId = $arFields['IBLOCK_SECTION'][0] ?? 0;
        if (!$IBlockSectionId) {
            $previewText .= $elName;
        } else {
            $previewText = $previewText . self::makeSectionPathRecursive($IBlockSectionId) . ' -> ' . $elName;
        }

        $newElFields = [
            'IBLOCK_SECTION_ID' => $targetSectionId,
            'NAME' => $elId,
            'ACTIVE_FROM' => date('d.m.Y'),
            'PREVIEW_TEXT' => $previewText
        ];

        try {
            self::$isProcessing = true;
            
            $dbElRes = CIBlockElement::GetList([], ['NAME' => (string) $elId]);
            if ($arEl = $dbElRes->GetNext()) {
                $success = $el->Update($arEl['ID'], $newElFields);
                if (!$success) {
                    Debug::writeToFile(['msg' => "Error on updating element $elId: {$el->LAST_ERROR}"]);
                    return "Error on updating element $elId: {$el->LAST_ERROR}";
                }
            } else {
                $newElFields['IBLOCK_ID'] = $targetBlockId;

                $newElId = $el->Add($newElFields);
                if (!$newElId) {
                    Debug::writeToFile(['msg' => "Error on adding element: {$el->LAST_ERROR}"]);
                    return "Error on adding element: {$el->LAST_ERROR}";
                }
            }
        } catch (\Exception $e) {
            Debug::writeToFile(['msg' => "Exception {$e->getMessage()}"]);
        } finally {
            self::$isProcessing = false;
        }
    }

    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        $iQuality = 95;
        $iWidth = 1000;
        $iHeight = 1000;
        /*
         * Получаем пользовательские свойства
         */
        $dbIblockProps = \Bitrix\Iblock\PropertyTable::getList(array(
            'select' => array('*'),
            'filter' => array('IBLOCK_ID' => $arFields['IBLOCK_ID'])
        ));
        /*
         * Выбираем только свойства типа ФАЙЛ (F)
         */
        $arUserFields = [];
        while ($arIblockProps = $dbIblockProps->Fetch()) {
            if ($arIblockProps['PROPERTY_TYPE'] == 'F') {
                $arUserFields[] = $arIblockProps['ID'];
            }
        }
        /*
         * Перебираем и масштабируем изображения
         */
        foreach ($arUserFields as $iFieldId) {
            foreach ($arFields['PROPERTY_VALUES'][$iFieldId] as &$file) {
                if (!empty($file['VALUE']['tmp_name'])) {
                    $sTempName = $file['VALUE']['tmp_name'] . '_temp';
                    $res = \CAllFile::ResizeImageFile(
                        $file['VALUE']['tmp_name'],
                        $sTempName,
                        array("width" => $iWidth, "height" => $iHeight),
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                        false,
                        $iQuality);
                    if ($res) {
                        rename($sTempName, $file['VALUE']['tmp_name']);
                    }
                }
            }
        }

        if ($arFields['CODE'] == 'brochures') {
            $RU_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_RU');
            $EN_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_EN');
            if ($arFields['IBLOCK_ID'] == $RU_IBLOCK_ID || $arFields['IBLOCK_ID'] == $EN_IBLOCK_ID) {
                \CModule::IncludeModule('iblock');
                $arFiles = [];
                foreach ($arFields['PROPERTY_VALUES'] as $id => &$arValues) {
                    $arProp = \CIBlockProperty::GetByID($id, $arFields['IBLOCK_ID'])->Fetch();
                    if ($arProp['PROPERTY_TYPE'] == 'F' && $arProp['CODE'] == 'FILE') {
                        $key_index = 0;
                        while (isset($arValues['n' . $key_index])) {
                            $arFiles[] = $arValues['n' . $key_index++];
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'L' && $arProp['CODE'] == 'OTHER_LANG' && $arValues[0]['VALUE']) {
                        $arValues[0]['VALUE'] = null;
                        if (!empty($arFiles)) {
                            $OTHER_IBLOCK_ID = $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? $EN_IBLOCK_ID : $RU_IBLOCK_ID;
                            $arOtherElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => $OTHER_IBLOCK_ID,
                                    'CODE' => $arFields['CODE']
                                ], false, false, ['ID'])
                                ->Fetch();
                            if ($arOtherElement) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arOtherElement['ID'], $OTHER_IBLOCK_ID, $arFiles, 'FILE');
                            }
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'E') {
                        $elementIds = [];
                        foreach ($arValues as &$arValue) {
                            if ($arValue['VALUE']) {
                                $elementIds[] = $arValue['VALUE'];
                                $arValue['VALUE'] = null;
                            }
                        }
                        if (!empty($arFiles && !empty($elementIds))) {
                            $rsElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => \Only\Site\Helpers\IBlock::getIblockID('PRODUCTS', 'CATALOG_' . $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? '_RU' : '_EN'),
                                    'ID' => $elementIds
                                ], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
                            while ($arElement = $rsElement->Fetch()) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arElement['ID'], $arElement['IBLOCK_ID'], $arFiles, 'FILE');
                            }
                        }
                    }
                }
            }
        }
    }

    private static function makeSectionPathRecursive(int $sectionId)
    {
        $dbRes = CIBlockSection::GetByID($sectionId);
        if ($sectionData = $dbRes->GetNext()) {
            $nextSectionId = $sectionData['IBLOCK_SECTION_ID'] ?? 0;
            if ($nextSectionId) {
                return self::makeSectionPathRecursive($nextSectionId) . ' -> ' . $sectionData['NAME'];
            } else {
                return $sectionData['NAME'];
            }        
        }
    }
}
