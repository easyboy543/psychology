$(function(){
	//<div class="camera_wrap"><div data-src="../images/DSCF.jpg"></div><div data-src="../images/149447.jpg"></div>
	var res;
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
	
	$("#lightbox").append('<a href="#popupParis" data-rel="popup" data-position-to="window" data-transition="fade"><img class="popphoto" src="../images/DSCF.jpg" alt="Paris, France" style="width:30%"></a>'+
					'<div data-role="popup" id="popupParis" data-overlay-theme="b" data-theme="b" data-corners="false">'+
    			'<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a><img class="popphoto" src="../images/DSCF.jpg" style="max-height:512px;" alt="Paris, France">'+'</div>');
	$("#lightbox").popup("refresh");
	$.post("/psychology/student/php/router/Router.php",{"action": "getTeacher"} ,function(response){
		if(response!="cannot link DB."){
			res = $.parseJSON(response);
			var str = "";
			for(var res_len=0;res_len<res.length;res_len++){
				$("#collapsibleFilter").append('<div data-role="collapsible" data-filtertext="'+res[res_len]["name"]+'"><h3>'+res[res_len]["name"]+'</h3>'+
					'<p>聯絡電話: '+res[res_len]["phone"]+'</p>'+
					'<p>聯絡信箱: '+res[res_len]["account"]+'</p>'
				+'</div>');
				$("#collapsibleFilter").collapsibleset("refresh");
				
				str = '<div class="img-lists" data-src="'+"../images/"+res[res_len]["img"]+'">'+
						'<div class="camera_caption fadeFromBottom">'+res[res_len]["name"]+"老師"+'</div>'
					+"</div>";	
				$("#gallery").append(str);
			}
			var det_os = navigator.userAgent;
			var cam_time = "9000";
			var cam_height = "500px";
			if(det_os.indexOf("Android")>-1 || det_os.indexOf("iPhone")>-1){
				cam_height = "100%";
			}
			
			$("#gallery").camera({
				time: cam_time,
				height: cam_height
			});
		}
		else{
			console.log("cannot link DB.");
		}
		
	});
	
	$("#login-btn").bind("click", function(){
		if($("#user-name").val()==""){
			alertify.alert("帳號為空!");
		}
		else if($("#user-pwd").val()==""){
			alertify.alert("密碼為空!");
		}
		else{
			var account = $("#user-name").val();
			var pwd = $("#user-pwd").val();
			$.post("/psychology/student/php/router/Router.php", {"action": "student", "data":[{"user-name": account, "user-pwd": pwd}]}, function(response){
				console.log(response);
			});
			
		}
	});
	
});