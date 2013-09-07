$(document).ready(function(){
	$('div.detail a.ajax').click(function(){
		var buttonId = $(this).attr("id");
		var id = buttonId.substring(7,9);
		var temp = "#detail-" + id + " input";
		
		var params = $(temp).serialize();
		
		var url = $('div#resultContent a#ajaxUrl').attr("href");
	
		jQuery.ajax({  
           type: "post",  
           url: url,  
		   dataType: "json",
           data: params,
           success: function(msg){
				 // var tishi = "您提交的姓名为：" + msg.type +
                 // "<br /> 您提交的密码为：" + msg.recommendNum;
                 // $("#tishi").html(tishi);
                 // $("#tishi").css({color: "green"});
                if (msg.type == "success"){
					var temp = "#status-" + id;
					$(temp).html(msg.recommendNum);
				}	
					var temp = "#detail-" + id + " #button-" + id ;   //特定的a元素
					$(temp).replaceWith("<span class='new_button'>已推荐</span>");
					$(temp).attr("class", "new_button ajax");
					$(temp).html("已推荐");
            }
		});	
	});
});