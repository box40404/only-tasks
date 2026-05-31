<?php

use \Bitrix\Main\Localization\Loc;

class IBlockPropertyUserGroup
{
    public static function GetUserTypeDescription()
    {
        return
        [
            'PROPERTY_TYPE' => 'N',
            'USER_TYPE' => 'UserGroup',
            'DESCRIPTION' => Loc::getMessage('PROPERTY_USER_GROUP_DESC'),
            'GetSettingsHTML' => array(__CLASS__, 'getSettingsHTML'),
            'GetPropertyFieldHtml' => array(__CLASS__,  'getPropertyFieldHtml'),
        ];
    }

    public static function getSettingsHTML(
        array $arProperty,
        array $strHTMLControlName,
        array &$arPropertyFields
    ) {
        $arPropertyFields = array(
            'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'WITH_DESCRIPTION'),
        );

        return '';
    }

    public static function getPropertyFieldHtml(
        array $arProperty,
        array $value,
        array $strHTMLControlName
    ) {
		if($arProperty["USER_TYPE_SETTINGS"]["size"] > 1)
			$size = ' size="'.$arProperty["USER_TYPE_SETTINGS"]["size"].'"';
		else
			$size = '';

		if($arProperty["USER_TYPE_SETTINGS"]["width"] > 0)
			$width = ' style="width:'.$arProperty["USER_TYPE_SETTINGS"]["width"].'px"';
		else
			$width = '';

        $currentValue = $value["VALUE"];
        $arProperty['IS_REQUIRED'] ??= 'N';

		$html = '<select name="'.$strHTMLControlName["VALUE"].'"'.$size.$width.'>';

		if ($arProperty['IS_REQUIRED'] !== 'Y') {
			$html .= '<option value=""'.(empty($currentValue) ? ' selected' : '').'>'.Loc::getMessage("IBLOCK_PROP_ELEMENT_LIST_NO_VALUE").'</option>';
		}

        $options = '';

        $userGroupsRes = Cgroup::GetDropDownList();
        while ($arUserGroup = $userGroupsRes->Fetch()) {
            $selected = ($arUserGroup["REFERENCE_ID"] == $currentValue) ? ' selected' : '';
            $options .= '<option value="'. $arUserGroup["REFERENCE_ID"] .'"'. $selected .'>'. $arUserGroup['REFERENCE'] .'</option>';
        }
		$html .= $options;
		$html .= '</select>';

		return $html;
    }
}
