$(function(){
	alertify.set({
		labels:{
			ok: "確定",
			cancel: "取消"
		}
	});
	var res = "";
	
	$.post("/psychology/psycholo/php/router/Router.php", {"action": "comment_parent"}, function(response){
		console.log(response);
		res = $.parseJSON(response);
		if(res["result"]=="no-comment"){
			alertify.alert("沒有留言!", function(){
				location.href = "index.html";
			});
		}
		else if(res["result"]!="session-error"){
			var len = 0;
			for(;len<res.length;len++){
				$("#collapsibleFilter").append('<div data-role="collapsible" data-filtertext="'+res[len]["parent.name"]+'"><h3>'+res[len]["parent.name"]+'</h3>'+
					'<p>留言日期: '+res[res_len]["comment.msg_date"]+'</p>'+
					'<p>留言內容: '+res[res_len]["comment.contents"]+'</p>'
				+'</div>');
			}
		}
		else{
			console.log(res);
		}
		
	});
	
	$.post("/psychology/psycholo/php/router/Router.php", {"action": "check_login"}, function(response){
		console.log(response);
		res = $.parseJSON(response);
		if(res["result"]=="no_login"){
			$("#panel-menu").hide();
			$("#panel-login").show();
		}
		else{
			$("#panel-menu").show();
			$("#panel-login").hide();
		}
	});
	
});				
