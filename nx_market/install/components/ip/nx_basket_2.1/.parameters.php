<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>
<?$arComponentParameters = array(
	'PARAMETERS' => array(

		'ORDER_ARRAY_NAME' => Array(
			'NAME'=> GetMessage('NX_BASKET_VAR_NAME'),
			'TYPE' => 'STRING',
			'DEFAULT' => 'NX_BASKET',
			'VALUE' => 'NX_BASKET',
			'PARENT' => 'BASE',
		),
		
		'PRICE_CURRENCY' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('NX_BASKET_CURRENCY'),
			'TYPE' => 'STRING',
			'DEFAULT' => 'руб.',
		),

        'BASKET_LINK' => Array(
			'NAME'=> GetMessage('NX_BASKET_LINK'),
			'TYPE' => 'STRING',
			'DEFAULT' => '/basket/',
			'VALUE' => '/basket/',
			'PARENT' => 'BASE',
			
		) 
    )		
);
?>