<?php

namespace Only\Site\Agents;

use Bitrix\Main\Diag\Debug;
use CIBlockElement;

class Iblock
{
    public static function clearOldLogs()
    {
        try {
            \Bitrix\Main\Loader::includeModule('iblock');
            $logBlockCode = 'LOG';
            $logBlockRes = \CIBlock::GetList([], ['CODE' => $logBlockCode, 'CHECK_PERMISSIONS' => 'N']);
            $logBlockId = $logBlockRes->Fetch()['ID'] ?? 0;
            if (!$logBlockId) {
                Debug::writeToFile("IBlock $logBlockCode not found");
                return '';
            }

            $freshLogsRes = CIBlockElement::GetList(
                ['ID' => 'DESC'],
                ['IBLOCK_ID' => $logBlockId, 'CHECK_PERMISSIONS' => 'N'],
                false,
                ['nTopCount' => 10],
                ['ID']
            );
            $lastFreshLogId = 0;
            while ($arFreshLog = $freshLogsRes->Fetch()) {
                $lastFreshLogId = $arFreshLog['ID'];
            }

            $logsResult = CIBlockElement::GetList(
                ['ID' => 'ASC'],
                ['IBLOCK_ID' => $logBlockId, '<ID' => $lastFreshLogId, 'CHECK_PERMISSIONS' => 'N'],
                false, false,
                ['ID']);
            while ($arLog = $logsResult->Fetch()) {
                CIBlockElement::Delete($arLog['ID']);
            }
        } catch (\Throwable $e) {
            Debug::writeToFile(['msg' => "Exception {$e->getMessage()}"]);}

        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }

    public static function example()
    {
        global $DB;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblockId,
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);
            while ($arLog = $rsLogs->Fetch()) {
                \CIBlockElement::Delete($arLog['ID']);
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}
