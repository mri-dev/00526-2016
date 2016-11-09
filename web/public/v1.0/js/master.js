$(function(){
	searchFilters();
	var transports_c 			= $('.transports');

	if(transports_c.length ){
		var transports_c_from_top 	= $('.transports').offset().top;
	}

	var footer_height 			= $('#footer').height();

	$(document).scroll(function(){
		var top = $(this).scrollTop();

		if(top > 200){
			if($('#topper .upTop').css('display') == 'none'){
				$('#topper .upTop').fadeIn(400);
			}
		}else{
			$('#topper .upTop').fadeOut(400);
		}

		// Transports box changes
		if(transports_c.length ){
			var transparent_c_from_bottom = $(document).height() - (transports_c.height() + $('.transports').offset().top);

			if(transparent_c_from_bottom != footer_height)
			if ( ( top + $('#topper').height() ) > transports_c_from_top ) {

				if ( transparent_c_from_bottom <= footer_height ) {
					transports_c.removeClass('toTopFix').addClass('inFooter');
				}else {
					transports_c.removeClass('inFooter').addClass('toTopFix');
				}
			} else {
				transports_c.removeClass('toTopFix').removeClass('inFooter');
			}
		}


		// Cart-Box
		/*if(top > (250 - 30)){
			$('.cart-box').css({
				top: (top-150)+'px'
			});
		}else{
			$('.cart-box').css({
				top: '-2px'
			});
		}	*/
	});

	// Auto Resizer
	//autoresizeImages();

	var width = $(window).width();
	$(window).resize(function(){
	   if($(this).width() != width){
	      //autoresizeImages();
	   }
	});

	// Dropdown select mobil click helper
	$('.dropdown-list-container').click( function(){
		var list = $(this).find('.dropdown-list-selecting');

		if( list.hasClass('showed') ){
			$(this).find('.dropdown-list-selecting').removeClass('showed');
		} else {
			$(this).find('.dropdown-list-selecting').addClass('showed');
		}

	} );
	$('.cart-float').click( function(){
		var view = $(this);

		if( view.hasClass('opened') ){
			$(this).removeClass('opened');
		} else {
			$(this).addClass('opened');
		}

	} );



	$('.social-fb-box').bind({
		mouseenter: function(){
			$(this).animate({
				right: 0
			}, 100);
		},
		mouseleave: function(){
			$(this).animate({
				right: -310
			}, 100);
		}
	});

	$('#topper .upTop > a').click(function(){
		$('body,html').animate({
			scrollTop: 0
		}, 800);
	});

	$('.con i.hbtn').click(function(){
		var key = $(this).attr('key');

		$('.newWire.'+key).slideToggle(200);
	});
	var prevKey = null;
	$('.selector').click(function(){

		var key = $(this).attr('key');
		if(key != prevKey){
		  $('.selectors .selectorHint').slideUp(100);
		}
		$('.selectorHint.'+key).slideToggle(200);
		prevKey = key;
	});

	$('.param input[mode!=minmax], .param select').each(function(){
		checkUsedParam($(this));
	});
	$('.param input[mode=minmax]').each(function(){
		checkUsedMinMaxParam($(this));
	});

	$('.selectorHint input[type=checkbox][for]').click(function(){
		var fr 			= $(this).attr('for');
		var selText 	= '';
		var selVal 		= '';

		$('.selectorHint input[type=checkbox][for='+fr+']').each(function(){
			var fr 			= $(this).attr('for');
			var selected 	= $(this).is(':checked');
			var text 		= $(this).attr('text');
			var val 		= $(this).val();

			if(selected){
				selText += text+", ";
				selVal 	+= val+",";
			}

		});

		if(selText == ''){
			selText = ($(this).attr('defText')) ? $(this).attr('defText') : 'összes' ;
		}else{
			selText = selText.slice(0,-2);
		}
		if(selVal == ''){
			selVal = '';
		}else{
			selVal = selVal.slice(0,-1);
		}
		$('#'+fr).text(selText);
		$('#'+fr+'_v').val(selVal);

	});

	$('.szuro .param i.ips').click(function(){
		removeFilterItem($(this));
	});

	$('*[autoOpener]').each(function(){
		var e = $(this);
		var flag = e.attr('autoOpener');
		var state = localStorage.getItem(flag);

		if(!state){
			$(this).css({'display':'block'});
		}else{
			$(this).css({'display':'none'});
		}
	});

	initRanges();

	$('.product-view a.zoom, a.zoom').fancybox({
		padding: 0,

		openEffect : 'elastic',
		openSpeed  : 250,

		closeEffect : 'elastic',
		closeSpeed  : 250,

		closeClick : true,

		helpers : {
			overlay : null,
			buttons	: {
				position : 'bottom'
			},
			title: {
				type: 'over'
			}
		}
	});

	$('#slideShow').slick({
	  dots: true,
	  infinite: true,
	  speed: 600,
	  slidesToShow: 1,
	  slidesToScroll: 1,
	  autoplay: true,
  	  autoplaySpeed: 8000,
  	  adaptiveHeight: true,
  	  responsive : [
  	  	{
  	  		breakpoint: 480,
  	  		settings : {
  	  			mobileFirst : true,
  	  			respondTo : window
  	  		}
  	  	}
  	  ]
	});

	var hlviewer = setInterval( function(){
		var key = $('.highlight-view .items a[key]').attr('key');
		var item_nums = $('.highlight-view .items li').size();
		var current = parseInt($('.highlight-view .items li[class*=active]').attr('index'));
		var next = current + 1;
		var prev = current - 1;

		if ( next > item_nums ) {
			next = 1;
		}

		if ( prev <= 0 ) {
			prev = item_nums;
		};

		$('.highlight-view .items li').removeClass('active');

		if ( key == 'next' ) {
			$('.highlight-view .items li[index='+next+']').addClass('active');
		} else {
			$('.highlight-view .items li[index='+prev+']').addClass('active');
		}

	}, 2500);


	$('.highlight-view .items a[key]').click( function(){
		var key = $(this).attr('key');
		var item_nums = $('.highlight-view .items li').size();
		var current = parseInt($('.highlight-view .items li[class*=active]').attr('index'));
		var next = current + 1;
		var prev = current - 1;

		window.clearInterval(hlviewer);

		if ( next > item_nums ) {
			next = 1;
		}

		if ( prev <= 0 ) {
			prev = item_nums;
		};

		$('.highlight-view .items li').removeClass('active');

		if ( key == 'next' ) {
			$('.highlight-view .items li[index='+next+']').addClass('active');
		} else {
			$('.highlight-view .items li[index='+prev+']').addClass('active');
		}
	});

	$('a[href*=#]:not([href=#])').click(function(){
		var hash 	= $(this).attr('href').replace('#','');
		var target 	= $('a[name='+hash+']');

		$('html,body').animate({
          scrollTop: target.offset().top - 40
        }, 500);

		return false;
	});
	// Mobile Device events
	$('*[mb-event]').each( function(i){
		var _ =  $(this).data('mb');

		switch (_.event) {
			case 'toggleOnClick':

				if ( _.target ) {
					$(_.target).unbind('mouseenter mouseleave click');
					$(this).click( function(){
						var t = $(_.target);
						var opened = t.hasClass('opened');
						if ( opened ) {
							t.removeClass('opened');
						} else {
							$('.mb-tgl-close').removeClass('opened');
							t.addClass('opened');
						}
					});
				}
			break;
		}

	} );

	// Mobile Device max Width
	$('*.mobile-max-width').each( function(){
		if ( width <= 480 ) {
			$(this).css({
				width: width,
				maxWidth : width
			});
		}
	});
})

