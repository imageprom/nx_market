<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(false);
$APPLICATION->RestartBuffer();
$path = $_SERVER['DOCUMENT_ROOT'].$templateFolder;
require_once($path.'/view.php');
echo json_encode($arResult);
?>