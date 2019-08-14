<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="nx-basket-result">
<?$frame = $this->createFrame()->begin('<div class="text-center"><h3 class="nx-empty">Загрузка...</h3><img src="/upload/load.gif" alt = "Загрузка" /></div>');?> 	
<?CJSCore::Init(array("jquery"));?>
<?$path = $_SERVER['DOCUMENT_ROOT'].$templateFolder;
	require_once($path.'/view.php');
	echo $arResult['HTML'];
?>
<?$frame->end();?>
</div>