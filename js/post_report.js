$(function(){

	alertify.set({
		labels:{
			ok: "確定",
			cancel: "取消"
		}
	});
	
	$.post("/psychology/student/php/router/Router.php", {"action": "check_login"}, function(response){
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
	
	/*
	$.getJSON("http://peterwebsite.lionfree.net/psychology/json/student_name.json", function(response){
		//console.log(response);
		
		for(var count=0;count<response.length;count++){
			$("#report-name-list").append('<li><a href="'+"javascript:"+"getStuNum("+response[count]["account"]+")"+'" class="namestudent">'+response[count]["account"]+"</a></li>");
		}
		$("#report-name-list").listview("refresh");
	
		/*for(var name_len=0;name_len<response.length;name_len++){
			$("li").click(function(){
				alertify.alert("click");
			});
		}
	
	});*/
	
	$("#report-name-list").on("filterablebeforefilter", function(e, data){
		var $ul = $( this ),
            $input = $( data.input ),
            $value = $input.val(),
            html = "";
        $ul.html( "" );
        if ( $value && $value.length > 2 ) {
            $.post("/psychology/student/php/router/Router.php",{ "data": $input.val(),"action": "filter_student_number"} , function(response){
				res = $.parseJSON(response);
				for(var count=0;count<res.length;count++){
					$ul.append('<li><a href="'+"javascript:"+"getStuNum("+res[count]["account"]+")"+'" class="namestudent">'+res[count]["account"]+"</a></li>");
				}
				$ul.listview( "refresh" );
			}).fail(function(){
				alertify.alert("不好意思，出錯了，再試著搜尋一次吧!");
			});
        }
	});
	
	$("#report-button").click(report_handle);
	$("#cancel-button").click(reset_form);
	
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
	
	function report_handle(){
		if($("#report_name").val()==""){
			alertify.alert("申報學號呢?");
		}
		else if($("#select-event").val()=="請選擇呈報原因"){
			alertify.alert("原因沒有勾選!");
		}
		else{
			$.post("/psychology/student/php/router/Router.php", {"action": "report_handle", "data": [{"report_name": $("#report_name").val(), "select_event": $("#select-event").val()}]}, function(response){
				res = $.parseJSON(response);
				if(res["result"]=="report_success"){
					alertify.alert("通報成功!");
				}
				else if(res["result"]=="not-finish"){
					alertify.alert("已經有人通報,正在處理中!");
				}
				else if(res["result"]=="lie_times_limit"){
					alertify.alert("謊報已經到達上限,請等解除謊報,謝謝");
				}
				else{
					console.log(res);
				}
			});
		}
	}
	
	function reset_form(){
		$("#select-event").prop("selectedIndex", 0);
		$("#select-event").selectmenu('refresh');
		$("#report_name").val("");
	}
	
});

function getStuNum(stu_num){
	$("#report_name").val(stu_num);
}
	