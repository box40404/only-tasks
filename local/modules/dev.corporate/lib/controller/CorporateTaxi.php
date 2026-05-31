<?php

namespace Dev\Corporate\Controller;

use Bitrix\Main\Engine\ActionFilter\Attribute\Rule\DisablePrefilters;
use Bitrix\Main\Engine\ActionFilter;
use Dev\Corporate\Filters\AccessFilter;
use Bitrix\Main\Context;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\UserTable;
use Bitrix\Main\Engine\CurrentUser;

class CorporateTaxi extends \Bitrix\Main\Engine\Controller
{
    protected function getDefaultPreFilters()
    {
        return [
            new ActionFilter\HttpMethod(),
            new ActionFilter\Authentication(),
            new ActionFilter\Csrf(),
            new AccessFilter()
        ];
    }
    
    #[DisablePrefilters([
        ActionFilter\Csrf::class,
    ])]
    public function indexAction()
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $request = Context::getCurrent()->getRequest();

        $dateFrom = $request->get('date_from') ?? date('d.m.Y H:i');
        $dateTo = $request->get('date_to') ?? date('d.m.Y H:i', strtotime('+1 day'));

        $userGroups = CurrentUser::get()->getUserGroups();

        $CREntity = IblockTable::compileEntity('CategoryRights');
        $CRDataClass = $CREntity->getDataClass();
        
        $arAvailableComfort = $CRDataClass::getList([
            'filter' => [
                '=ACTIVE' => 'Y',
                'USER_GROUP.VALUE' => $userGroups
            ],
            'select' => ['COMF_CATEGORY_ID' => 'COMF_CATEGORY.VALUE']
        ])
        ->fetchAll();

        if (empty($arAvailableComfort)) {
            return $this->renderComponent('only:cars.free', '', ['CARS' => []]);
        }

        $comfortIds = array_map(
            fn ($v) => (int) $v,
            array_column($arAvailableComfort, 'COMF_CATEGORY_ID')
        );

        $CCEntity = IblockTable::compileEntity('CorporateCars');
        $CCDataClass = $CCEntity->getDataClass();

        $arAvailableCars = $CCDataClass::getList([
            'filter' => [
                '=ACTIVE' => 'Y',
                'COMF_CATEGORY.VALUE' => $comfortIds
            ],
            'select' => [
                'CAR_ID' => 'ID',
                'NAME', 
                'DRIVER_ID' => 'DRIVER.VALUE',
                'DRIVER_NAME' => 'DRIVER_USER.NAME',
                'DRIVER_SURNAME' => 'DRIVER_USER.LAST_NAME',
                'COMF_CATEGORY_NAME' => 'COMFORT_CATEGORY.NAME'
            ],
            'runtime' => [
                'DRIVER_USER' => [
                    'data_type' => UserTable::class,
                    'reference' => ['=this.DRIVER.VALUE' => 'ref.ID'],
                    'join_type' => 'inner' 
                ],
                'COMFORT_CATEGORY' => [
                    'data_type' => ElementTable::class,
                    'reference' => ['=this.COMF_CATEGORY.VALUE' => 'ref.ID']
                ]
            ]
        ])
        ->fetchAll();

        if (empty($arAvailableCars)) {
            return $this->renderComponent('only:cars.free', '', ['CARS' => []]);
        }

        $arAvailableCarIds = array_column($arAvailableCars, 'CAR_ID');

        $HLTaxiBooksEntity = HighloadBlockTable::compileEntity('CorporateTaxiBooks');
        $HLTaxiBooksTable = $HLTaxiBooksEntity->getDataClass();

        $arBookedCars = $HLTaxiBooksTable::getList([
            'filter' => [
                '<=UF_DATE_FROM' => $dateTo,
                '>=UF_DATE_TO' => $dateFrom,
                'UF_CAR_ID' => $arAvailableCarIds 
            ],
            'select' => ['CAR_ID' => 'UF_CAR_ID']
        ])
        ->fetchAll();

        $arFreeCars = array_udiff($arAvailableCars, $arBookedCars, function ($ar1, $ar2) {
            return $ar1['CAR_ID'] - $ar2['CAR_ID'];
        });

        return $this->renderComponent('only:cars.free', '', ['CARS' => $arFreeCars]);
    }
}
