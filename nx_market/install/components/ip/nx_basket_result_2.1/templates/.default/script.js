(function(){
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
    return false;
}

function nx_show_aviable_message(target, aviable) {
	var parent = target.closest(".nx-order-result"),
		message = parent.find('.nx-order-message');
		console.log(aviable);
		console.log(target);
		console.log(parent);
		if(!message.length)
			parent.append('<span class="nx-order-message">Для заказа доступно '+aviable+'</span>');
}

function nx_hide_aviable_message(target) {	
	var message = target.closest(".nx-order-result").find('.nx-order-message');
	if( message.length) message.remove(); 
}

$('body').on("click", '.nx-basket-result .nx-order-message', function() {
	 $(this).remove();
});

$(function() {$(".nx-del").click(function(e) {if($.preventDefaultEvent(e)) return;});});
$(function() {$(".nx-delall").click(function(e) {if($.preventDefaultEvent(e)) return;});});

//var nx_basket_link = $(".nx-basket-state a").attr("href");
//var nx_basket_link = '/basket.php';
var nx_basket_link = '/basket/';

$('body').on("click", '.nx-del', function() {
	var current_item = $(this).closest('.tr'),
		nx_ids = $(this).attr("name");
	
	$.post(nx_basket_link, {NX_ID:nx_ids, NX_ACTION:"delete"}, function(data) {   
		
		var content = $(data).find('.nx-basket-state').contents(); 	
		$(".nx-basket-state").empty().append(content);
		
		var rsum = $(data).find('#nx-res-sum').contents();
		$("#nx-res-sum").empty().append(rsum);
		current_item.remove();

		if (!$(".nx-del").length ) {$(".nx-basket-result").empty().append("<h3 class='nx-empty'>В вашем заказе ничего нет</h3>");}

	});
});
	
$('body').on("click", '.nx-delall', function() {
$.get(nx_basket_link, {NX_ACTION:"delete_all"}, function(data) {  
		var content = $(data).find('.nx-basket-state').contents(); 	
		$(".nx-basket-state").empty().append(content);
		$(".nx-basket-result").empty().append("<h3 class='nx-empty'>В вашем заказе ничего нет</h3>");
	});
});

function item_recount(my_object, direction) {
    
	var current_item = my_object.closest('.tr');
		nx_counter = current_item.find(".nx-count"),			
		nx_ids = current_item.find(".nx-res-id").val(),
		nx_av = parseInt(current_item.find(".nx-res-av").val()),
   		nx_counter_val = parseInt(nx_counter.val());
   		show_message = false;
   	if(direction >= 0 ) {
   		if(nx_counter_val >= nx_av)   {
   			nx_counter_val = nx_av;
   			show_message = true;
   			var index = $('.nx-basket-result .nx-count').index(nx_counter);
   		}
   		else {
   			if(direction > 0) nx_counter_val = 1 + nx_counter_val;
   		}
   	}
	else if (nx_counter_val > 1) {
		nx_counter_val =  nx_counter_val - 1;
	}
	
	$.post(nx_basket_link, {NX_ID:nx_ids, NX_COUNT:nx_counter_val, NX_ACTION:"change"}, function(data) {  
		
		var content = $(data).find('.nx-basket-state').contents(); 	
		$(".nx-basket-state").html(content);
		
		var content = $(data).find('.nx-basket-result'); 
		$(".nx-basket-result").empty().append(content);

		if(show_message) {
			//var index = $('.nx-basket-result .nx-count').index(nx_counter);
			nx_show_aviable_message ($('.nx-basket-result .nx-count').eq(index), nx_av);

		}
	
	});
};

$('body').on("change", '.nx-count', function() {item_recount( $(this), 0);});
$('body').on("click",  '.nx-recount', function() {item_recount( $(this), 1);});
$('body').on("click",  '.nx-basket-result .up', function() {item_recount( $(this), 1);});
$('body').on("click",  '.nx-basket-result .down', function() {item_recount( $(this), -1);});

});})(jQuery);

 