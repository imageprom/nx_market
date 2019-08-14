<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!$arResult['IS_EMPTY']) foreach($arResult["ELEMENTS"] as $element) $res[] = array('id' => $element['ID'], 'cnt' => $element['COUNT']);
?>
<b class="basket-title">Мой заказ</b>
<div class="nx-basket-state">
<a href="<?=$arResult['BASKET_LINK']?>" >
<?if($arResult['IS_EMPTY']):?>Пока пуст<?else:?>
<b><?=$arResult['COUNT']?></b> товар<?$count=$arResult['COUNT']%10; if($count > 1 && $count < 5) echo 'a'; elseif($count != 1) echo 'ов';?> 
<span>на сумму </span>
<strong><?=NXMarket\nx_fprice($arResult["SUM"])?>&nbsp;<s class="r">Р</s></strong>
<?endif;?>
</a>
<input type="hidden" class="nx-order-item-list" value='<?=json_encode($res)?>' />
</div>
