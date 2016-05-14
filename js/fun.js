// JavaScript Document
function act_confirm(msg,url) {
if (confirm(msg)) {
	window.location = url;
	}
else{
	return false;
	}
}

function act_confirmn(msg,url) {
if (confirm(msg)) {
	//window.location = url;
	window.open(url,"_blank");
	}
else{
	return false;
	}
}

function fidAll()
{
	var items=document.getElementsByName("num[]");
	var chk=document.getElementById("fida");
	if(items)
	{
		for(i=0;i<items.length;i++)
		{
			items[i].checked=chk.checked;
		}
	}
}