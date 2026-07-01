<?php
add_action('admin_footer','mo_wpns_start_scan');

?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<div class="mo_wpns_setting_layout" id="scan_status_table">
		<div>
			<div>
				<div style="float: left;">
					<p id="scanstatus"></p>
					<h2>Malware Scan</h2>
				</div>
				
				<div class="malwaresummarydiv">
					<div class="mo_wpns_malwarescandiv msdivl">
						<div class="hdiv"><b>Scan Now</b></div>
						<p>Kindly choose the Scan Mode according to your needs.</p>
						<p>For Custom Scan, you can configure the settings in Custom Scan Settings Tab.</p>
					</div>

					<div id="summary_scan" class="mo_wpns_malwarescandiv msdivr">
						<div class="hdiv shdiv"><b>Scan Summary</b></div>
					<?php show_summary(); ?>		
					</div>
				</div>
			</div>
		<?php
		if(! isset($_GET['view'])){
		?>
			<div>
				<p class="hdiv">Scan Modes</p>
			</div>
			<div class="malwaresummarydiv">
				<div class="mo_wpns_sub_scanmode mo_wpns_msdivl">
					<div class="hdiv"><b>Quick Scan</b></div>
					<p class="mo_wpns_scan_desc">Quick Scan checks all Plugins, Themes and Core files for Vulnerable Code and SQL Injections using PHP malware signatures.</p>
					<input id="quick_scan_button" type="button" name="quick_scan_button" class="mo_wpns_scan_button" value="Quick Scan">
				</div>
				<div class="mo_wpns_sub_scanmode mo_wpns_msdivr mo_wpns_msdivl">
					<div class="hdiv"><b>Standard Scan</b></div>
					<p class="mo_wpns_scan_desc">Standard Scan checks all Plugins, Themes and Core files for external links and compares with the repository as well.</p>
					<input id="standard_scan_button" type="button" name="standard_scan_button" class="mo_wpns_scan_button" value="Standard Scan">
				</div>
				<div class="mo_wpns_sub_scanmode mo_wpns_msdivl mo_wpns_msdivr">
					<div class="hdiv">
						<b>Deep Scan</b>
						<strong><a href="admin.php?page=upgrade"> <b style="color: red;">[Premium]</b> </a></strong>
					</div>
					<p class="mo_wpns_scan_desc">Deep Scan checks all Plugins, Themes and Core files for RFI, Trojans and Backdoors using advanced signatures and detects blacklisted domains as well.</p>
					<input id="deep_scan_button" type="button" name="deep_scan_button" class="mo_wpns_deep_scan_button" value="Deep Scan">
				</div>
				<div class="mo_wpns_sub_scanmode mo_wpns_msdivr">
					<div class="hdiv"><b>Custom Scan</b></div>
					<p class="mo_wpns_scan_desc">Custom Scan gives you an option to choose which files to scan and what to check for.</p>
					<input id="custom_scan_button" type="button" name="custom_scan_button" class="mo_wpns_scan_button" value="Custom Scan">
				</div>
			</div>
		<?php
		}
		?>
			
		</div>
	</div>
	<div class="mo_wpns_setting_layout" id="scan_report_table">
	<?php if(! isset($_GET['view'])){ ?>	
		<h2>Malware Scan Report</h2>
	<?php }else{ ?>
		<h2>Detail Report Of Scan
			<a href="<?php echo $currenturl ?>"><b style="float: right; padding-right: 4%">Back To Scan</b></a>
		</h2>
	<?php } ?>
		<hr>
		<div id="scandata">
			<?php 
				include_once $mo_lla_dirName. 'controllers'.DIRECTORY_SEPARATOR.'malware_scan_result.php';
				echo showScanResults();
			?>
		</div>
	</div>
