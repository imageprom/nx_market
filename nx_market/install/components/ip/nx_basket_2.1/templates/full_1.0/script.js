﻿(function(){
$(function(){
jQuery.preventDefaultEvent = function(e, options) {
    options = options || {shift:1, ctrl:1, alt:1, meta:1};
    var href = e.currentTarget.getAttribute('href');
    if(((options.shift && e.shiftKey)
        || (options.alt && e.altKey)
        || (options.ctrl && e.ctrlKey)
        || (options.meta && e.metaKey))
        && href && href.indexOf('#') != 0
        && href.indexOf('javascript:') != 0
    ) return true;
    e.preventDefault();
    return false;}
	
$(function() {$('.nx-to-basket').click(function(e) {if($.preventDefaultEvent(e)) return;});});

var nxBasketLink = $('.nx-basket-state a').attr('href'),
	nxBasketLink = '/ajax/nx_basket.php',

	nxBasketItems =  $('.nx-order-item-list').attr('value');
 if(nxBasketItems) nxBasketItems = JSON.parse(nxBasketItems);

function nxGetItem(id) {
	var result;
	jQuery.each(nxBasketItems, function(k, currentItem) {
		if(id == currentItem.id) {
			result = currentItem;
			return true;
		}
	});
	return result;
}

function GetCurrentItem(id, ost) {
var count = '';
	currentItem = nxGetItem(id);
	if (currentItem) {
		itemClass = '';
		if (currentItem.cnt >= ost) itemClass = 'nx-current-full'
			count = '<span class="nx-current-count '+itemClass+'">'+currentItem.cnt+'</span>';
		return count;
	}
	return count;
} 

function nxShowAviableMessage(target, aviable) {

var toCartControler = target.closest('.nx-basket-byer'),
	message = toCartControler.find('.nx-order-message');
	messagePlace = toCartControler.find('.nx-order-count-block');
	if(!message.length)
		messagePlace.append('<span class="nx-order-message">Для заказа доступно '+aviable+'</span>');
}

function nxHideAviableMessage(target) {	
	var message = target.closest('.nx-basket-byer').find('.nx-order-message');
	if( message.length) message.remove(); 
}

function nxBasketInit() {

	console.log('Init basket');
	console.log('List lenght: ' + $('.nx-order-item-list').length);


	if($('.nx-order-item-list').length > 0) {

		nxBasketItems =  $('.nx-order-item-list').attr('value');
	 if(nxBasketItems) nxBasketItems = JSON.parse(nxBasketItems);
 	
 	}
 	else {
 		$.post(nxBasketLink, {NX_ACTION:'init'}, function(data) {
 			console.log('Ajax init');  		
			nxBasketItems = $(data).find('.nx-order-item-list').attr('value');
			if(nxBasketItems) nxBasketItems = JSON.parse(nxBasketItems);

			console.log(nxBasketItems);
		}); 	
 	}

	$('.nx-basket-byer').each(function( index ) {
	  dataCart = $(this).attr('data-cart');
	  $(this).empty();
	  if(dataCart) {
	  	dataCart = JSON.parse(dataCart);
	  	if(dataCart.ost > 0) {
	  		count = GetCurrentItem(dataCart.id, dataCart.ost);

	  		var countBlock = '<span class="nx-counters">'+
	  						 '<button class="nx-counter up">+1</button>' +
	  						 '<button class="nx-counter down">-1</button>' +
	  						  '</span>' +
	  						 '<input type="text" class="nx-order-count" value="1">';

	  			addButton = '<a href="/basket?NX_ID=' + dataCart.id + '&NX_PRICE=' + dataCart.price + '&NX_ACTION=add" class="nx-to-basket ">В корзину</a>';
		
	  		$(this).append(countBlock + count +  addButton);
	  	}
	  	else $(this).addClass('no-aviable').append('<span class="btn nx-no-aviable">Нет в наличии</span>');
	  }
	});
}

$('body').on('click', '.nx-basket-byer .up', function() {

var toCartControler = $(this).closest('.nx-basket-byer'),
	nxCountInput = toCartControler.find('.nx-order-count'),
	dataCart = toCartControler.attr('data-cart');
	if(dataCart) { dataCart = JSON.parse(dataCart); nx_av = dataCart.ost;}
		nxCountInput_val = parseInt(nxCountInput.val());	
		nxCountInput.val(1 + nxCountInput_val); nxCountInput.change();
});	


$('body').on('click', '.nx-basket-byer .down', function() {

var toCartControler = $(this).closest('.nx-basket-byer'),
	nxCountInput = toCartControler.find('.nx-order-count'),
	dataCart = toCartControler.attr('data-cart');
	if(dataCart) { dataCart = JSON.parse(dataCart); nx_av = dataCart.ost;}
		nxCountInput_val = parseInt(nxCountInput.val());
	if(nxCountInput_val > 1) 
		nxCountInput.val(nxCountInput_val-1);  nxCountInput.change();
});


$('body').on('change', '.nx-basket-byer .nx-order-count', function() {
var toCartControler = $(this).closest('.nx-basket-byer'),
	nxCountInput = toCartControler.find('.nx-order-count'),
	dataCart = toCartControler.attr('data-cart');
 	
 	if(dataCart) {
  		dataCart = JSON.parse(dataCart);
  		nxCountInput_val = parseInt(nxCountInput.val());
			
		if(nxCountInput_val < 1) { 
			nxCountInput.val(1);
			nxHideAviableMessage($(this));
		}
		else {
			if(nxCountInput_val > dataCart.ost) {
				nxCountInput.val(dataCart.ost);
				nxShowAviableMessage ($(this), dataCart.ost);
			}
			else{
				nxHideAviableMessage($(this));
			}
		}
	}
});

$('body').on('click', '.nx-to-basket', function() {
var toCartControler = $(this).closest('.nx-basket-byer'),
	dataCart = toCartControler.attr('data-cart'),
	nxCount = parseInt(toCartControler.find('.nx-order-count').val());
 	if(dataCart) {
  		dataCart = JSON.parse(dataCart); 
  		currentItem  = nxGetItem (dataCart.id);
		if(!currentItem) {currentItem = new Object; currentItem.cnt = 0;}
	
		if( currentItem.cnt < dataCart.ost) {
			
			if(nxCount + currentItem.cnt > dataCart.ost) { 
				nxCount = dataCart.ost - currentItem.cnt;
				nxShowAviableMessage ($(this), dataCart.ost);
			}
				
			$.post(nxBasketLink, {NX_ID:dataCart.id, NX_COUNT:nxCount, NX_PRICE:dataCart.price, NX_NAME:dataCart.name, NX_NOTE:dataCart.note, NX_ACTION:"add"}, function(data) {  		
				var content = $(data).find('.nx-basket-state').contents(); 	
				$('.nx-basket-state').empty().append(content);

				nxBasketItems =  $('.nx-order-item-list').attr('value');
			    if(nxBasketItems) nxBasketItems = JSON.parse(nxBasketItems);
				
				countBlock = toCartControler.find('.nx-current-count');
				countBlock.fadeOut().remove();
				count = GetCurrentItem(dataCart.id, dataCart.ost);
				
				if (count) toCartControler.prepend(count); 
				nxHideAviableMessage($(this));
			});
			
			return false;
		}
		
		nxShowAviableMessage ($(this), dataCart.ost);
	}
	return false;
});

$('body').on('click', '.nx-order-count-block .nx-order-message', function() {
	 $(this).remove();
});

$(function() {$('.nx-del').click(function(e) {if($.preventDefaultEvent(e)) return;});});


$('body').on('click', '.nx-del', function() {

	var nx_ids = $(this).attr('name');

	$.post(nxBasketLink, {NX_ID:nx_ids, NX_ACTION:'delete'}, function(data) { 

		var content = $(data).find('.nx-basket-state').contents(); 	
		$('.nx-basket-state').html(content);

		nxBasketInit();
        
       	
	});

	return false;

});


nxBasketInit();

$(window).scroll(function () {if ($(this).scrollTop() > 100) {$('#NXBasket').addClass('nx-basket-fixed')} else {$('#NXBasket').removeClass('nx-basket-fixed')}});

console.log('End of basket build');

});})(jQuery);