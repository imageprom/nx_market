<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<?
function NXPluralForm($n, $form1, $form2, $form5) {
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $form5;
    if ($n1 > 1 && $n1 < 5) return $form2;
    if ($n1 == 1) return $form1;
    return $form5;
}
?>
<div class="nx-basket-state">
	<ins class="h1">Корзина</ins>
	<?if($arResult["IS_EMPTY"]):?>
		<div class="spot-count"></div>
			<div class="nx-basket-inside">
			<b></b>
			<span class="go">Ваша корзина пуста</span>
		</div>
	<?else:?>
		<div class="spot-count"><u><?=$arResult['COUNT']?></u></div>
		<div class="nx-basket-inside">
			<b><?=$arResult["COUNT"]?> <?=NXPluralForm($arResult['COUNT'], 'товар', 'товарa', 'товаров')?> на <strong><?=NXMarket\nx_fprice($arResult['SUM'])?><s class="r">Р</s></strong></b>
			<a href="<?=$arResult['BASKET_LINK']?>" class="go" href="<?=$arResult['BASKET_LINK']?>?action=buy">Оформить покупку</a>
		</div>
	<?endif;?>
	<input type="hidden" class="nx-order-item-list" value='<?=json_encode($arResult['JSON_ITEMS'])?>' />


	<div class="nx-basket-small">
	    <div class="nx-basket-small-inside">
	        <?foreach ($arResult['ELEMENTS'] as $cnt => $arItem):?>
	         
	           <?$arItem['SUM'] = $arItem['PRICE'] * $arItem['COUNT'];?>
	            <div class="nx-basket-small-items nx-flex-row-l-c">
	               	<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="prw<?if(!$arItem['PREVIEW_PICTURE']['SRC']):?> nophoto<?endif;?>">
	                    <?if($arItem['PREVIEW_PICTURE']):?>
	                        <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" <?if($arItem['PREVIEW_PICTURE']['WIDTH'] < $arItem['PREVIEW_PICTURE']['HEIGHT']):?>class="vertical"<?endif;?> />
	                    <?else:?>
	                        <?=$arItem['NAME']?>
	                    <?endif;?>
	                </a>
	                
	                <div class="description">
	                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="ttl"><?=$arItem['NAME'];?></a>
	                    <span class="nx-flex-row item-info"><?=$arItem['COUNT']?> шт. на сумму <?=NXMarket\nx_fprice($arItem['SUM'])?><s class="r">Р</s></span>
	                </div>
	                
	                <a href="?NX_ID=<?=$arItem['ORDER']['ID']?>&&NX_ACTION=delete"  class="nx-del" title="Удалить" name="<?=$arItem['ID']?>"></a>

	            </div>
	        <?endforeach;?>
	        <?if($arResult['COUNT']>0):?>
	            <a class="buy-order" href="<?=$arResult['BASKET_LINK']?>?action=buy">Оформить покупку</a>
	        <?endif;?>
	    </div>
	</div>

</div>



