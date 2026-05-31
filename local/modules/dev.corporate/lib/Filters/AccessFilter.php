<?php

namespace Dev\Corporate\Filters;

use Bitrix\Main\Error;
use Bitrix\Main\EventResult;

class AccessFilter extends \Bitrix\Main\Engine\ActionFilter\Base
{
    public function onBeforeAction(\Bitrix\Main\Event $event)
    {
        $right = \CMain::GetUserRight('dev.corporate');
        if ($right === 'D') {
            $this->addError(new Error(
                'Access Denied',
                403
            ));

            return new EventResult(
                EventResult::ERROR,
                handler: $this
            );
        }

        return null;
    }
}
