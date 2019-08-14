<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}
?>

<section class="landing-block g-pt-20 g-pb-20">
	<div class="container">
		<div class="tab-content">
			<div class="tab-pane fade show active">
				<div class="landing-component">
				<?$APPLICATION->IncludeComponent("ip:nx_basket_result_2.0", "nx_landing.1.0", Array(
	    "SOURCE_TYPE" => "catalog",	// Тип источника данных
		"SOURCE_ID" => array(	// Источник данных
			0 => "1",
		),
		"ORDER_ARRAY_NAME" => "NX_BASKET",	// Имя переменной в которой хранится масссив заказов
		"PRICE_CODE" => "PRICE",	// Свойство-источник цены
		"TAIL_CODE" => 'ost_52',
		"UNIT_CODE" => "UNIT",	// Свойство-источник едениц измерения
		"PRICE_CURRENCY" => "<s class='r'>Р</s>",	// Валюта
		"PRICE_NDS" => "",	// НДС (%)
		"CACHE_TYPE" => "N",	// Тип кеширования
		"CACHE_TIME" => "36000",	// Время кеширования (сек.)
		"CACHE_GROUPS" => "Y",	// Учитывать группу пользователя
		"COMPONENT_TEMPLATE" => "nx_landing.1.0",
		"SEND_XML" => "Y",	// Отправлять xml
	),
	false
);?>
<?$APPLICATION->IncludeComponent(
	"ip:mailform_4.9", 
	"nx_landing_basket.1.0", 
	array(
		"COMPONENT_TEMPLATE" => "nx_landing_basket.1.0",
		"BUTTON" => "Отправить заказ",
		"COUNT" => "5",
		"PLACEHOLDERS" => "N",

		"F1_NAME" => "Ваше имя",
		"F1_OBLIG" => "Y",
		"F1_TYPE" => "text",
		"F1_CONNECT" => "UF_USER_NAME",

		"F2_NAME" => "Ваш телефон",
		"F2_OBLIG" => "Y",
		"F2_TYPE" => "phone",
		"F2_CONNECT" => "UF_USER_PHONE",

		"F3_NAME" => "Способ доставки",
		"F3_OBLIG" => "Y",
		"F3_TYPE" => "radio",
		"F3_VALS" => array(
			0 => "В пределах МКАД",
			1 => "За МКАД",
			40 => "",
		),
		"F3_CONNECT" => "UF_DELIVERY",

		"F4_NAME" => "Желательная дата доставки",
		"F4_OBLIG" => "Y",
		"F4_TYPE" => "data",
		"F3_CONNECT" => "UF_DELIVERY_DATE",

		"F5_NAME" => "Комментарий",
		"F5_OBLIG" => "N",
		"F5_TYPE" => "textarea",
		"F5_CONNECT" => "UF_COMMENT",

		"FORM_ID" => "zakaz",
		"FORM_TITLE" => "Заказ",
		"FROM" => "info@allacarte.ru",
		"MAIL_RECIPIENT" => "zakaz@allacarte.ru",
		"BCC" => "necris@imageprom.com, liska_m@bk.ru, order@imageprom.com",
		"MANAGER_BACK" => "Y",
		"NAME_MAIL_RECIPIENT" => "Сайт alla-carte.ru",
		"SUBJECT" => "Заказ с сайта",
		"TEXT" => "Отправить",
		"TYPE" => "Заказ",
		"USER_CONNECTION" => "",
		
		"MAGAZINE_CONNECT" => "Y",
		"SHOW_LOG" => "Y",

		"LOG_FORMAT" => "hib",
		"LOG_HIB_ID" => "1",
		"USER_CONNECT" => "none",
		"DATA_CONNECT" => "UF_DATE",
		"TITLE_CONNECT" => "UF_TITLE",
		"SUM_CONNECT" => "UF_SUM",
		"JSON_CONNECT" => "UF_ORDER",
		"ARCHIVE_CONNECT" => "none"

	),
	false
);?>
				</div>
			</div>
		</div>
	</div>
</section>