<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule('nx_market')) {ShowError(GetMessage('NXMARKET_MODULE_NOT_INSTALLED')); return;}

if($arParams['ORDER_ARRAY_NAME']) $arParams['ORDER_ARRAY_NAME'] = trim($arParams['ORDER_ARRAY_NAME']);
if($arParams['BASKET_RESULT_LINK']) $arResult['BASKET_LINK'] = trim($arParams['BASKET_RESULT_LINK']); 
if($arParams['CURRENCY']) $arResult['CURRENCY'] = trim($arParams['PRICE_CURRENCY']);


if (($_SESSION[$arParams['ORDER_ARRAY_NAME']])) {
	$NXOrder = new NXMarket\COrder($_SESSION[$arParams['ORDER_ARRAY_NAME']]);
}

//&& $user_id

elseif (!$USER->IsAuthorized() && $nx_save = $APPLICATION->get_cookie($arParams['ORDER_ARRAY_NAME'])) {
	$NXOrder = new NXMarket\COrder(unserialize($nx_save));
}

else {
    $nx_order = array();
	if($USER->IsAuthorized()) {
		$rsUser = CUser::GetList(($by='ID'), ($order='desc'), array('ID'=>$USER->GetID()), array('SELECT'=>array('UF_USER_ORDER')));
		if($User=$rsUser->GetNext()) {
			$nx_order = unserialize($User['~UF_USER_ORDER']);
		}
	}
	
	if(count($nx_order) > 0) $NXOrder = new NXMarket\COrder ($nx_order);
	else $NXOrder = new NXMarket\COrder(false);
}

if ($_REQUEST['NX_ACTION'] == 'delete_all') {
	$NXOrder->Clear();
}

elseif (!is_array($_REQUEST['NX_ITEMS'])) {

	if ($_REQUEST['NX_ID'] && $_REQUEST['NX_ACTION']=='add') {
		if (!$_REQUEST['NX_COUNT']) $_REQUEST['NX_COUNT']=1;
		$NXOrder->Add(intval($_REQUEST['NX_ID']) , intval($_REQUEST['NX_COUNT']), $_REQUEST['NX_PRICE'], $_REQUEST['NX_NOTE'], $_REQUEST['NX_NAME']);
	}

	elseif ($_POST['NX_ID']&& $_REQUEST['NX_COUNT']>0 && $_POST['NX_ACTION']=='replace') {
		$NXOrder->Replace(intval($_REQUEST['NX_ID']), intval($_POST['NX_COUNT']), $_REQUEST['NX_PRICE'], $_REQUEST['NX_NOTE'], $_REQUEST['NX_NAME']);
	}

	elseif ($_REQUEST['NX_ID']['NX_ID']&& $_REQUEST['NX_COUNT']>0 && $_REQUEST['NX_ACTION']=='change') {
	    $count=intval($_REQUEST['NX_COUNT']);
		$id=intval($_REQUEST['NX_ID']);
		if($count > 0) $NXOrder->Change($id, $count);
	}

	elseif ($_REQUEST['NX_ID'] && $_REQUEST ['NX_ACTION'] == 'delete') {
		$NXOrder->Delete($_REQUEST['NX_ID']);
	}
} 

else {

	foreach ($_REQUEST['NX_ITEMS'] as $Item) {
		if ($Item['NX_ID'] && ($_REQUEST['NX_ACTION'] == 'add' || $Item['NX_ACTION'] == 'add')) {
			$NXOrder->Add(intval($Item['NX_ID']), intval($Item['NX_COUNT']), $Item['NX_PRICE'], $Item['NX_NOTE']);
		}

		elseif ($Item['NX_ID'] && ($_REQUEST['NX_ACTION'] == 'replace' || $Item['NX_ACTION'] == 'replace')) {
			$NXOrder->Replace(intval($Item['NX_ID']), intval($Item['NX_COUNT']), $Item['NX_PRICE'], $Item['NX_NOTE']);
		}

		elseif ($Item['NX_ID'] && ($_REQUEST['NX_ACTION'] == 'delete' || $Item['NX_ACTION'] == 'delete') ) {
			$NXOrder->Delete($Item['NX_ID']);
		}
	}
}

$arResult['IS_EMPTY'] = $NXOrder->IsEmpty();
$arResult['SUM'] = $NXOrder->GetSum();
$arResult['COUNT'] = $NXOrder->GetCount();
$arResult['ELEMENTS'] = $NXOrder->GetListing();

$_SESSION[$arParams['ORDER_ARRAY_NAME']] = array();
$_SESSION[$arParams['ORDER_ARRAY_NAME']] = $NXOrder->GetListing();

$tmp = serialize($NXOrder->GetListing());

$user_id = $USER->GetID();
if(!$user_id) $APPLICATION->set_cookie($arParams['ORDER_ARRAY_NAME'], $tmp, time()+60*60*24); 
if($user_id && $_REQUEST['NX_ACTION']) {$user = new CUser; $user->Update($user_id, array('UF_USER_ORDER'=>$tmp));}

unset($_REQUEST['NX_ACTION']);
$this->IncludeComponentTemplate();;
?>