function autoresizeImages(){
	var images = $('.img-auto-cuberatio');

	images.each( function( index, img ){
		var ie = $(img);
		var width = ie.width();

		ie.css({
			height : width
		});
	});
}

function initRanges(){
	$('.range').each(function(){
		var e 		= $(this);
		var key 	= e.attr('key');
		var smin 	= parseInt(e.attr('smin'));
		var smax 	= parseInt(e.attr('smax'));
		var amin 	= parseInt(e.attr('amin'));
		var amax 	= parseInt(e.attr('amax'));

		smin = (isNaN(smin)) ? amin : smin;
		smax = (isNaN(smax)) ? amax : smax;

		e.slider({
			range: true,
			min: amin,
			max: amax,
			step: 1,
			values: [smin,smax],
			slide: function(event, ui){
				$('#'+key+'_range_min').val(ui.values[0]);
				$('#'+key+'_range_max').val(ui.values[1]);
				$('#'+key+'_range_info_min').text(ui.values[0]);
				$('#'+key+'_range_info_max').text(ui.values[1]);
				postFilterForm();
			}
		});
	});
}

function Cart(){
	this.content = "#cartContent";
	this.push = function(i){
		var oi = $(this.content).find(".item");
		var ec = '<div class="item i'+i.termekID+'">'+
		'<div class="img">'+
			'<div class="img-thb">'+
			'<span class="helper"></span>'+
			'<img src="'+i.profil_kep+'" alt="'+i.termekNev+'" name="'+i.termekNev+'"/>'+
			'</div>'+
		'</div>'+
		'<div class="info">'+
			'<div class="adder">'+
				'<i class="fa fa-minus-square" title="Kevesebb" onclick="Cart.removeItem('+i.termekID+')"></i>'+
				'<i class="fa fa-plus-square" title="Több" onclick="Cart.addItem('+i.termekID+')"></i>'+
			'</div>'+
			'<div class="remove"><i class="fa fa-times "  onclick="Cart.remove('+i.termekID+');" title="Eltávolítás"></i></div>'+
			'<div class="name"><a href="'+i.url+'">'+i.termekNev+'</a> <span class="in">x '+i.me+'</span></div>'+
			'<div class="sub"><div class="tipus">Szín: <span class="val">'+((i.szin) ? i.szin+'</span>' : '')+''+( (i.meret)?', Méret: <span class="val">'+i.meret+'</span>':'')+'</div><span class="ar">'+( (i.ar != '-1')? i.ar+' Ft / db' : 'Ár: érdeklődjön' )+'</span></div>'+
		'</div>'+
		'<div class="clr"></div></div>';
		if(oi.length == 0){
			$(this.content).html(ec);
		}else{
			$(ec).insertAfter(this.content+' .item:last');
		}
	}
	this.addItem = function(id){
		var parent = this;
		$.post('/ajax/post/',{
			type : 'cart',
			mode : 'addItem',
			id 	 : id
		},function(d){
			var p = $.parseJSON(d);
			if(p.success == 1){
				getCartInfo(function(e){
					refreshCart(e);
					parent.reLoad(e);
				});
			}else{
				aler(p.msg);
			}
		},"html");
	}
	this.removeItem = function(id){
		var parent = this;
		$.post('/ajax/post/',{
			type : 'cart',
			mode : 'removeItem',
			id 	 : id
		},function(d){
			var p = $.parseJSON(d);
			if(p.success == 1){
				getCartInfo(function(e){
					refreshCart(e);
					parent.reLoad(e);
				});
			}else{
				aler(p.msg);
			}
		},"html");
	}
	this.reLoad = function(e){
		$(this.content).html('<div class="noItem"><div class="inf">A kosár üres</div></div>');
		buildCartItems(e);
	}
	this.remove = function(id){
		var c = this.content;
		var parent = this;
		$.post('/ajax/post/',{
			type : 'cart',
			mode : 'remove',
			id 	 : id
		},function(d){
			var p = $.parseJSON(d);
			if(p.success == 1){
				$(c+' .item.i'+id).remove();
				var oi = $(c).find(".item");
				if(oi.length == 0){
					$(c).html('<div class="noItem"><div class="inf">A kosár üres</div></div>');
				}
				getCartInfo(function(e){
					refreshCart(e);
					parent.reLoad(e);
				});
			}else{
				aler(p.msg);
			}
		},"html");
	}
}

