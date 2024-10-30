function _miniifbox_submit() {
	if(document.box_form.box_title.value == "") {
		alert(miniifbox_adminscripts.box_title);
		document.box_form.box_title.focus();
		return false;
	}
	else if(document.box_form.box_srcdoc.value == "" && document.box_form.box_srclink.value == "") {
		alert(miniifbox_adminscripts.box_text);
		document.box_form.box_srclink.focus();
		return false;
	}
	else if(document.box_form.box_group.value == "" && document.box_form.box_group_txt.value == "") {
		alert(miniifbox_adminscripts.box_group);
		document.box_form.box_group_txt.focus();
		return false;
	}
	else if(document.box_form.box_width.value == "" || isNaN(document.box_form.box_width.value)) {
		alert(miniifbox_adminscripts.box_width);
		document.box_form.box_width.focus();
		document.box_form.box_width.select();
		return false;
	}
	else if(document.box_form.box_height.value == "" || isNaN(document.box_form.box_height.value)) {
		alert(miniifbox_adminscripts.box_height);
		document.box_form.box_height.focus();
		document.box_form.box_height.select();
		return false;
	}
	else if(document.box_form.box_start.value == "") {
		alert(miniifbox_adminscripts.box_start);
		document.box_form.box_start.focus();
		return false;
	}
	else if(document.box_form.box_end.value == "") {
		alert(miniifbox_adminscripts.box_end);
		document.box_form.box_end.focus();
		return false;
	}
}

function _miniifbox_delete(id) {
	if(confirm(miniifbox_adminscripts.box_delete)) {
		document.frm_box_display.action="options-general.php?page=mini-iframe-box&ac=del&did="+id;
		document.frm_box_display.submit();
	}
}	

function _miniifbox_redirect() {
	window.location = "options-general.php?page=mini-iframe-box";
}

function _miniifbox_help() {
	window.open("http://www.gopiplus.com/work/2020/04/12/mini-iframe-box-wordpress-plugin/");
}

function _miniifbox_numericandtext(inputtxt) {  
	var numbers = /^[0-9a-zA-Z]+$/;  
	document.getElementById('box_group').value = "";
	if(inputtxt.value.match(numbers)) {  
		return true;  
	}  
	else {  
		alert(miniifbox_adminscripts.box_numletters); 
		newinputtxt = inputtxt.value.substring(0, inputtxt.value.length - 1);
		document.getElementById('box_group_txt').value = newinputtxt;
		return false;  
	}  
}