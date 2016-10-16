$(function() {	
	tableDNDinit();
	// show new page form
	$('.newPage').click(function(){$(this).hide(200).parent().find('.box').show();return false;}); 
		
		// tree init
		$('#tree').treeview({animated: "fast",collapsed: true,unique: false,persist: "cookie"});
	
	// LANGs NOTATION -----------------------------------------------------------
	$('form .langs a').click(function(){
		var o = $(this),
			input = $('input[name=lang]'),
			lold = input.val(),
			data = {
				id : o.attr("href").replace("#aid",""),
				act : 8,
				lang : o.attr("title")
			};
		$.post("./inc/ajax.post.php", data, function(json) {  
				json = jQuery.parseJSON(json);
				var obj = $('*[name*="_"]'), i=0;
				for(i ;i < 4;i++){
					obj.eq(i).val(json[i]);
				}
				CKEDITOR.instances.editor1.setData(json[3]);
				input.val(data.lang);
				$('.langs a').removeClass("sel");
				o.addClass("sel");
			});
		
		return false;
	});
	
	
	//  KEYWORDS -----------------------------------------------------------
	$("#kwd").click(function (){
		var	o = $(this),
			data = {
			keywords : $('input[name=keywords]').val(),
			id : o.attr("href").replace("#aid", ""),
			act : 9
		};
		$.getJSON("./inc/ajax.get.php?cb=?", data, function(json) {  
			showStatus(json);
			return false;
		});	
	});// JavaScript Document
	
	// SAVE ARTICLE -----------------------------------------------------------
	$('form[name=article]').submit(function (){
		var arr = $(this).serializeArray(),
		data = { act : 7};
		for (i in arr) {
			data[arr[i].name] = (i == 5 ? CKEDITOR.instances.editor1.getData() : arr[i].value);
		}
		if(data['lang'] === "sk"){
			$('#tree').find(".curr").text(data['title_']);
			if(data.title_.length === 0){
				showStatus({err:1,msg:'Názov slovenskej verzie stránky musí byť vyplený.'});
				return false;
			}
		}
		$.post("./inc/ajax.post.php", data, function(json) {  
				//json = jQuery.parseJSON(json);
				showStatus(json);
			}, "json");
		return false;
	})	
	//  NEW ARTICLE -----------------------------------------------------------
		$("#new").click(function (){
			
			var	o = $(this),
				data = {
				title_sk : $('input[name=article]').val(),
				id : o.attr("href").replace("#aid", ""),
				act : 4
			};
			
			if(data.title_sk.length === 0){
				return false;
			}
			$.getJSON("./inc/ajax.get.php?cb=?", data, function(json) {  
				$('#dnd tbody').html(json.html);
				showStatus(json);
				if(json.err === 0){
					tableDNDinit();
					createClasses();
					$("#dnd tbody tr:last").css({ background: '#e3ffdc' });
					$(".navigator").first().remove();
					$("#new").parent().prepend(json.pagi);
					o.prev('input').val('').focus();
				}
				return false;
			});
			
		});
});