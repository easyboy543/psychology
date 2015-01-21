$(function(){
	//<div class="camera_wrap"><div data-src="../images/DSCF.jpg"></div><div data-src="../images/149447.jpg"></div>
	var res;
	var arr_gallery = [{}];
	var url = location.hash;
	if(url=="#_=_"){
		location.href = "index.html";
	}
	
	alertify.set({
		labels:{
			ok: "確定",
			cancel: "取消"
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
	
	$("#login-btn").bind("click", login_handle);
	
	$("#user-acc,#user-pwd").keypress(function(e){
		if(e.which==13){
			login_handle();
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
	
	function login_handle(){
		if($("#user-acc").val()==""){
			alertify.alert("帳號為空!");
		}
		else if($("#user-pwd").val()==""){
			alertify.alert("密碼為空!");
		}
		else if($("#g-recaptcha-response").val()==""){
			alertify.alert("抱歉,沒通過驗證喔!");
		}
		else{
			var account = $("#user-acc").val();
			var pwd = $("#user-pwd").val();
			var recaptcha = $("#g-recaptcha-response").val();
		
			$.post("/psychology/psycholo/php/router/Router.php", {"action": "psycholo_login", "data":[{"user-acc": account, "user-pwd": pwd, "captcha": recaptcha}]}, function(response){
				console.log(response);
				var res = $.parseJSON(response);
				if(res["result"]=="verify-fail"){
					alertify.alert("登入失敗!");
				}
				else if(res["result"]=="not-active"){
					alertify.alert("此帳號已經停權了,請聯絡管理員!");
				}
				else if(res["result"]=="verify-success"){
					location.reload();
				}
				else{
					console.log(response);
				}
			});
			
		}
	}
	
});
