1.  ������� ������� � ������� � ������.

������ - ���� result_modifier.php - ��������� �������� �������� (catalog.element)

$default = 'ost_52';
$arResult['OSTATOK'] = $arResult['PROPERTIES'][$default]['VALUE'];
$price = floatval($arResult['PROPERTIES']['PRICE']['VALUE']);
$arResult['UNIT'] = $arResult['PROPERTIES']['UNIT']['VALUE'];

$arResult['CART_DATA'] = array(
	'id' => $arResult['ID'],
	'ost' => intval($arResult['OSTATOK']),
	'price' => $arResult['PRICE'],
	'unit' => $arResult['UNIT'],
	'name' => htmlentities($arResult['NAME'], ENT_QUOTES, "UTF-8"),
	'note' => array(),
);

2. ������� � ������ template.php ��������� ���
	<div class="nx-basket-byer"	data-cart='<?=json_encode($arResult['CART_DATA'])?>'></div>

	��� ������� ��������� ����� ���������� �� �������� ����� ������������� �������� �������������� ����������� �������.
	
3. ��������� ��� ��� �������� � ���������� �����.