var Cart = new Cart();

function createFilterArRange(smin, smax, amin, amax){
	amin = parseInt(amin);
	amax = parseInt(amax);
	smin = parseInt(smin);
	smax = parseInt(smax);

	smin = (typeof smin === 'undefined' || isNaN(smin)) ? 0 : smin;
	smax = (typeof smax === 'undefined' || isNaN(smax)) ? 250000 : smax;

	amin = (typeof amin === 'undefined' || isNaN(amin)) ? 0 : amin;
	amax = (typeof amax === 'undefined' || isNaN(amax)) ? 250000 : amax;

	$('#arShow').text("("+amin+" - "+amax+")");

	$('#filter_termekAr_range').slider({
      range: true,
      min: amin,
      max: amax,
	  step: 1000,
      values: [ smin, smax ],
      slide: function( event, ui ) {
      	$( "#fil_ar_min" ).val(ui.values[ 0 ]);
		$( "#fil_ar_max" ).val(ui.values[ 1 ]);
		$('#arShow').text("("+ui.values[0]+" - "+ui.values[1]+")");
		postFilterForm();
      }
    });
}
function openCloseBox(elem, flag){
	var flagState 	= localStorage.getItem(flag);
	var disp 		= $(elem).css('display');
	if(disp == 'none'){
		localStorage.removeItem(flag);
		$(elem).toggle("slide");
	}else{
		localStorage.setItem(flag,11);
		$(elem).toggle("slide");
	}

	console.log(flagState);
}
function removeFilterItem(e){
	var key = e.attr('key');
	var mode = e.attr('mode');

	if(mode == 'range'){
		$('#'+key+'_min').val('');
		$('#'+key+'_max').val('');
	}else{
		$('#'+key+'_v').val('');
		$('#'+key).text('összes').removeClass('filtered');
	}
	e.remove();
}
function checkUsedMinMaxParam(e){
	var key = e.attr('id');
	var v 	= e.val();
	if(key.search('_min') > -1){
		key = key.replace('_min','');
		if(v != ''){
			$("<i class='fa fa-times ips' mode='range' key='"+key+"'></i>").insertBefore('#'+key+'_min');
		}
	}
}
function checkUsedParam(e){
	if(e.attr('type') == 'hidden'){
		var v 	= e.val();
		var id 	= e.attr("id");
		if(v != ''){
			var key = id.replace('_v','');
			$("<i class='fa fa-times ips' key='"+key+"'></i>").insertBefore('#'+key);
		}
	}
}
function searchFilters(){

	$('.selectorHint input[type=checkbox][for]').each(function(){
		var fr = $(this).attr('for');
		var ch = $(this).is(':checked');
		var selText 	= '';
		var selVal 		= '';

		$('.selectorHint input[type=checkbox][for='+fr+']').each(function(){
			var fr 			= $(this).attr('for');
			var selected 	= $(this).is(':checked');
			var text 		= $(this).attr('text');
			var val 		= $(this).val();

			if(selected){
				$('#'+fr).addClass('filtered');
				selText += text+", ";
				selVal 	+= val+",";
			}

		});

		if(selText == ''){
			selText = $(this).attr('defText');
		}else{
			selText = selText.slice(0,-2);
		}
		if(selVal == ''){
			selVal = '';
		}else{
			selVal = selVal.slice(0,-1);
		}
		$('#'+fr).text(selText);
		$('#'+fr+'_v').val(selVal);

	});

	$('button[cart-data]').click(function(){

		var key = $(this).attr('cart-data');
		var rem = $(this).attr('cart-remsg');
		var me 	= $('input[cart-count='+key+']').val();

		//if(typeof me === 'undefined'){ me = 0; }

		$('#'+rem).html('<i class="fa fa-spin fa-spinner"></i> folyamatban...');

		addToCart(key, me, function(success, msg){
			if (success == 1) {
				$('#'+rem).html('<div class="success">'+msg+'</div>');
			} else {
				$('#'+rem).html('<div class="error">'+msg+'</div>');
			}
		} );
	});

	getCartInfo(function(e){
		refreshCart(e);
		buildCartItems(e);
	});
}
function buildCartItems(c){
	var i = c.items;

	for(var s = 0; s < i.length; s++){
		var e = i[s];
		Cart.push(e);
	}
}

function getCartInfo(callback){
	$.post('/ajax/get/',{
		type : 'cartInfo'
	},function(d){
		var p = $.parseJSON(d);
		callback(p);
	},"html");
}
function refreshCart(p){
	$('#cart-item-num-v').text(p.itemNum);
	$('#cart-item-num').text(p.itemNum);
	$('.cart-item-num').text(p.itemNum);
	$('#cart-item-prices').text(p.totalPriceTxt);

	if( p.itemNum > 0 ){
		$('.cart-box').show(0);
	}else{
		$('.cart-box').hide(0);
	}
}

function addToCart(termekID, me, callback){
	$.post('/ajax/post/',{
		type : 'cart',
		mode : 'add',
		t 	 : termekID,
		m    : me
	},function(d){
		var p = $.parseJSON(d);
		if(p.success == 1){
			getCartInfo(function(e){
				refreshCart(e);
				Cart.reLoad(e);
			});
		}
		callback(p.success, p.msg);
	},"html");
}
