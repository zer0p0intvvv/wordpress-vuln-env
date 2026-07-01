<div id="wpns_message" style=" padding-top:8px"></div>
<div class="mo_wpns_tab">
	<button class="tablinks" onclick="openTab(event, 'security_login')" id="login_sec">Login Security</button>
    <button class="tablinks" onclick="openTab(event, 'security_registration')" id="reg_sec">Registration Security</button>
    <button class="tablinks" onclick="openTab(event, 'content_spam')" id="spam_content">Content & Spam</button>
</div>
<br>
<div class="tabcontent" id="security_login">
	<div class="mo_wpns_divided_layout">
		<table style="width:100%;">
			<tr>
				<td>
					<?php include_once $mo_lla_dirName . 'controllers'.DIRECTORY_SEPARATOR.'login-security.php'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="tabcontent" id="security_registration">
	<div class="mo_wpns_divided_layout">
		<table style="width:100%;">
			<tr>
				<td>
					<?php include_once $mo_lla_dirName . 'controllers'.DIRECTORY_SEPARATOR.'registration-security.php'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="tabcontent" id="content_spam">
	<div class="mo_wpns_divided_layout">
		<table style="width:100%;">
			<tr>
				<td>
					<?php include_once $mo_lla_dirName . 'controllers'.DIRECTORY_SEPARATOR.'content-protection.php'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<script>
	document.getElementById("security_login").style.display = "block";
	document.getElementById("security_registration").style.display = "none";
	document.getElementById("content_spam").style.display = "none";
	document.getElementById("login_sec").className += " active";
	function openTab(evt, tabname){
		var i, tablinks, tabcontent;
		tabcontent = document.getElementsByClassName("tabcontent");
  			for (i = 0; i < tabcontent.length; i++) {
    		tabcontent[i].style.display = "none";
  		}
		tablinks = document.getElementsByClassName("tablinks");
		for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		}
		document.getElementById(tabname).style.display = "block";
		localStorage.setItem("tablast", tabname);
  		evt.currentTarget.className += " active";
	}
	var tab = localStorage.getItem("tablast");
	if(tab == "security_login"){
		document.getElementById("login_sec").click();
	}
	else if(tab == "security_registration"){
		document.getElementById("reg_sec").click();
	}
	else if(tab == "content_spam"){
		document.getElementById("spam_content").click();
	}
	else{
		document.getElementById("login_sec").click();
	}
</script>