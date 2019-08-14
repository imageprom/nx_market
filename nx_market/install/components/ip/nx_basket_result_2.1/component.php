<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule('iblock')) {ShowError(GetMessage('IBLOCK_MODULE_NOT_INSTALLED')); return;}
if(!CModule::IncludeModule('nx_market')) {ShowError(GetMessage('NXMARKET_MODULE_NOT_INSTALLED')); return;}

$arParams['AJAX'] = isset($_REQUEST['nx_basket_result_ajax']) && $_REQUEST['nx_basket_result_ajax'] == 'Y';

$arResult['CURRENCY'] = trim($arParams['PRICE_CURRENCY']);
$arParams['ORDER_ARRAY_NAME'] = trim($arParams['ORDER_ARRAY_NAME']);
$arParams['PRICE_NDS'] = floatval(trim($arParams['PRICE_NDS']));

if(!$arParams['UNIT_CODE']) $arParams['UNIT_CODE'] == 'UNIT';
else trim($arParams['UNIT_CODE']);

if($arParams['ID_FROM_NOTE'] != 'Y')
	$arParams['ID_FIELD_NAME'] = 'ID';
else {
	$arParams['ID_FIELD_NAME'] = trim($arParams['ID_FIELD_NAME']);
}

if ($arParams['CACHE_TYPE'] == 'Y' || ($arParams['CACHE_TYPE'] == 'A' && COption::GetOptionString('main', 'component_cache_on', 'Y') == 'Y'))
	$arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);

else $arParams['CACHE_TIME'] = 0;

if($this->StartResultCache(false, Array(($arParams['CACHE_GROUPS'] === 'N'? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS))) {

	$arResult['SECTIONS'] = Array();
	$arSFilter = Array('IBLOCK_ID' => $arParams['SOURCE_ID'], 'GLOBAL_ACTIVE' => 'Y');
	$sect_res = CIBlockSection::GetList(Array($by => $order), $arSFilter);
	while( $ar_sec = $sect_res->GetNext()) {
		$arResult['SECTIONS'][$ar_sec['ID']] = $ar_sec['NAME'];
	}
	$this->EndResultCache();
}

if (($_SESSION[$arParams['ORDER_ARRAY_NAME']]) && ($NXOrder=new NXMarket\COrder ($_SESSION[$arParams['ORDER_ARRAY_NAME']]))&&!$NXOrder->IsEmpty()) {

	$arResult['ORDER'] = $NXOrder->GetListing();
	$arResult['ITEMS'] = Array();
	$arResult['SUM'] = 0;

	$ID = Array();

	foreach ($arResult['ORDER'] as $item) {
		if($arParams['ID_FIELD_NAME'] == 'ID')
			$ID[$item['ID']] = $item['ID'];
		else {
			$ID[$item['ID']] = $item['NOTE'][$arParams['ID_FIELD_NAME']];
		}
	}

	$arSelect = Array('IBLOCK_ID', 'ID', 'XML_ID', 'NAME', 'DETAIL_PAGE_URL', 'SECTION_PAGE_URL', 'PROPERTY_*', 'PREVIEW_PICTURE', 'SORT', 'CODE', 'IBLOCK_SECTION_ID');
	$arFilter = Array('IBLOCK_ID' => $arParams['SOURCE_ID'], 'ID'=> $ID, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y', 'ACTIVE_DATE' => 'Y');
	$res = CIBlockElement::GetList(Array('SORT' => 'ASC', 'NAME' => 'ASC'), $arFilter, $arSelect);

	while($ar_res = $res->GetNextElement()) { 
		$Item = $ar_res->GetFields();
		$Item['PROPERTIES'] = $ar_res->GetProperties();

		$arResult['IB_ITEMS'][$Item['ID']] = $Item;
	}

	foreach ($arResult['ORDER'] as $arOrder) {

		if($arParams['ID_FIELD_NAME'] == 'ID')
			$ID = $arOrder['ID'];
		else {
			$ID = $arOrder['NOTE'][$arParams['ID_FIELD_NAME']];
		}

		$Item = $arResult['IB_ITEMS'][$ID];
		$Item['ORDER'] = $arOrder;
		$Item['SECTION'] = $arResult['SECTIONS'][$Item['IBLOCK_SECTION_ID']];

		if (($Item['ORDER']['NAME'] =='untitled' || !$Item['ORDER']['NAME']) &&  $Item['NAME'])
			 $Item['ORDER']['NAME'] = $Item['NAME'];

		if($Item['PREVIEW_PICTURE']) $Item['PREVIEW_PICTURE'] = CFile::GetFileArray($Item['PREVIEW_PICTURE']);

		$Item['ORDER'] = $arResult['ORDER'][$Item['ID']];

		$arResult['ITEMS'][] = $Item;

		$arResult['SUM_NO_NDS'] += $Item['ORDER']['COUNT'] * $Item['ORDER']['PRICE'];

	}
	
	$arResult['ITEM_COUNT'] = count($arResult['ITEMS']);

	$arResult['NDS'] = $arResult['SUM_NO_NDS'] * (1 + ($arParams['PRICE_NDS'] / 100)) - $arResult['SUM_NO_NDS'];
	$arResult['SUM'] = $arResult['SUM_NO_NDS'] + $arResult['NDS'];
}

if($arResult['ITEM_COUNT'] > 0) $arResult['IS_EMPTY'] = false;
else $arResult['IS_EMPTY'] = true;

$arResult['DISCOUNT'] = 0;
$arResult['DELIVERY_SUM'] = 0;


if($arParams['AJAX']) {
	$this->setFrameMode(false);
	define('BX_COMPRESSION_DISABLED', true);
	ob_start();

	$APPLICATION->IncludeComponent(
		'ip:nx_basket_2.0',
		'empty', 
		array(
			"ORDER_ARRAY_NAME" => "NX_BASKET",
			"PRICE_CURRENCY" => "Р",
			"BASKET_RESULT_LINK" => "/basket",
			"COMPONENT_TEMPLATE" => "",
			"COMPOSITE_FRAME_MODE" => "N",
			"COMPOSITE_FRAME_TYPE" => "AUTO"
		),
		false
	);

	$this->IncludeComponentTemplate('ajax');
	$json = ob_get_contents();

	$APPLICATION->RestartBuffer();
	while(ob_end_clean());
	header('Content-Type: application/json; charset='.LANG_CHARSET);
	CMain::FinalActions();
	echo $json;
	die();
}
else {
	$this->IncludeComponentTemplate(); 
}