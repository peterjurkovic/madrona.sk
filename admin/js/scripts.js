function createClasses(){
	$('tr:even').addClass('odd');
  	//$('tr:odd').removeClass('odd');
} 

function showStatus(data){
	var html = '<p class="'+ (data.err === 0 ? "ok" : "err") +'">'+ data.msg +'</p>',
	o = $("#status");
	o.html(html).center().fadeIn();
	setTimeout(function() {o.fadeOut(100);}, 4000);
}

function renameArr(a){
	var d = {};	
	for (i in a) {
		d[a[i].name] = a[i].value;
	}
	return d;
}

jQuery.extend({
	getCount: function(data) {
		var r = false;
		$.ajax({
			url	: "./inc/ajax.get.php",
			type: 'get',
			data : data,
			dataType : 'json',
			async: false,
			success: function(json) {
				if(json.count === 0) r = true;
			}
		});
		return r;
	}
});

function validate(f){
	var inputs = f.find('input.required, textarea.required'),
	valid = true,

	vldt = {
		required : function(v,i) { return { r : !!v ,  msg : 'Nie sú výplnené povinné hodnoty'}; },
		email	 : function(v,i) { return { r : v.match( /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/ ), msg : 'Neplatná e-mailová adresa'}; },
		fiveplus : function(v,i) { return { r : v.length >= 5, msg : 'Hodnota musí mať min. 5 znakov'} ;},
		numeric  : function(v,i) { return { r : !isNaN(v), msg : 'Hodnota '+v+' nie je číslo.'} ;},
		unique   : function(v,i) { var d = {coll : i.attr("name"),id : $('input[name=id]').eq(0).val(),table : i.parents("form").eq(0).attr("name"),val:v,act : 21 };
			return { r : $.getCount(d), msg : 'Hodnota <strong>'+v+'</strong> sa už v databáze nachádza.' }
		}
	};
	inputs.removeClass('formerr');
	inputs.each(function(){
		var input = $(this),
			val = input.val(),
			cls = input.attr("class").split(' ');

		for(i in cls){
			if(vldt.hasOwnProperty(cls[i])){
				var res = vldt[cls[i]](val,input);
				if(!res.r){
					input.addClass('formerr');
					showStatus({ err : 1, msg : res.msg});
					valid = false;
				}
			}
		}
	});
	return valid;	
}

// ZMENA PORADIA -----------------------------------------------------------
	function tableDNDinit(){
			$("#dnd").tableDnD({
			onDragClass: "myDragClass",
			onDrop: function(table, row) {
				var rows = table.tBodies[0].rows,
				order = parseInt(table.start.replace("o","").replace(" odd", "").replace(" mark", "")),
				data = {
					orderStr : "",
					table : $("#dnd").find('tbody').attr("class"),
					act : 2
				};
				
				for (var i=0; i < rows.length; i++) {
					 data.orderStr += rows[i].id + "-" + (order + i);
				}
				
				$("#dnd tbody").find("tr").each(function(index){
					$(this).removeClass().addClass("o"+ order);
					order++;
				});
				
				$.getJSON("./inc/ajax.get.php?cb=?", data, function(json) {  
					showStatus(json);
				});
				
			   createClasses();
			},
			onDragStart: function(table, row) {
				table.start = $("#dnd tbody tr:first").attr("class");
			}
		});
	}  
	

