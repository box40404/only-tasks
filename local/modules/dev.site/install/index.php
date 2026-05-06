<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class dev_site extends CModule
{
    var $MODULE_ID = "dev.site";
    var $MODULE_NAME = "Тренировочный модуль";
    var $PARTNER_NAME = "dev";
    var $PARTNER_URI = "http://localhost";

    function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
    }
}