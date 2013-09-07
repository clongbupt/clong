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
				 // var tishi = "���ύ������Ϊ��" + msg.type +
                 // "<br /> ���ύ������Ϊ��" + msg.recommendNum;
                 // $("#tishi").html(tishi);
                 // $("#tishi").css({color: "green"});
                if (msg.type == "success"){
					var temp = "#status-" + id;
					$(temp).html(msg.recommendNum);
				}	
					var temp = "#detail-" + id + " #button-" + id ;   //�ض���aԪ��
					$(temp).replaceWith("<span class='new_button'>���Ƽ�</span>");
					$(temp).attr("class", "new_button ajax");
					$(temp).html("���Ƽ�");
            }
		});	
	});
});