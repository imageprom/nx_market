<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<div class="nx-basket-result">
<?if($arResult['IS_EMPTY']):?><h3 class="nx-empty">В вашем заказе ничего нет</h3>
<?else:
 $order['XML'] = '<products>';
 $order['ARCHIVE'] = '<table><tr><th class="histotyId">Арт.</th><th class="histotyName">Наименование</th><th class="histotyPrice">Цена</th><th class="histotyCount">Количество</th><th class="histotyAmount">Стоимость</th></tr>';
 
 $table_order = 'width:100%; border:none; border-collapse:collapse; background:#fff; margin-top:2px; ';
 $table_order_th = $table_order_td = 'text-align:left; width:40%; padding:5px 10px; border-bottom:1px solid #ddd; line-height:110%; font-size:9pt; empty-cells:show;';
 $table_order_td.= 'background:#fff; ';
 $table_order_th.= 'background:#50597e; color:#fff; border-right:1px solid #ddd; ';
 $table_order_td_price = str_replace('text-align:left', 'text-align:right', $table_order_td).'white-space:nowrap; ';
 $table_order_th_price = str_replace('text-align:left', 'text-align:right', $table_order_th).'white-space:nowrap; ';
 $table_order_td_count = str_replace('text-align:left', 'text-align:center', $table_order_td);
 $table_order_th_count = str_replace('text-align:left', 'text-align:center', $table_order_th);
 $table_order_td_prw = $table_order_td.'width:80px; vertical-align:middle';
 $table_order_td_sum = 'border-left-color:#fff; border-right-color:#fff; font-size:14pt; padding:20px 0 20px 0; text-align:right;';
 $table_order_td_nds = 'border-left-color:#fff; border-right-color:#fff; font-size:10pt; padding:20px 0 20px 0;';

 $order['MAIL_STYLES'] = '
 	table.order {'.$table_order.'}
	table.order td {'.$table_order_td.'},
	table.order th {'.$table_order_th.'},
	table.order td.price {'.$table_order_td_price.'} 
	table.order th.price {'.$table_order_th_price.';} 
	table.order td.prw {'.$table_order_th_prw.';}
	table.order td.count, table.order th.count {text-align:center;}
 						 ';

 $order['MAIL'] = '<h3>Состав заказа</h3>
				   <table class="order" width="100%" cellspacing="0" cellpadding="5"  bgcolor="ffffff" style="'.$table_order.'"">
				   <tr><th colspan="2"   bgcolor="50597e" style="'.$table_order_th.'">Наименование</th>
					   <th class="count" bgcolor="50597e" style="'.$table_order_th_count.'">Цена</th>
					   <th class="count" bgcolor="50597e" style="'.$table_order_th_count.'">Кол-во</th>
					   <th class="price" bgcolor="50597e" style="'.$table_order_th_price.'border-left-color:#fff;">Стоимость</th>
					</tr>';
?>

<input type="hidden" name="NX_ACTION" value="replace" />
<div class = "nx_order">
	<div class="tr thead">
		<div class="th basket-name">Наименование</div>
		<div class="th basket-price">Цена</div>
		<div class="th basket-cnt">Количество</div>
		<div class="th basket-sup">Стоимость</div >
		<div class="th basket-action"><a href="?NX_ACTION=delete_all" class="nx-delall" title="Очистить корзину">Очистить корзину</a>
	</div>	
