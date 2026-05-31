<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;

class dev_corporate extends CModule
{
    public $MODULE_ID = 'dev.corporate';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = 'Y';

    public function __construct()
    {
        include __DIR__ . '/version.php';

        if (isset($arModuleVersion['VERSION'], $arModuleVersion['VERSION_DATE']))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('DEV_CORPORATE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DEV_CORPORATE_MODULE_DESCRIPTION');
    }

    public static function GetModuleRightList()
    {
        return array(
            "reference_id" => array("D", "R", "W"),
            "reference" => array(
                Loc::getMessage('DEV_CORPORATE_ACCESS_D'),
                Loc::getMessage('DEV_CORPORATE_ACCESS_R'),
                Loc::getMessage('DEV_CORPORATE_ACCESS_W'),
            )
        );
    }

    public function DoInstall()
    {
        $this->InstallDB();

        RegisterModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        $this->UnInstallDB();

        UnRegisterModule($this->MODULE_ID);
    }

    public function InstallDB()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'iblock',
            'OnIBlockPropertyBuildList',
            $this->MODULE_ID,
            'IBlockPropertyUserGroup',
            'GetUserTypeDescription'
        );
    }

    public function UnInstallDB()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'iblock',
            'OnIBlockPropertyBuildList',
            $this->MODULE_ID,
            'IBlockPropertyUserGroup',
            'GetUserTypeDescription'
        );
    }
}