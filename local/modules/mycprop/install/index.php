<?php

use Bitrix\Main\EventManager;

class mycprop extends CModule
{
    public $MODULE_ID = 'mycprop';

    public function __construct()
    {
        $this->MODULE_NAME = 'Комплексное свойство';
        $this->MODULE_DESCRIPTION = 'Модуль добавляющий комплексное свойство';

        include(__DIR__ . '/version.php');

        if (isset($arModuleVersion['VERSION'])) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'] ?? null;
        }
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
            'MyCProp',
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
            'MyCProp',
            'GetUserTypeDescription'
        );
    }
}