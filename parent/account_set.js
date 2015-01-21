$(function(){
	
	var res = "";
	
	alertify.set({
		labels:{
			ok: "確定",
			cancel: "取消"
		}
	});
	
	$.post("/psychology/psycholo/php/router/Router.php", {"action": "parent_check_login"}, function(response){
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
		$.post("/psychology/psycholo/php/router/Router.php", {"action": "parent_logon"}, function(response){
			if(response){
				location.reload();
				$(window).trigger("resize");
			}
			else{
				console.log(response);
			}
		});
	});
	
	$("#save-change").click(function(response){
		if($("#update-pwd").val()==$("#update-pwd2").val()){
			if($("#update-pwd").val().length<8){
				alertify.alert("密碼長度為8以上!");
			}
			else{
				$.post("/psychology/psycholo/php/router/Router.php", {"action": "parent_update",
					"data": [{"update-pwd": $("#update-pwd").val(), "update-pwd2": $("#update-pwd2").val()}]}, 
					function(response){
						console.log(response);
						res = $.parseJSON(response);
						if(res["result"]=="update-pwd-success"){
							alertify.alert("修改密碼成功!");
						}
						else if(res["result"]=="pwd-short-len"){
							alertify.alert("密碼長度為8以上!");
						}
						else if(res["result"]=="pwd-diff"){
							alertify.alert("密碼輸入不一致!");
						}
						else if(res["result"]=="pwd-not-change"){
							alertify.alert("密碼沒有變更!");
						}
						else{
							console.log(response);
						}
					});
			}
		}
		else{
			alertify.alert("密碼輸入不一致!");
		}
	});
	
});
