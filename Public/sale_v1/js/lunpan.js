$().ready(function(){
	$(".Editor-container").addClass("col-md-9").css({padding:"0"});
	$("#reset-all").click(function(){
		$(".Editor-editor").text("");
	});
});
