<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	'NAME' => GetMessage('СOMPONET_NAME'),
	'DESCRIPTION' => GetMessage('СOMPONET_DESC'),
	'ICON' => '/images/basket.gif',
	'PATH' => array(
		'ID' => 'my_components',
		'NAME' => GetMessage('IP_COMPONENTS_TITLE'),
		'CHILD' => array(
			'ID' => 'my_basket',
			'NAME' => GetMessage('COMPONENT_DESC_SECT')
		)
	),
);
?>