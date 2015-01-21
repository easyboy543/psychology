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
	
	$.post("/psychology/student/php/router/Router.php", {"action": "check_login"}, function(response){
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
	
	$.post("/psychology/student/php/router/Router.php",{"action": "getTeacher"} ,function(response){
		//console.log(response);
		if(response!="cannot link DB."){
			res = $.parseJSON(response);
			var str = "";
			for(var res_len=0;res_len<res.length;res_len++){
				$("#collapsibleFilter").append('<div data-role="collapsible" data-filtertext="'+res[res_len]["name"]+'"><h3>'+res[res_len]["name"]+'</h3>'+
					'<p>聯絡電話: '+res[res_len]["phone"]+'</p>'+
					'<p>聯絡信箱: '+res[res_len]["account"]+'</p>'
				+'</div>');
				
				str = '<div class="img-lists" data-src="'+"../images/"+res[res_len]["img"]+'">'+
						'<div class="camera_caption fadeFromBottom">'+res[res_len]["name"]+"老師"+'</div>'
					+"</div>";	
				$("#gallery").append(str);
				
				arr_gallery[res_len] = {};
				arr_gallery[res_len].href = "../images/"+res[res_len]["img"];
				arr_gallery[res_len].title = res[res_len]["name"]+"老師";
			}
			
			$("#collapsibleFilter").collapsibleset("refresh");
			var det_os = navigator.userAgent;
			var cam_time = "9000";
			var cam_height = "500px";
			if(det_os.indexOf("Android")>-1 || det_os.indexOf("iPhone")>-1){
				cam_height = "100%";
				$("#gallery").show();
				$("#gallery").camera({
					time: cam_time,
					height: cam_height
				});
			}
			else{
				$("#gallery").remove();
			}
			
		}
		else{
			console.log("cannot link DB.");
		}
		
	});
	
	$('#lightbox').click(function(e){
		e.preventDefault();
		$.swipebox(arr_gallery);
	});
	
	
	$("#login-btn").bind("click", login_handle);
	
	$("#user-acc,#user-pwd").keypress(function(e){
		if(e.which==13){
			login_handle();
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
	
	/*$("#fb-login").click(function(){
		$.post("/psychology/student/php/router/Router.php", {"action": "facebook_login"}, function(response){
			console.log(response);
			res = $.parseJSON(response);
			if(res["result"]=="is-fb-login"){
				location.reload();
			}
			else{
				location.target = "_blank";
				location.href = res["result"];
			}
		});
	});*/
	
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
		
			$.post("/psychology/student/php/router/Router.php", {"action": "parent_login", "data":[{"user-acc": account, "user-pwd": pwd, "captcha": recaptcha}]}, function(response){
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
