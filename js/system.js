// eService-HK
// System script
function alertm(message, type){
	$('file').show('slow');
	var alertm_element = document.getElementById('alertm');
	var alertm_message = document.getElementById('alertm_message');
	switch(type){
		case 'warning':
			alertm_message.innerHTML = message;
			alertm_element.style.display = 'block';
			alertm_element.className = 'warning';
			break;
		case 'error':
			alertm_message.innerHTML = message;
			alertm_element.style.display = 'block';
			alertm_element.className = 'error';
			break;
		case 'close':
			alertm_message.innerHTML = null;
			alertm_element.style.display = 'none';
			alertm_element.className = null;
			break;
		default:
			return false;
			break;
	}
	return false;
}

//Valid data
function validdata() {
	if (document.getElementById('file').value == "") {
		alertm("請先選擇一個圖片檔案上傳 !", 'warning');
		return false;
	} else {
		document.getElementById('file').style.visibility = 'hidden';
		document.getElementById('upload_button').style.visibility = 'hidden';
		/*document.getElementById('uploading_div').style.visibility = 'visible';*/

		$('#uploading_div').css('visibility', 'visible');
		return true;
	}
}

function checktype(value){
	var length = value.length;
	var type = value.substr(length-4, 4);
	//Add the support filetype
	if(type == '.gif' || type == '.GIF' || type == '.jpg' || type == '.JPG' || type == 'jpeg' || type == 'JPEG' || type == '.png' || type == '.PNG') return true;
	alertm('檔案格式不支援 !', 'error');
	document.getElementById('re_set').click();
	return false;
}

function langchooser(){
var langdrop = document.getElementById('langdrop');

	if(langdrop.style.display == 'block'){
		langdrop.style.display = 'none';
	}else{
		langdrop.style.display = 'block';
	}	
}

function validsize() {
	var newsize = document.getElementById('newsize');
	if (newsize.value < 1 || newsize.value > 1920) {
		alert("縮圖大小應大於 1 及少於 1920 !");
		return false;
	} else {
		return true;
	}
}

function CopyText(text) {
	copy(text);
}

function copy(meintext) {
	if (window.clipboardData)  {
		window.clipboardData.setData("Text", meintext);
	} else if (window.netscape) {
		netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip) return;
		// maak een transferable
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans) return;
		trans.addDataFlavor('text/unicode');
		var str = new Object();
		var len = new Object();
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		var copytext=meintext;
		str.data=copytext;
		trans.setTransferData("text/unicode",str,copytext.length*2);
		var clipid=Components.interfaces.nsIClipboard;
		if (!clip) return false;
		clip.setData(trans,null,clipid.kGlobalClipboard);
	}
	alert("複製完成");
	return true;
}