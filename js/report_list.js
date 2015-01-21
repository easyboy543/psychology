$(function(){
	
	var res = null;
	
	alertify.set({
		labels:{
			ok: "確定",
			cancel: "取消"
		}
	});
	
	$.post("/psychology/student/php/router/Router.php", {"action": "check_login"}, function(response){
		console.log(response);
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
		$.post("/psychology/student/php/router/Router.php", {"action": "logon"}, function(response){
			if(response){
				location.reload();
				$(window).trigger("resize");
			}
			else{
				console.log(response);
			}
		});
	});
	
	$("#update-list").bind("click", function(){
		$("#report-list").html( "" );
		$.post("/psychology/student/php/router/Router.php", {"action": "report_list"}, function(response){
			res = $.parseJSON(response);
			var res_count = 0;
			var state = null;
			var finish_date = null;
			for(;res_count<res.length;res_count++){
				if(res[res_count]["state"]==0){
					state = "目前沒有受理";
					finish_date = "還沒受理...";
				}
				else{
					state = "正在受理中";
					if(res[res_count]["finish_date"]=="0000-00-00"){
						finish_date = "持續輔導中...";
					}
					else{
						finish_date = "輔導結束，結束日期: "+res[res_count]["finish_date"];
					}
				}
				$("#report-list").append('<div data-role="collapsible" data-filtertext="'+res[res_count]["notified_account"]+'"><h3>'+res[res_count]["notified_account"]+'</h3>'+
					'<p>通報原因: '+res[res_count]["reason"]+'</p>'+
					'<p>通報日期: '+res[res_count]["notify_date"]+'</p>'+
					'<p>受理情形: '+state+'</p>'+
					'<p>輔導狀況: '+finish_date+'</p>'
				+'</div>');
			}
		
			$("#report-list").collapsibleset( "refresh" );
		});
	})
	
	$.post("/psychology/student/php/router/Router.php", {"action": "report_list"}, function(response){
		res = $.parseJSON(response);
		var res_count = 0;
		var state = null;
		var finish_date = null;
		for(;res_count<res.length;res_count++){
			if(res[res_count]["state"]==0){
				state = "目前沒有受理";
				finish_date = "還沒受理...";
			}
			else{
				state = "正在受理中";
				if(res[res_count]["finish_date"]=="0000-00-00"){
					finish_date = "持續輔導中...";
				}
				else{
					finish_date = "輔導結束，結束日期: "+res[res_count]["finish_date"];
				}
			}
			$("#report-list").append('<div data-role="collapsible" data-filtertext="'+res[res_count]["notified_account"]+'"><h3>'+res[res_count]["notified_account"]+'</h3>'+
					'<p>通報原因: '+res[res_count]["reason"]+'</p>'+
					'<p>通報日期: '+res[res_count]["notify_date"]+'</p>'+
					'<p>受理情形: '+state+'</p>'+
					'<p>輔導狀況: '+finish_date+'</p>'
				+'</div>');
		}
		
		$("#report-list").collapsibleset( "refresh" );
	});
	
});