<?php
function mo_wpns_start_scan(){
	if ( ('admin.php' != basename( $_SERVER['PHP_SELF'] )) || ($_GET['page'] != 'malwarescan') ) {
        return;
    }
?>
<script>
	jQuery(document).ready(function(){
	jQuery('input[name="quick_scan_button"]').click(function(){
		var elem = document.getElementById("quick_scan_button");
		if (elem.value=="Quick Scan")
			elem.value = "Scanning";
		else 
			elem.value = "Quick Scan";
		jQuery("#scanstatus").removeClass();
		jQuery("#scanstatus").addClass("alert alert-warning");
		jQuery("#scanstatus").html("Malware scan is <strong>in progress.</strong> You can see result in reports after it's done.");

		jQuery('input[name="quick_scan_button"]').attr('disabled', true);
		jQuery('input[name="custom_scan_button"]').attr('disabled', true);
		jQuery('input[name="standard_scan_button"]').attr('disabled', true);
		document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
		document.getElementById('custom_scan_button').style.backgroundColor = '#b0d2cf';
		document.getElementById('standard_scan_button').style.backgroundColor = '#b0d2cf';

		var data={
			'action':'mo_wpns_malware_redirect',
			'call_type':'malware_scan_initiate',
			'scan':'scan_start',
			'scantype':'quick_scan'
		};
		jQuery.post(ajaxurl, data, function(response){
			var xmlString = response;
	   		doc = new DOMParser().parseFromString(xmlString, "text/html");
		   	var all_scan_summary=doc.getElementById('summary_all');
		   	var current_scan_summary=doc.getElementById('summary_current');
		   	jQuery('#summary_all').html(all_scan_summary);
		   	jQuery('#summary_current').html(current_scan_summary);
		   	var summary_html= doc.getElementById('summary_all');
		   	summary_html.remove();
		   	var current_summary= doc.getElementById('summary_current');
		   	current_summary.remove();
		   	var status_table= doc.getElementById('scan_status_table');
		   	status_table.remove();
		   	var report_scan= doc.getElementById('scan_report_table');
		   	report_scan.remove();
		   	var s = new XMLSerializer();
		   	var d= doc;
		   	var str=s.serializeToString(d); 
			jQuery('#scandata').html(str);

			jQuery('input[name="quick_scan_button"]').removeAttr('disabled');
			document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="standard_scan_button"]').removeAttr('disabled');
			document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="custom_scan_button"]').removeAttr('disabled');
			document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';

			if (elem.value=="Quick Scan") 
				elem.value = "Scanning";
			else 
				elem.value = "Quick Scan";
			
			jQuery("#scanstatus").removeClass();
			jQuery("#scanstatus").addClass("alert alert-success");
			jQuery("#scanstatus").html("Malware scan is <strong>completed.</strong> You can see result in reports below.");
		});
	});

	jQuery('input[name="standard_scan_button"]').click(function(){
		var elem = document.getElementById("standard_scan_button");
		if (elem.value=="Standard Scan")
			elem.value = "Scanning";
		else 
			elem.value = "Standard Scan";
		jQuery("#scanstatus").removeClass();
		jQuery("#scanstatus").addClass("alert alert-warning");
		jQuery("#scanstatus").html("Malware scan is <strong>in progress.</strong> You can see result in reports after it's done.");
		
		jQuery('input[name="quick_scan_button"]').attr('disabled', true);
		jQuery('input[name="custom_scan_button"]').attr('disabled', true);
		jQuery('input[name="standard_scan_button"]').attr('disabled', true);
		document.getElementById('quick_scan_button').style.backgroundColor = '#b0d2cf';
		document.getElementById('custom_scan_button').style.backgroundColor = '#b0d2cf';
		document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';

		var data={
			'action':'mo_wpns_malware_redirect',
			'call_type':'malware_scan_initiate',
			'scan':'scan_start',
			'scantype':'standard_scan'
		};
		jQuery.post(ajaxurl, data, function(response){
			var xmlString = response;
	   		doc = new DOMParser().parseFromString(xmlString, "text/html");
		   	var all_scan_summary=doc.getElementById('summary_all');
		   	var current_scan_summary=doc.getElementById('summary_current');
		   	jQuery('#summary_all').html(all_scan_summary);
		   	jQuery('#summary_current').html(current_scan_summary);
		   	var summary_html= doc.getElementById('summary_all');
		   	summary_html.remove();
		   	var current_summary= doc.getElementById('summary_current');
		   	current_summary.remove();
		   	var status_table= doc.getElementById('scan_status_table');
		   	status_table.remove();
		   	var report_scan= doc.getElementById('scan_report_table');
		   	report_scan.remove();
		   	var s = new XMLSerializer();
		   	var d= doc;
		   	var str=s.serializeToString(d); 
			jQuery('#scandata').html(str);

			jQuery('input[name="quick_scan_button"]').removeAttr('disabled');
			document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="standard_scan_button"]').removeAttr('disabled');
			document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="custom_scan_button"]').removeAttr('disabled');
			document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';

			if (elem.value=="Standard Scan") 
				elem.value = "Scanning";
			else 
				elem.value = "Standard Scan";
			
			jQuery("#scanstatus").removeClass();
			jQuery("#scanstatus").addClass("alert alert-success");
			jQuery("#scanstatus").html("Malware scan is <strong>completed.</strong> You can see result in reports below.");
		});
	});

	jQuery('input[name="custom_scan_button"]').click(function(){
		var elem = document.getElementById("custom_scan_button");
		if (elem.value=="Custom Scan")
			elem.value = "Scanning";
		else 
			elem.value = "Custom Scan";
		jQuery("#scanstatus").removeClass();
		jQuery("#scanstatus").addClass("alert alert-warning");
		jQuery("#scanstatus").html("Malware scan is <strong>in progress.</strong> You can see result in reports after it's done.");
		
		jQuery('input[name="quick_scan_button"]').attr('disabled', true);
		jQuery('input[name="custom_scan_button"]').attr('disabled', true);
		jQuery('input[name="standard_scan_button"]').attr('disabled', true);
		document.getElementById('quick_scan_button').style.backgroundColor = '#b0d2cf';
		document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';
		document.getElementById('standard_scan_button').style.backgroundColor = '#b0d2cf';

		var data={
			'action':'mo_wpns_malware_redirect',
			'call_type':'malware_scan_initiate',
			'scan':'scan_start',
			'scantype':'custom_scan'
		};
		jQuery.post(ajaxurl, data, function(response){
			var xmlString = response;
	   		doc = new DOMParser().parseFromString(xmlString, "text/html");
		   	var all_scan_summary=doc.getElementById('summary_all');
		   	var current_scan_summary=doc.getElementById('summary_current');
		   	jQuery('#summary_all').html(all_scan_summary);
		   	jQuery('#summary_current').html(current_scan_summary);
		   	var summary_html= doc.getElementById('summary_all');
		   	summary_html.remove();
		   	var current_summary= doc.getElementById('summary_current');
		   	current_summary.remove();
		   	var status_table= doc.getElementById('scan_status_table');
		   	status_table.remove();
		   	var report_scan= doc.getElementById('scan_report_table');
		   	report_scan.remove();
		   	var s = new XMLSerializer();
		   	var d= doc;
		   	var str=s.serializeToString(d); 
			jQuery('#scandata').html(str);

			jQuery('input[name="quick_scan_button"]').removeAttr('disabled');
			document.getElementById('quick_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="standard_scan_button"]').removeAttr('disabled');
			document.getElementById('standard_scan_button').style.backgroundColor = '#20b2aa';
			jQuery('input[name="custom_scan_button"]').removeAttr('disabled');
			document.getElementById('custom_scan_button').style.backgroundColor = '#20b2aa';

			if (elem.value=="Custom Scan") 
				elem.value = "Scanning";
			else 
				elem.value = "Custom Scan";
			
			jQuery("#scanstatus").removeClass();
			jQuery("#scanstatus").addClass("alert alert-success");
			jQuery("#scanstatus").html("Malware scan is <strong>completed.</strong> You can see result in reports below.");
		});
	});	

});
</script>
<?php
}
function show_summary(){
	$mo_wpns_db_handler = new Mo_lla_MoWpnsDB();
	$last_id=$mo_wpns_db_handler->get_last_id();
	$send_id=$last_id[0]->max;
	if(is_null($send_id)){
		$total_scan=0;
		$total_malicious=0;
		$last_scan=0;
		$malicious_last_scan=0;
	}else{
		$result = $mo_wpns_db_handler->get_report_with_id($send_id);
		$total_scan=$mo_wpns_db_handler->count_files();
		$total_malicious=$mo_wpns_db_handler->count_malicious_files();
		$last_scan=$mo_wpns_db_handler->count_files_last_scan($send_id);
		$malicious_last_scan=$mo_wpns_db_handler->count_malicious_last_scan($send_id);
	}
?>
	<div id="summary_all" class="malwaresummarydiv"><div class="summarydiv">Total Files scanned: <?php echo $total_scan; ?></div>
	<div class="summarydiv ">Total Infected Files Found: <?php echo $total_malicious; ?></div></div>
	<div id="summary_current" class="malwaresummarydiv"><div class="summarydiv">Files Scanned in last scan: <?php echo $last_scan; ?> </div>
	<div class="summarydiv">Infected Files in last scan: <?php echo $malicious_last_scan; ?> </div></div>
	
<?php
}