$(function() {
	var get = "./inc/ajax.get.php?cb=?";
	createClasses();
	// menu
	$("nav li.t").hover(function () { $(this).children('ul').show(200); },  function () { $('nav ul ul').hide();});		
	$('.f').prettyFileInput();	
		
	// Clear input on click
	$('.clickClear').click(function(){$(this).val('');return false;});
	
	// GALLERY upload  -----------------------------------------------------------
	function loader2(data, jqForm, options){
		var c = 0;
		for(var i =0; i < 5 ; i++){
			if(data[i].value.length === 0){
				c += 1;
			}else{
				var ext = data[i].value.split('.').pop().toLowerCase();
				if($.inArray(ext, ['png','jpg','jpeg']) == -1) {
					showStatus({ err : 1, msg : "Povolené sú len obrázky typu: png, jpg, jpeg"});
					return false;
				}
			}
		}
		if(c === 5){
			showStatus({ err: 1, msg : 'Nie je vybraný žiadny obrázok.' });
			return false;
		}
		$('#uploader .loader').fadeIn();
		return true;
	}
		
	function showImgs(data)  {
		$('#gallery').html(data);
		var o = $('#uploader');
		o.find('input[type=file]').val('');
		o.find('.file-holder').remove();
		o.find('.loader').fadeOut();
		return false;
	} 
		
	//  DELETING GALLERY imgs -----------------------------------------------------------
	$("#gallery").delegate("#gallery a.del", "click", function(e){
		var	o = $(this),
			data = {
			url : o.attr("title"),
			act : 14
		};
		$.getJSON(get, data, function(json) {  
				if(json.err === 1){
					showStatus(json);
					return false;
				}
				o.parent('.ibox').fadeOut(300);
		}); 
	});
	$('#uploader').ajaxForm({ success: showImgs ,beforeSubmit:  loader2});
		
	// AVATARS upload  -----------------------------------------------------------
	function loader(data, jqForm, options){
		if(data[0].value.length === 0 && data[1].value.length === 0 && data[2].value.length === 0){
			showStatus({ err: 1, msg : 'Nie je vybraný žiadny obrázok.' });
			return false;
		}
		$('#avatars .loader').fadeIn();
		return true;
	}
	
	function processJson(data)  {
		data = jQuery.parseJSON(data);	
		var aid = $('#avatars input[name=id]').val();	 
		if(data.err === 0){
			for(var i = 1; i <= 3; i++){
				if(typeof data["avatar"+i] != "undefined" ){
						getImage({ act: 11, img :data["avatar"+i], aid : aid, eid :  "avatar"+i});
				}
			}
		}
		showStatus(data);
		var o = $('#avatars');
		o.find('input[type=file]').val('');
		o.find('.file-holder').remove();
		o.find('.loader').fadeOut();
		return false;
	} 
	
	function getImage(data){
		$.get('./inc/ajax.get.php',data , function(html){$('#' + data.eid).html(html); } );
		return false;
	}
	
	
	$('#avatars').ajaxForm({ datatype: 'json', success: processJson ,beforeSubmit:  loader});
	
	//  DELETING AVATARs -----------------------------------------------------------
	$("#avatars").delegate("#avatars a.del", "click", function(){
		var	o = $(this),
			data = {
			id : o.attr("href").replace("#id", ""),
			info : o.attr("title").split("#"),
			act : 12
		};
		$.getJSON(get, data, function(json) {  
				if(json.err === 1){
					showStatus(json);
					return false;
				}
				$('#'+data.info[1]).html('<img src="./img/noavatar.png" alt="Nie je nahratý obrazok." />');
		}); 
	});
		
	//  IMAGE HOVER -----------------------------------------------------------
	$(".cbox").delegate(".ibox", "mouseenter", function(){
		$(this).find('a').removeClass("hidden");
	});
	$(".cbox").delegate(".ibox", "mouseleave", function(){
		$(this).find('a').addClass("hidden");
	});

	// ZMENA AKTIVNOSTI -----------------------------------------------------------
	$("table.tc").delegate(".a1, .a0", 'click', function (e) {
			var o = $(this),
			data = {
				id : o.attr("href").replace("#id",""),
				act : 1,
				table : o.parents('tbody').eq(0).attr("class"),
				status : (o.hasClass("a1") ? 0 : 1)
			};
			$.getJSON(get, data, function(json) {  
					if(json.err === 0){o.removeClass().addClass("a"+data.status);}
					showStatus(json);
			});
		return false;
	})
	
	// MAZANIE -----------------------------------------------------------
	$("table.tc").delegate(".del", 'click', function (e) {
			var o = $(this),
			data = {
				id : o.attr("href").replace("#id",""),
				act : 3,
				table : o.parents('tbody').eq(0).attr("class")
			};
			if(!confirm("Skutočne chcete zmazať položku ID : "+ data.id +" ?")){
				return false;
			}
			$.getJSON(get, data, function(json) {  
					if(json.err === 0){
						o.parent().parent().hide(1000);	
					}
					showStatus(json);
					createClasses();
			});
		return false;
	})
	
	// AUTOCOMPLETE -----------------------------------------------------------
	$( "input[name=q]" ).autocomplete({
			 focus: function( event, ui ) {
				$( "input[name=q]" ).val( ui.item.label );
				return false;
			},
			 source: function(reques, response){  
			 	 reques.act = 5;
				 reques.table = $('input[name=q]').attr("id");
				 var s = reques.table.split("-"),
				 id = s[0] = 'id_' +s, val = s[1];
			 	 $.getJSON(get, reques, function(data) {  
                 	 response( $.map( data, function( item ) {
							return {label: item[val], value: item[val]}
						}));
            	});  
			 },
			minLength: 1,
			select: function( event, ui ) {
				ui.item.value;
			}
	});
	
	
	// SEARCH -----------------------------------------------------------
	$('.search').submit(function (){
		var data = {
			q : $('input[name=q]').val(),
			table : $("#dnd").find('tbody').attr("class"),
			act : 6
		}
		if(data.q.length  === 0){
			return false;
		}
		$.getJSON(get, data, function(json) {  
					if(json.err === 1){
						return false;
					}
					$('#dnd tbody').html(json.html);
					$(".navigator").first().remove();
					$('.right .breadcrumb').html('<strong>Výsledky vyhľadávania: '+ data.q +'</strong>');
					createClasses();
			}); 
		
		return false;
	})
	
	
	// SAVE USER -----------------------------------------------------------
	$('.ajax').submit(function (){
		var o = $(this),  
		arr = o.serializeArray(),
		data = renameArr(arr);
		if(!validate( o )){
			return false;
		}
		$.getJSON(get, data, function(json) {  
				showStatus(json);
			});
		return false;
	})
	
	
	// NEW USER -----------------------------------------------------------
	$('form[name=add]').submit(function (){
		var o = $(this),
		arr = o.serializeArray(),
		data = renameArr(arr);
		if(!validate( o )){
			return false;
		}
		$.getJSON(get, data, function(json) {  
				showStatus(json);
				if(json.err === 0){
				o.find('input[type=text]').val('');
				o.find('input[type=password]').val('')};
		});
		return false;
	})	 
	
	// HELP ---------------------------------------------

	$('#help div a').hover(function(e) {
		var url = $(this).attr('title');
		$('<img id="thumb" src="' + url + '" >').css('top', (e.pageY - 140)).css('left',(e.pageX + 40)).appendTo('body');
		}, function() {
		$('#thumb').remove();
	});
	
	$('#help div a').mousemove(function(e) {
			$("#thumb").css('top', (e.pageY - 140)).css('left',(e.pageX + 40));
	});
	
	
	
});

