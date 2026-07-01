<?php

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	class Mo_lla_MoWpnsDB
	{
		private $transactionTable;
		private $blockedIPsTable;
		private $whitelistIPsTable;
		private $emailAuditTable;
		private $malwarereportTable;
		private $scanreportdetails;
		private $skipfiles;
		private $hashfile;

		function __construct()
		{
			global $wpdb;
			$this->transactionTable		= $wpdb->base_prefix.'wpns_transactions';
			$this->blockedIPsTable 		= $wpdb->base_prefix.'wpns_blocked_ips';
			$this->attackList		= $wpdb->base_prefix.'wpns_attack_logs';
			$this->whitelistIPsTable	= $wpdb->base_prefix.'wpns_whitelisted_ips';
			$this->emailAuditTable		= $wpdb->base_prefix.'wpns_email_sent_audit';
			$this->IPrateDetails 		= $wpdb->base_prefix.'wpns_ip_rate_details';
			$this->attackLogs		= $wpdb->base_prefix.'wpns_attack_logs';
			$this->malwarereportTable	= $wpdb->base_prefix.'wpns_malware_scan_report';
			$this->scanreportdetails	= $wpdb->base_prefix.'wpns_malware_scan_report_details';
			$this->skipfiles			= $wpdb->base_prefix.'wpns_malware_skip_files';
			$this->hashfile 			= $wpdb->base_prefix.'wpns_malware_hash_file';
			
		}

		function mo_plugin_activate()
		{
			global $wpdb;
			if(!get_option('mo_wpns_dbversion')||get_option('mo_wpns_dbversion')<142)
			{
				update_option('mo_wpns_dbversion', Mo_lla_MoWpnsConstants::DB_VERSION );
				$this->generate_tables();
			} 
			else 
			{
				$current_db_version = get_option('mo_wpns_dbversion');
				if($current_db_version < Mo_lla_MoWpnsConstants::DB_VERSION)
				update_option('mo_wpns_dbversion', Mo_lla_MoWpnsConstants::DB_VERSION );
			}
		}

		function generate_tables()
		{
			global $wpdb;
			$table_Name = $this->transactionTable;
			if($wpdb->get_var("show tables like '$table_Name'") != $table_Name)
			{

				$sql = "CREATE TABLE IF NOT EXISTS " . $table_Name . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `ip_address` mediumtext NOT NULL ,  `username` mediumtext NOT NULL ,
				`type` mediumtext NOT NULL , `url` mediumtext NOT NULL , `status` mediumtext NOT NULL , `created_timestamp` int, UNIQUE KEY id (id) );";
				$results = $wpdb->get_results($sql);
			}

			$tableName = $this->blockedIPsTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` int NOT NULL AUTO_INCREMENT, `ip_address` mediumtext NOT NULL , `reason` mediumtext, `blocked_for_time` int,
				`created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}
			

			$tableName = $this->whitelistIPsTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
            {
                $sql = "CREATE TABLE " . $tableName . " (
                `id` int NOT NULL AUTO_INCREMENT, `ip_address` mediumtext NOT NULL , `created_timestamp` int, UNIQUE KEY id (id) );";
                dbDelta($sql);
            }
			

			$tableName = $this->emailAuditTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` int NOT NULL AUTO_INCREMENT, `ip_address` mediumtext NOT NULL , `username` mediumtext NOT NULL, `reason` mediumtext, `created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}
			$tableName = $this->IPrateDetails;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "CREATE TABLE " . $tableName . " (
				ip varchar(20) , time bigint );";
				dbDelta($sql);
			}

			$tableName = $this->attackLogs;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
			{
				$sql = "create table ". $tableName ." (
						ip varchar(20),
						type varchar(20),
						time bigint,
						input mediumtext );";
				//dbDelta($sql);
				$results = $wpdb->get_results($sql);
				
			}
			$tableName = $this->malwarereportTable;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `scan_mode` mediumtext NOT NULL, `scanned_folders` mediumtext NOT NULL, `scanned_files` int, `start_timestamp` int, `completed_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}

			$tableName = $this->scanreportdetails;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `report_id` bigint, `filename` mediumtext NOT NULL, `report` mediumtext NOT NULL ,  `created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}

			$tableName = $this->skipfiles;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
				`id` bigint NOT NULL AUTO_INCREMENT, `path` mediumtext NOT NULL , `signature` mediumtext, `created_timestamp` int, UNIQUE KEY id (id) );";
				dbDelta($sql);
			}

			$tableName = $this->hashfile;
			if($wpdb->get_var("show tables like '$tableName'") != $tableName)
			{
				$sql = "CREATE TABLE " . $tableName . " (
			 	`id` bigint(20) NOT NULL AUTO_INCREMENT,`file name` varchar(500) NOT NULL,`file hash` mediumtext NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`), UNIQUE KEY `file name` (`file name`),                    UNIQUE KEY `id_2`(`id`));";
			 	dbDelta($sql);
			}
		}
		
		function get_ip_blocked_count($ipAddress)
		{
			global $wpdb;
			$blocking_type = get_option('mo_wpns_time_of_blocking_type');
			if($blocking_type = "permanent"){
				return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable." WHERE ip_address = '".$ipAddress."'" );
			}else{
				return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable." WHERE ip_address = '".$ipAddress."' and blocked_for_time > ".time().";" );
			}
			//$wpdb->get_var("DELETE FROM ".$this->blockedIPsTable." WHERE ip_address = '".$ipAddress."' and blocked_for_time <".time().";");
		//	return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable." WHERE ip_address = '".$ipAddress."'" );
		}
		function get_total_blocked_ips()
		{
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable);
		}
		function get_total_manual_blocked_ips()
		{
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable." WHERE reason = 'Blocked by Admin';");
		}
		function get_total_blocked_ips_waf()
		{
			global $wpdb;
			$totalIPBlocked = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable);
			return $totalIPBlocked - $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->blockedIPsTable." WHERE reason = 'Blocked by Admin';");
		}
		function get_blocked_attack_count($attack)
		{
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->attackList." WHERE type = '".$attack."'" );
		}
		
		function get_count_of_blocked_ips(){
			global $wpdb;
			return $wpdb->get_var("SELECT COUNT(DISTINCT 'ip_address') FROM ".$this->blockedIPsTable);

		}

		function get_time_of_block_ip()
		{	
			global $wpdb;
			$wpdb->get_var("DELETE FROM ".$this->blockedIPsTable." WHERE blocked_for_time <".time().";");
			
		}
		function get_blocked_ip($entryid)
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT ip_address FROM ".$this->blockedIPsTable." WHERE id=".$entryid );
		}

		function get_blocked_ip_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT id, reason, ip_address, created_timestamp FROM ".$this->blockedIPsTable);
		}


		function get_blocked_sqli_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='SQL attack'");
		}
		function get_blocked_rfi_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='RFI attack'");
		}
		function get_blocked_lfi_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='LFI attack'");
		}
		function get_blocked_rce_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='RCE attack'");
		}
		function get_blocked_xss_list()
		{
			global $wpdb;
			return $wpdb->get_results("SELECT ip, type, time, input FROM ".$this->attackList."WHERE type='XSS attack'");
		}

		function insert_blocked_ip($ipAddress,$reason,$blocked_for_time)
		{
			global $wpdb;
			$wpdb->get_var("DELETE FROM ".$this->blockedIPsTable." WHERE blocked_for_time <".time().";");
			$wpdb->insert( 
				$this->blockedIPsTable, 
				array( 
					'ip_address' => $ipAddress, 
					'reason' => $reason,
					'blocked_for_time' => $blocked_for_time,
					'created_timestamp' => current_time( 'timestamp' )
				)
			);
			return;
		}

		function delete_blocked_ip($entryid)
		{
			global $wpdb;
			$wpdb->query( 
				"DELETE FROM ".$this->blockedIPsTable."
				 WHERE id = ".$entryid
			);
			return;
		}

		function get_whitelisted_ip_count($ipAddress)
		{
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->whitelistIPsTable." WHERE ip_address = '".$ipAddress."'" );
		}

		function insert_whitelisted_ip($ipAddress)
		{
			global $wpdb;
			$wpdb->insert( 
				$this->whitelistIPsTable, 
				array( 
					'ip_address' => $ipAddress, 
					'created_timestamp' => current_time( 'timestamp' )
				)
			);
		}

		function get_number_of_whitelisted_ips(){
			global $wpdb;
			return $wpdb->get_var("SELECT COUNT(*) FROM ".$this->whitelistIPsTable."");
		}

		function delete_whitelisted_ip($entryid)
		{
			global $wpdb;
			$wpdb->query( 
				"DELETE FROM ".$this->whitelistIPsTable."
				 WHERE id = ".$entryid
			);
			return;
		}

		function get_whitelisted_ips_list()
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT id, ip_address, created_timestamp FROM ".$this->whitelistIPsTable );
		}

		function get_email_audit_count($ipAddress,$username)
		{
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->emailAuditTable." WHERE ip_address = '".$ipAddress."' AND 
			username='".$username."'" );
		}

		function insert_email_audit($ipAddress,$username,$reason)
		{
			global $wpdb;
			$wpdb->insert( 
				$this->emailAuditTable, 
				array( 
					'ip_address' => $ipAddress,
					'username' => $username,
					'reason' => $reason,
					'created_timestamp' => current_time( 'timestamp' )
				)
			);
			return;
		}

		function insert_transaction_audit($ipAddress,$username,$type,$status,$url=null)
		{
			global $wpdb;
			$data 		= array( 
							'ip_address' 		=> $ipAddress, 
							'username' 	 		=> $username,
							'type' 		 		=> $type,
							'status' 	 		=> $status,
							'created_timestamp' => current_time( 'timestamp' )
						);
			$data['url'] = is_null($url) ? '' : $url;  
			$wpdb->insert(  $this->transactionTable, $data);
			return;
		}

		function get_transasction_list()
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT ip_address, username, type, status, created_timestamp FROM ".$this->transactionTable." order by id desc limit 5000" );
		}

		function get_login_transaction_report()
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT ip_address, username,status, type,created_timestamp FROM ".$this->transactionTable." WHERE type='User Login' order by id desc limit 5000" );
		}

		function get_error_transaction_report()
		{
			global $wpdb;
			return $wpdb->get_results( "SELECT ip_address, username, url, type, created_timestamp FROM ".$this->transactionTable." WHERE type <> 'User Login' order by id desc limit 5000" );
		}

		function update_transaction_table($where,$update)
		{
			global $wpdb;

			$sql = "UPDATE ".$this->transactionTable." SET ";
			$i = 0;
			foreach($update as $key=>$value)
			{
				if($i%2!=0)
					$sql .= ' , ';
				$sql .= $key."='".$value."'";
				$i++;
			}
			$sql .= " WHERE ";
			$i = 0;
			foreach($where as $key=>$value)
			{
				if($i%2!=0)
					$sql .= ' AND ';
				$sql .= $key."='".$value."'";
				$i++;
			}
			
			$wpdb->query($sql);
			return;
		}

		function get_count_of_attacks_blocked(){
			global $wpdb;
			return $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->transactionTable." WHERE status = '".Mo_lla_MoWpnsConstants::FAILED."' OR status = '".Mo_lla_MoWpnsConstants::PAST_FAILED."'" );
		}

		function get_failed_transaction_count($ipAddress)
		{
			global $wpdb;
			$failed_transaction_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->transactionTable." WHERE ip_address = '".$ipAddress."'
                                        			AND status = '".Mo_lla_MoWpnsConstants::FAILED."'" );
			return $failed_transaction_count;
		}

		function delete_transaction($ipAddress)
		{
			global $wpdb;
			$wpdb->query( 
				"DELETE FROM ".$this->transactionTable." 
				WHERE ip_address = '".$ipAddress."' AND status='".Mo_lla_MoWpnsConstants::FAILED."'"
			);
			return;
		}

		function create_scan_report($folderNames, $scan_type){
			global $wpdb;
			$wpdb->insert( 
				$this->malwarereportTable, 
				array( 
					'scan_mode' => $scan_type,
					'scanned_folders' => $folderNames,
					'scanned_files' => 0,
					'start_timestamp' => time()
				)
			);
			$result = $wpdb->get_results( "SELECT * FROM ".$this->malwarereportTable." order by id DESC LIMIT 1");
			if($result){
				$record = $result[0];
				return $record->id;
			}
		}

		function add_report_details($reportid, $filename, $report){
			global $wpdb;
			$wpdb->insert( 
				$this->scanreportdetails, 
				array( 
					'report_id' => $reportid,
					'filename' => $filename,
					'report' => serialize($report),
					'created_timestamp' => time()
				)
			);
		}

		function scan_report_complete($recordId,$no_of_scanned_files){
			global $wpdb;
			$wpdb->query( 
				"UPDATE ".$this->malwarereportTable." set completed_timestamp = ".time().", scanned_files=".$no_of_scanned_files." WHERE id = ".$recordId
			);
		}

		function count_files(){
			global $wpdb;
			$sql= $wpdb->get_results("SELECT SUM(`scanned_files`) AS scan_count FROM ".$this->malwarereportTable);
			return $sql[0]->scan_count;
		}

		function count_malicious_files(){
			global $wpdb;
			$sql= $wpdb->get_results("SELECT COUNT(*) AS total_mal FROM ".$this->scanreportdetails);
			return $sql[0]->total_mal;
		}

		function count_files_last_scan($reportid){
			global $wpdb;
			$sql= $wpdb->get_results('SELECT * FROM '.$this->malwarereportTable.' WHERE `id`="'.$reportid.'"');
			return $sql[0]->scanned_files;
		}

		function count_malicious_last_scan($reportid){
			global $wpdb;
			$sql= $wpdb->get_results('SELECT COUNT(*) AS mal_file FROM '.$this->scanreportdetails.' WHERE `report_id`="'.$reportid.'"');
			return $sql[0]->mal_file;
		}

		function check_hash($hash_of_file){
			global $wpdb;
			$sql= 'SELECT * FROM '.$this->hashfile.' WHERE `file hash`="'.$hash_of_file.'"';
			$result=$wpdb->query( $sql );
			return $result;
		}

		function insert_hash($source_file_path,$hash_of_file){
			global $wpdb;
			$query= 'INSERT INTO '.$this->hashfile.'(`file name`,`file hash`) VALUES("'.$source_file_path.'", "'.$hash_of_file.'") ON DUPLICATE KEY UPDATE `file hash`="'.$hash_of_file.'"';
			$res=$wpdb->query( $query );
		}

		function get_last_id(){
			global $wpdb;
			$result= $wpdb->get_results("SELECT MAX(Id) AS max FROM ".$this->malwarereportTable);
			return $result;
		}

		function get_report_with_id($reportid){
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM ".$this->malwarereportTable." where id=".$reportid );
			return $result;
		}

		function delete_report($reportid){
			global $wpdb;
			$wpdb->query( 
				"DELETE FROM ".$this->malwarereportTable." WHERE id = ".$reportid
			);
		}

		function get_report(){
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM ".$this->malwarereportTable." order by id desc" );
			return $result;
		}

		function get_vulnerable_files_count_for_reportid($reportid){
			global $wpdb;
			$result = $wpdb->get_results( "SELECT count(*) as  count FROM ".$this->scanreportdetails." where report_id=".$reportid );
			return $result;
		}

		function ignorefile($filename){
			$signature = md5_file($filename);
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM ".$this->skipfiles." where path = '".$filename."'" );
			if($result){
				$wpdb->query( 
					"UPDATE ".$this->skipfiles." SET signature = '".$signature."' WHERE path = '".$filename."'"
				);
			} else {
				$wpdb->insert(
					$this->skipfiles, 
					array( 
						'path' => $filename,
						'signature' => $signature,
						'created_timestamp' => time()
					)
				);
			}
		}

		function ignorechangedfile($recordId){
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM ".$this->skipfiles." where id = ".$recordId );
			if($result){
				$record = $result[0];
				$signature = md5_file($record->path);
				$wpdb->query( 
					"UPDATE ".$this->skipfiles." set signature = '".$signature."' WHERE id = ".$recordId
				);
			}
		}

		function getlistofignorefiles(){
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM ".$this->skipfiles."" );
			return $result;
		}

		function get_detail_report_with_id($reportid){
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM ".$this->scanreportdetails." where report_id=".$reportid );
			return $result;
		}

		
	}