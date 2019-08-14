<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$arSelect = Array('ID', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL', 'NAME');

if(!$arResult['IS_EMPTY']) {
		foreach($arResult['ELEMENTS'] as &$element) { 
			$arResult['JSON_ITEMS'][] = array('id' => $element['ID'], 'cnt' => $element['COUNT']);

		$arFilter = Array('ID' => $element['ID']);
	 	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		if ($arFields = $res->GetNext()) {
			$element['NAME'] = $arFields['NAME'];
		 	$arFields['PREVIEW_PICTURE'] = CFile::GetFileArray($arFields['PREVIEW_PICTURE']);

		 	$element += $arFields;
		}
	}
}
?> 