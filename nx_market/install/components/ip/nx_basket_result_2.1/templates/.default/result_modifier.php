<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if($_POST['NX_DISCOUNT'])
	$arResult['DISCOUNT'] = floatval($_POST['NX_DISCOUNT']);

if($_POST['NX_DELIVERY_SUM'])
	$arResult['DELIVERY_SUM'] = floatval($_POST['NX_DELIVERY_SUM']);

?>