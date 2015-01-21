$(function(){
	
	var res = null;
	
	alertify.set({
		labels:{
			ok: "確定",
			cancel: "取消"
		}
	});
	
	$.post("/psychology/psycholo/php/router/Router.php", {"action": "check_login"}, function(response){
		res = $.parseJSON(response);
		if(res["result"]=="no_login"){
			$("#post-report-page").hide();
			alertify.alert("未登入!", function(){
				location.href = "index.html";
			});
		}
		else{
			$("#post-report-page").show();
		}
	});
	
	$("#main-logon").click(function(){
		$.post("/psychology/psycholo/php/router/Router.php", {"action": "logon"}, function(response){
			if(response){
				location.reload();
				$(window).trigger("resize");
			}
			else{
				console.log(response);
			}
		});
	});
	
	$.post("/psychology/psycholo/php/router/Router.php", {"action": "report_list_available"}, function(response){
		console.log(response);
		res = $.parseJSON(response);
		var res_count = 0;
		var state = null;
		var finish_date = null;
		if(res["result"]=="no-available"){
			alertify.alert("沒有未受理學生！", function(response){
				location.href = "index.html";
			});
		}
		else{
			res = res["result"];
			for(;res_count<res.length;res_count++){
				$("#available-list").append('<div data-role="collapsible" data-filtertext="'+res[res_count]["notified_account"]+'"><h3>有問題學號： '+res[res_count]["notified_account"]+'</h3>'+
					'<p>通報帳號：'+res[res_count]["notify_account"]+'</p>'+
					'<p>通報原因: '+res[res_count]["reason"]+'</p>'+
					'<p>通報日期: '+res[res_count]["notify_date"]+'</p>'+
					'<p>結束日期: '+res[res_count]["finish_date"]+'</p>'+
					'<p>受理日期: '+res[res_count]["accept_date"]+'</p>'
					+'</div>');
			}
		
			$("#available-list").collapsibleset( "refresh" );
		}
	});
	
});
