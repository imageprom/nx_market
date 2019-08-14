<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(
    \Bitrix\Landing\Manager::getDocRoot() .
    '/bitrix/modules/landing/blocks/.components.php'
);

$return = array(
    'block' => array(
       'name' => 'Корзина',
        'section' => array('IP компоненты', 'text'),
        'type' => 'nx_basket',
        'html' => false,
        'subtype' => 'component',
        'namespace' => 'ip'
    ),
    'cards' => array(),
    'nodes' => array(
        'ip:nx_basket_2.0' => array(
             'type' => 'component',
             'extra' => array(
                 'editable' => array(
                    'BASKET_RESULT_LINK' => array(),
                 )
             )
        )
    )
);



return $return;
