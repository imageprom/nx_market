<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule('iblock')) return;
if(!CModule::IncludeModule('nx_market')) return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList( Array('sort' => 'asc'), Array('TYPE' => $arCurrentValues['SOURCE_TYPE'], 'ACTIVE'=>'Y'));
while( $arr=$rsIBlock->Fetch()) $arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];

$arProperty_source['none'] = 'Не выбрано';
foreach ($arCurrentValues['SOURCE_ID'] as $source_id) {
	$rsProp = CIBlockProperty::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array('ACTIVE'=>'Y', 'IBLOCK_ID'=>$source_id));
	while ($arr=$rsProp->Fetch()) {
		if($arr['PROPERTY_TYPE'] != 'F' && !array_key_exists($arr['CODE'], $arProperty_source)) $arProperty_source[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$arComponentParameters = array(
	
	'GROUPS' => array(
		
		'SOURCE_SETTINGS' => array(
			'SORT' => 100,
			'NAME' => 'Настройки источника',
		), 

		'BASKET_SETTINGS' => array(
			'SORT' => 110,
			'NAME' => 'Настройки магазина',
		), 
		
	),
			
	'PARAMETERS' => array(

		'ORDER_ARRAY_NAME' => Array(
			'NAME'=>'Имя переменной в которой хранится масссив заказов',
			'TYPE' => 'STRING',
			'DEFAULT' => 'NX_BASKET',
			'VALUE' => 'NX_BASKET',
			'PARENT' => 'BASKET_SETTINGS',
				
		),

		'SEND_XML' => array(
			'PARENT' => 'BASKET_SETTINGS',
			'NAME' => 'Отправлять xml',
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
				
		'SOURCE_TYPE' => array(
			'PARENT' => 'SOURCE_SETTINGS',
			'NAME' => 'Тип источника данных',
			'MULTIPLE'=>'N',
			'TYPE' => 'LIST',
			'VALUES' => $arIBlockType,
			'REFRESH' => 'Y',
		),
		
		'SOURCE_ID' => array(
			'PARENT' => 'SOURCE_SETTINGS',
			'NAME' => 'Источник данных',
			'MULTIPLE'=>'Y',
			'TYPE' => 'LIST',
			'VALUES' => $arIBlock,
			'REFRESH' => 'Y',
		),


		'PRICE_CODE' => array(
			'PARENT' => 'BASKET_SETTINGS',
			'NAME' => 'Свойство-источник цены',
			'TYPE' => 'LIST',
			'VALUES' => $arProperty_source,
		),

		'PRICE_CURRENCY' => array(
			'PARENT' => 'BASKET_SETTINGS',
			'NAME' => 'Валюта',
			'TYPE' => 'STRING',
			'DEFAULT' => 'руб.',
		),
		
		'PRICE_NDS' => array(
			'PARENT' => 'BASKET_SETTINGS',
			'NAME' => 'НДС (%)', 
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),

		'UNIT_CODE' => array(
			'PARENT' => 'BASKET_SETTINGS',
			'NAME' => 'Свойство-источник едениц измерения',
			'TYPE' => 'LIST',
			'VALUES' => $arProperty_source,
		),

		'ID_FROM_NOTE' => array(
			'PARENT' => 'BASKET_SETTINGS',
			'NAME' => 'ID элемента инфоблока хранится в NOTE',
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		), 

		'CACHE_TIME'  =>  Array('DEFAULT'=>36000),
		'CACHE_GROUPS' => array(
			'PARENT' => 'CACHE_SETTINGS',
			'NAME' => 'Учитывать группу пользователя',
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		
    )
);

if($arCurrentValues['ID_FROM_NOTE'] == 'Y') {
	
	$arComponentParameters['PARAMETERS']['ID_FIELD_NAME'] = Array(
		'NAME' => 'Имя поля с ID в NOTE',
		'TYPE' => 'STRING',
		'DEFAULT' => 'real_id',
		'PARENT' => 'BASKET_SETTINGS',
	);
}
?>