function show_scan_details($detailreport, $result, $ignorefiles){
	$record = $result[0];
	echo "<b>Malicious files found: </b>" .count($detailreport);
?>
	<div style=float:right><b>Scan Time :</b> <?php echo date("M j, Y, g:i:s a",$record->start_timestamp); ?><br><b>Completion Time :</b> <?php echo date("M j, Y, g:i:s a",$record->completed_timestamp); ?></div><br><br><hr><br>
	<table id="reports_table" class="display" cellspacing="0" width="100%">
	<thead><tr><th>Malicious Files</th><th>Issues</th><th>Action</th></tr></thead>
	<tbody> 
<?php
	foreach($detailreport as $report){
		$issues = unserialize($report->report);
		$filename = $report->filename;
		$classdiv = "";
		$issuecolor = "mo_wpns_red";
		$status = "<a href='".add_query_arg( array('trust' => base64_encode($report->filename)), $_SERVER['REQUEST_URI'] )."'>I trust this file</a>";
		if(in_array($report->filename,array_keys($ignorefiles))){
			if($ignorefiles[$filename]['signature']==md5_file($report->filename)){
				$classdiv = "mo_wpns_gray";
				$issuecolor = "mo_wpns_brightred";
				$status = "<span class=mo_wpns_lightgreen>trusted</span>";
			}else{
				$classdiv = "mo_wpns_gray";
				$issuecolor = "mo_wpns_brightred";
				$status = "<a href='".add_query_arg( array('trustchanged' => $ignorefiles[$filename]['id']), $_SERVER['REQUEST_URI'] )."'>I trust this file</a><br><span class=mo_wpns_brightred><center>( changed )</center></span>";
			}
		} 
		echo "<tr><td class=".$classdiv.">".$report->filename."</td><td>";
		foreach($issues as $key=>$value){
			if($key=='Shell Script'){
				echo "<div><span class='".$issuecolor." issue'><b>".$key."</b></span></div>";
				foreach ($value as $issue) {
					echo "<div class='issuecontent' data-line='".$issue["l"]."' data-issue='".$issue["d"]."'>Match Found: ".$issue["d"]." Line: ".$issue["l"]."</div>";
				}
			} 
			if($key=='Vulnerable Code'){
				echo "<div><span class='".$issuecolor." issue'><b>".$key."</b></span></div>";
				foreach ($value as $issue) {
					echo "<div class='issuecontent' data-line='".$issue["l"]."' data-issue='".$issue["d"]."'>Match Found: ".$issue["d"]." Line: ".$issue["l"]."</div>";
				}
			} 
			if($key=='SQL Injection'){
				echo "<div><span class='".$issuecolor." issue'><b>".$key."</b></span></div>";
				foreach ($value as $issue) {
					echo "<div class='issuecontent' data-line='".$issue["l"]."' data-issue='".$issue["d"]."'>Match Found: ".$issue["d"]." Line: ".$issue["l"]."</div>";
				}
			} 
			if($key=='repo'){
				echo "<div><span class='".$issuecolor." issue'><b>Check File with Repo: </b></span><div><div class='issuecontent'>File Status: ".$value["exist"]."</div>";
			} 
			if($key=='extl'){
				echo "<div><span class='".$issuecolor." issue'><b>External Link:</b></span></div>";
				foreach ($value as $issue) {
					echo "<div class='issuecontent' data-line='".$issue["l"]."' data-issue='".$issue["d"]."'>Link: ".$issue["d"]." Line: ".$issue["l"]."</div>";
				}
			} 	
		}
		echo "</td><td>".$status."</td></tr>";
	}
?>
	</tbody>
	</table>
	<div id="myModal" class="modal">
	  <div class="modal-content">
		<span class="mo_wpns_scan_close_issue">×</span>
		<div>
			<b>Issue on line number : <span id="modalline"></span></b>
			<pre id="modalcontent"></pre>
		</div>
	  </div>
	</div>
	<script>
		jQuery(".issue").click(function(){
			var issuehtml = jQuery(this).parent().find(".issuecontent").html();
			var modal = document.getElementById('myModal');
			var span = document.getElementsByClassName("mo_wpns_scan_close_issue")[0];
			var token = jQuery(this).parent().find(".issuecontent").data("token");
			var issue = jQuery(this).parent().find(".issuecontent").data("issue");
			issuehtml = "<span class=red style=white-space:initial >"+token +" "+issuehtml+"</span>";
			jQuery("#modalline").html(jQuery(this).parent().find(".issuecontent").data("line"));
			jQuery("#modalcontent").html(issuehtml);
			modal.style.display = "block";
			span.onclick = function() {
				modal.style.display = "none";
			}			
			window.onclick = function(event) {
				if (event.target == modal) {
					modal.style.display = "none";
				}
			}
		});
	</script>
<?php
}