(function($) {
    $.fn.prettyFileInput = function(options) {
        var defaults = {
            ihc: 'file-input', bc: 'btn2', abc: 'btn-file-input',  bac: 'btn-file-input-active',ffhc: 'file-holder',dt: 'Vybrať foto', dfst: 'Zmeniť výber'
        };
        var options = $.extend(defaults, options);
        return this.each(function() {

            var obj = $(this);
            obj.wrap('<span class="' + options.ihc + '"></span>');
            obj.after('<span class="' + options.bc + ' ' + options.abc + '">' + options.dt + '</span>');
            obj.bind('change focus click', function() {
                $val = obj.val();
                valArray = $val.split('\\');
                newVal = valArray[valArray.length - 1];
                $button = obj.siblings('.' + options.bc + '');
                $fakeHolder = obj.siblings('.' + options.ffhc + '');
                if (newVal !== '') {
                    $button.addClass(options.bac).html(options.dfst);
                }
                if ($fakeHolder.length === 0) {
                    obj.parent().append('<span class="'+options.ffhc+'">' + newVal + '</span>');
                } else {
                    $fakeHolder.text(newVal);
                }
                if (($fakeHolder.length > 0) && (newVal === '')) {
                    $fakeHolder.remove();
                    $button.html(options.dt).removeClass().addClass(options.bc + ' ' + options.abc);
                }
            });
        });
    };
})(jQuery);
jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}

