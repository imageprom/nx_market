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
				<?$APPLICATION->IncludeComponent(
					"ip:nx_basket_2.0", 
					'nx_landing.1.0', 
					array(
						"ORDER_ARRAY_NAME" => "NX_BASKET",
						"PRICE_CURRENCY" => "Р",
						"BASKET_RESULT_LINK" => "/basket",
						"COMPONENT_TEMPLATE" => "",
						"COMPOSITE_FRAME_MODE" => "A",
						"COMPOSITE_FRAME_TYPE" => "AUTO"
					),
					false
				);
			?>
				</div>
			</div>
		</div>
	</div>
</section>