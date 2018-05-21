function getXmlHttpObject(){
	var xmlHttpRequest;
	if(window.ActiveXObject){
		xmlHttpRequest=new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		xmlHttpRequest=new XMLHttpRequest();
	}
	return xmlHttpRequest;
}

function $(id){
	return document.getElementById(id);
}

var myXmlHttpRequest="";
function checkCode(){
	myXmlHttpRequest=getXmlHttpObject();
	if(myXmlHttpRequest){
		var agentCode = $("#agent_code");
		var url="./control/agent.php?mytime="+new Date()+"&action=checkAgentCode&agentCode="+agentCode.val();
		myXmlHttpRequest.open("get",url,true);
		myXmlHttpRequest.onreadystatechange=deal;
		myXmlHttpRequest.send(null); 
	}
}
function deal(){
	if(myXmlHttpRequest.readyState==4 && myXmlHttpRequest.status==200){
		$("#agentcodeTip").html(myXmlHttpRequest.responseText);
	}
}