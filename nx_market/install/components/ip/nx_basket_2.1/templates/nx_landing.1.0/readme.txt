1.  Создать масссив с данными о товаре.

Пример - файл result_modifier.php - детальная страница каталога (catalog.element)

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

2. Добавть в шаблон template.php следующий код
	<div class="nx-basket-byer"	data-cart='<?=json_encode($arResult['CART_DATA'])?>'></div>

	Для каждого экзмпляра этого контейнера на странице будет автоматически добавлен соотвествующий управляющий элемент.
	
3. Проверить что все работает и подкрутить стили.

