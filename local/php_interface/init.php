<?php

\Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    'Only\Site\Handlers\Iblock' => '/local/modules/dev.site/lib/Handlers/iblock.php',
    'Only\Site\Agents\Iblock' => '/local/modules/dev.site/lib/Agents/Iblock.php',
]);

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandlerCompatible("iblock", "OnAfterIBlockElementAdd", [\Only\Site\Handlers\Iblock::class, 'addLog']);
$eventManager->addEventHandlerCompatible("iblock", "OnAfterIBlockElementUpdate", [\Only\Site\Handlers\Iblock::class, 'addLog']);