function show_scan_report($currenturl, $result){
	$mo_wpns_db_handler = new Mo_lla_MoWpnsDB();
?>
<table id="reports_table" class="display" cellspacing="0" width="100%">
<thead><tr><th>Scan Type</th><th>Scanned Folders</th><th>Status</th><th>Scan Time</th><th>Action</th></tr></thead>
<tbody>
	<?php 
	if(! is_null($result)){
		foreach($result as $report){
			$vresult = $mo_wpns_db_handler->get_vulnerable_files_count_for_reportid($report->id);
			if(count($vresult)>0)
				$vulnerablefies = $vresult[0]->count;
			else
				$vulnerablefies = 0;

			echo "<tr><td style=text-align:center>".$report->scan_mode."</td>";
			echo "<td style=text-align:center>";
			if(!empty($report->scanned_folders)){
				foreach(explode(";",$report->scanned_folders) as $folder){
					if(!empty($folder)){
						echo $folder."<br>";
					}
				}
			}
			echo "</td><td style=text-align:center>";
			echo "<span style=color:green id=scan_files>".$report->scanned_files." files scanned<br></span>";
			echo "<span style=color:red id=malicious_files>".$vulnerablefies." files found Malicious</span>";
			echo "</td><td style=text-align:center id=start_time>".date("M j, Y, g:i:s a",$report->start_timestamp)."</td>";
			echo "<td><a href='".add_query_arg( array('tab' => 'default', 'view' => $report->id), $currenturl )."'>View Details</a> <a href='".add_query_arg( array('tab' => 'default', 'delete' => $report->id), $currenturl )."'>Delete</a></td>";
			echo "</tr>";
		
		}
	}
	 ?>
</tbody>
</table>

<?php
}
?>
<script>
	jQuery(document).ready(function() {
		jQuery('#reports_table').DataTable({
			<?php if(! isset($_GET['view'])){ ?>
				"order": [[ 3, "desc" ]]
			<?php }
			else{ ?>
				"order": [[ 2, "desc" ]]
			<?php } ?>
		});
	} );
</script>