</div>
<?	if(!$site = SITE_SERVER_NAME) $site = $_SERVER["HTTP_HOST"];
		$site = 'http://'.str_replace('http://', '', $site);

	foreach ($arResult['ITEMS'] as $cnt => $arItem):
		
		if (!$arItem['ID'] ) {$arItem['ID'] = $cnt;}
		
		$arItem['SUM'] = $arItem['ORDER']['PRICE'] * $arItem['ORDER']['COUNT'];
		$arItem['NAME_MOD'] = htmlentities($arItem['NAME'], ENT_QUOTES, "UTF-8");
		$arItem['ART'] = $arItem['XML_ID'];
		$arItem['DISCOUNT_PRICE'] = $Item['ORDER']['PRICE'] * (1 - $arResult['DISCOUNT']/100);

		if($arResult['DISCOUNT'] > 0) {
			$Item['ORDER']['NOTE']['DISCOUNT_PRICE'] = $arItem['DISCOUNT_PRICE'];
			$Item['ORDER']['NOTE']['DISCOUNT'] = $arResult['DISCOUNT'];
		}
	
		if(!$arItem['UNIT'] = strtolower($arItem['PROPERTIES'][$arParams['UNIT_CODE']]['VALUE'])) $arItem['UNIT'] ='шт.';

		$arItem['TAIL'] = $arItem['PROPERTIES']['ost_52']['VALUE'];
		$noAviable = $arItem['TAIL'] ? false : true;	

		if(!$noAviable):
			$order['XML'] .= '<product>
									 <id>'.$arItem['XML_ID'].'</id>
									 <art>'.$arItem['ART'].'</art>
					                 <name>'.$arItem['NAME_MOD'].'</name>
									 <price>'.$arItem['ORDER']['PRICE'].'</price>
									 <count>'.$arItem['ORDER']['COUNT'].'</count>
									 <sum>'.($arItem['SUM']).'</sum>
							  </product>';	
			  
			$order['MAIL'] .= '<tr><td width="80" class="prw"  style="'.$table_order_td_prw.'" >';
		
			if($arItem['PREVIEW_PICTURE']['SRC']) 
				$order['MAIL'] .= '<img src="'.$site.$arItem['PREVIEW_PICTURE']['SRC'].'" alt="'.$arItem['NAME_MOD'].'"  width="80" /></td>';
			else 
				$order['MAIL'] .= '&nbsp;.</td>';

		    $order['MAIL'] .= '<td style="'.$table_order_td.'"><b>'.$arItem['NAME'].'</b><br />арт.'.$arItem['ART'].'</td>
				               <td class="price" style="'.$table_order_td_price.'">'.NXMarket\nx_fprice($arItem['ORDER']['PRICE']).'&nbsp;руб.</td>
				               <td class="price" style="'.$table_order_td_price.'">'.$arItem['ORDER']['COUNT'].'&nbsp;'.$arItem['UNIT'].'</td>
				               <td class="price" style="'.$table_order_td_price.'">'.NXMarket\nx_fprice($arItem['SUM']).'&nbsp;руб.</td>
				               </tr>';
		
			$order['ARCHIVE'] .= '<tr>
							      <td class="histotyId">'.$arItem['ART'].'</td>
							      <td class="histotyName">'.$$arItem['NAME_MOD'].'</td>
							      <td class="histotyPrice"">'.NXMarket\nx_fprice($arItem['ORDER']['PRICE']).' руб.</td>
							      <td class="histotyCount">'.$arItem['ORDER']['COUNT'].'</td>
							      <td class="histotyAmount">'.NXMarket\nx_fprice($arItem['SUM']).' руб.</td>
						          </tr>';
			
		

			$order['GOOGLE'][] =  array(
				'sku' => $arItem['XML_ID'], 
				'name' => $arItem['NAME'], 
				'category' => $arItem['SECTION'], 
				'price' => $arItem['DISCOUNT_PRICE'], 
				'quantity' => $arItem['ORDER']['COUNT']
			);

			$finalOrder[$Item['ID']] = $Item['ORDER'];				  	
			?>

			<div class="tr">

				<div class="td basket-item basket-content">
					<input type="hidden" name="NX_ITEMS[<?=$arItem['ID']?>][NX_ID]" value="<?=$arItem['ID']?>" class="nx-res-id" />
					<div class="prw-block">
					<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="prw<?if(!$arItem['PREVIEW_PICTURE']['SRC']):?> nophoto<?endif;?>">
						<?if($arItem['PREVIEW_PICTURE']):?>
							<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" <?if($arItem['PREVIEW_PICTURE']['WIDTH'] < $arItem['PREVIEW_PICTURE']['HEIGHT']):?>class="vertical"<?endif;?> />
						<?else:?>
							<?=$arItem['NAME']?>
						<?endif;?>
					</a>
					</div>
					<div class="ttl-block">
						<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="ttl"><?=$arItem['NAME']?></a>
						<small class="art">Арт. <?=$arItem['ART']?></small>
					</div>
				</div>

				<div class="td basket-price"><?=NXMarket\nx_fprice($arItem['ORDER']['PRICE'])?><s class="r">Р</s>
				     <input type="hidden"  name="NX_ITEMS[<?=$arItem['ID']?>][NX_PRICE]" value="<?=$arItem['ORDER']['PRICE']?>" />
				</div>

				<div class="td basket-count">
					<span class="nx-order-result nx-order-count-block">
				        <input type="hidden" value="<?=$arItem_tail?>" class="nx-res-av" /> 
						<input type="tel" value="<?=$arItem['ORDER']['COUNT']?>" name="NX_ITEMS[<?=$arItem['ORDER']['ID']?>][NX_COUNT]" class="nx-count inpt nx-order-count"/>
						<span class="nx-counters"><i class="nx-counter up">+1</i><i class="nx-counter down">-1</i></span>
						<span class="unit"><?=$arItem['UNIT']?></span>
					</span>
				</div>

				<div class="td basket-price basket-price-sum">
					<?=NXMarket\nx_fprice($arItem['SUM'])?><s class="r">Р</s>
				</div>

				<div class="td basket-action">
					<a href="?NX_ID=<?=$arItem['ID']?>&&NX_ACTION=delete"  class="nx-del" title="Удалить" name="<?=$arItem['ID']?>'">удалить</a>
				</div>

			</div>
		<?else: 
			$arResult['SUM_NO_NDS'] -= $arItem['SUM']; 
			$arResult['ITEM_COUNT'] --; 
			unset($arResult['ORDER'][$arItem['ID']]);
		?>
		<?endif;?>
	<?endforeach;?>
	<?
		if($arResult['DISCOUNT'] > 0) {
			$arResult['SUM_NO_NDS'] *= 1 - $arResult['DISCOUNT']/100;
		}
		
		$arResult['NDS'] = $arResult['SUM_NO_NDS'] * (1 + ($arParams['PRICE_NDS'] / 100)) - $arResult['SUM_NO_NDS'];
		$arResult['SUM'] = $arResult['NDS'] + $arResult['SUM_NO_NDS'];
		$arResult['FULL_SUM'] = $arResult['SUM'];
	?>

	<?if($order['DELIVERY_SUM'] > 0) {
		$order['XML'] .= '<delivery>'.$arResult['DELIVERY_SUM'].'</delivery>';
		$order['MAIL'].= '<tr><td align="right" colspan="5" style="'.$table_order_td_sum.'"><b style="'.$table_order_td_sum_b.'">Доставка: </b>'.NXMarket\nx_fprice($arResult['DELIVERY_SUM']).' руб.</td></tr>';
		$arResult['FULL_SUM'] = $arResult['SUM'] + $arResult['DELIVERY_SUM'];
	}
	?>

    <? $order['XML'] .= '</products><itog>'.$arResult['SUM'].' </itog>';
	   $order['MAIL'] .= '<tr><td align="right" colspan="5" style="'.$table_order_td_sum.'"><b>Итого: </b>'.NXMarket\nx_fprice($arResult['SUM']).' руб.</td></tr>';?>
	</div>
	
	<div class="nx-res-sum" id="nx-res-sum" >
		<b>Итого к оплате: </b>
		<span><?=NXMarket\nx_fprice($arResult['SUM'])?>&nbsp;<s class="r">Р</s></span>
	</div>    

	<?if($arResult['NDS'] > 0):?>
		<div class="nx-res-nds"id="nx-res-nds">
			<b>в т.ч. НДС: </b>
			<span><?=NXMarket\nx_fprice($arResult['NDS'])?>&nbsp;&nbsp;<s class="r">Р</s></span>
		</div>

		<?$order['XML']  .= '<nds>'.$arResult['NDS'].' </nds>';
		  $order['MAIL'] .= '<tr><td align="right" colspan="5" style="'.$table_order_td_nds.'"><b>в т.ч. НДС: </b>'.NXMarket\nx_fprice($arResult['NDS']).' руб.</td></tr>';?>
	<?endif;?>

	<?$order['MAIL'] .= '</table>';
	  
	  if($arParams['SEND_XML'] == 'N') unset($order['XML']);
	  
	  $order['ARCHIVE'] .='</table>';

	  $order['VAR'] = $arParams['ORDER_ARRAY_NAME'];
	  $order['JSON'] = json_encode($finalOrder);

	  $order['SUM'] = $arResult['SUM'];
	  $order['FULL_SUM'] = $arResult['FULL_SUM'];
	  $order['DELIVERY_SUM'] = $arResult['DELIVERY_SUM'];

	  $order['COUNT'] = $arResult['ITEM_COUNT'];
	  $order['NDS'] = $arResult['NDS'];  

	  if($arResult['DISCOUNT'] > 0) { 
	  	$order['DISCOUNT'] = $arResult['DISCOUNT'].'%';
	  	$order['XML'] .= '<discount>'.$arResult['DISCOUNT'].'%</discount>';
	  }

	  $order['XML'] .= '<full>'.$arResult['SUM'].' </full>';
	  
	  global $NX_BASKET_RESULT_DATA;
	  $NX_BASKET_RESULT_DATA = $order;
	?>
<?endif;?>
</div>