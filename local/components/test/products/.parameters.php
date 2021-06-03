<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

try
{
    if (!Main\Loader::includeModule('iblock') || !Main\Loader::includeModule("highloadblock"))
        throw new Main\LoaderException(Loc::getMessage('PRODUCTS_PARAMETERS_IBLOCK_MODULE_NOT_INSTALLED'));

    $iblocks = array(0 => " ");
    $iblocksCode = array("" => " ");

    $iterator = \Bitrix\Iblock\IblockTable::getList([
        'order' => ['SORT' => 'ASC'],
        'filter' => ['ACTIVE' => 'Y'],
    ]);
    while ($iblock = $iterator->fetch())
    {
        $iblocks[$iblock['ID']] = $iblock['NAME'];
        $iblocksCode[$iblock['CODE']] = $iblock['CODE'];
    }

    $hblocks = array(0 => " ");
    $hblocksCode = array("" => " ");

    $iterator = \Bitrix\Highloadblock\HighloadBlockTable::getList([
        'order' => ['NAME' => 'ASC'],
    ]);
    while ($hblock = $iterator->fetch())
    {
        $hblocks[$hblock['ID']] = $hblock['NAME'];
        $hblocksCode[$hblock['CODE']] = $hblock['CODE'];
    }

    $sortFields = array(
        'ACTIVE_FROM' => Loc::getMessage('PRODUCTS_PARAMETERS_SORT_ACTIVE_FROM'),
        'REVIEWS' => Loc::getMessage('PRODUCTS_PARAMETERS_SORT_REVIEWS')
    );

    $sortDirection = array(
        'ASC' => Loc::getMessage('PRODUCTS_PARAMETERS_SORT_ASC'),
        'DESC' => Loc::getMessage('PRODUCTS_PARAMETERS_SORT_DESC')
    );

    $arComponentParameters = array(
        'GROUPS' => array(
        ),
        'PARAMETERS' => array(
            'IBLOCK_ID' => array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('PRODUCTS_PARAMETERS_IBLOCK_ID'),
                'TYPE' => 'LIST',
                'VALUES' => $iblocks
            ),
            'REVIEWS_HBLOCK_ID' => array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('PRODUCTS_PARAMETERS_HBLOCK_ID'),
                'TYPE' => 'LIST',
                'VALUES' => $hblocks
            ),
            'SHOW_NAV' => array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('PRODUCTS_PARAMETERS_SHOW_NAV'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ),
            'COUNT' =>  array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('PRODUCTS_PARAMETERS_COUNT'),
                'TYPE' => 'STRING',
                'DEFAULT' => '0'
            ),
            'CACHE_TIME' => array(
                'DEFAULT' => 3600
            )
        )
    );
}
catch (Main\LoaderException $e)
{
    ShowError($e->getMessage());
}
?>