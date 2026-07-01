<?php

class Mo_lla_AjaxHandler
{
	function __construct()
	{
		add_action( 'admin_init'  , array( $this, 'mo_wpns_saml_actions' ) );
	}

	function mo_wpns_saml_actions()
	{
		global $moWpnsUtility,$mo_lla_dirName;

		if (current_user_can( 'manage_options' ) && isset( $_REQUEST['option'] ))
		{ 
			switch($_REQUEST['option'])
			{
				case "iplookup":
					$this->lookupIP(sanitize_text_fied($_GET['ip']));	break;
				case "backupDB":
					$this->backupDB();				break;
				case "dissmissfeedback":
					$this->handle_feedback();		break;
				case "whitelistself":
					$this->whitelist_self();		break;
				case "dissmissbruteforce":
					$this->handle_bruteforce();		break;

			}
		}
	}
	
	private function handle_bruteforce(){
		update_site_option("wpns_dont_show_enable_brute_force",true);
		wp_send_json('success');
	}

	private function lookupIP($ip)
	{
        $result=@json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip),true);
		$hostname 	= gethostbyaddr($result["geoplugin_request"]);
		try{
            $timeoffset	= timezone_offset_get(new DateTimeZone($result["geoplugin_timezone"]),new DateTime('now'));
            $timeoffset = $timeoffset/3600;

        }catch(Exception $e){
            $result["geoplugin_timezone"]="";
            $timeoffset="";
        }

		$ipLookUpTemplate  = Mo_lla_MoWpnsConstants::IP_LOOKUP_TEMPLATE;
		if($result['geoplugin_request']==$ip) {

            $ipLookUpTemplate = str_replace("{{status}}", $result["geoplugin_status"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{ip}}", $result["geoplugin_request"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{region}}", $result["geoplugin_region"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{country}}", $result["geoplugin_countryName"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{city}}", $result["geoplugin_city"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{continent}}", $result["geoplugin_continentName"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{latitude}}", $result["geoplugin_latitude"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{longitude}}", $result["geoplugin_longitude"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{timezone}}", $result["geoplugin_timezone"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{curreny_code}}", $result["geoplugin_currencyCode"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{curreny_symbol}}", $result["geoplugin_currencySymbol"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{per_dollar_value}}", $result["geoplugin_currencyConverter"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{hostname}}", $hostname, $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{offset}}", $timeoffset, $ipLookUpTemplate);

            $result['ipDetails'] = $ipLookUpTemplate;
        }else{
            $result["ipDetails"]["status"]="ERROR";
        }

		wp_send_json( $result );

    }
	// private function backupDB()
	function backupDB()
	{
		if ( function_exists('memory_get_usage') && ( (int) ini_get('memory_limit') < 128 ) )
			ini_set('memory_limit', '128M' );
		global $wpdb;
		$tables 		= $wpdb->get_results("SHOW TABLES", ARRAY_N);
		$nooftables 	= count($tables);
		$query			= "";
		$tableswithfk 	= array();
		$tableswithoutfk= array();

		foreach($tables as $table)
		{
			if(is_array($table))
				$table = $table[0];
			$createtable = $wpdb->get_results("SHOW CREATE TABLE  $table", ARRAY_A);
			if(!empty($createtable[0]))
			{
				$createquery = $createtable[0]['Create Table'];
				if (strpos($createquery, 'FOREIGN KEY') !== false) 
					array_push($tableswithfk,$table);
				else
					array_push($tableswithoutfk, $table);
			}
		}
		
		$query .= $this->get_table_query($query,$tableswithoutfk);

		$query .= $this->get_table_query($query,$tableswithfk);

		$fileName = $this->create_db_backup_file($query);
		wp_send_json($fileName);
	}

	private function get_table_query($query,$tables)
	{
		global $wpdb;
		foreach($tables as $table)
		{
			$createtable = $wpdb->get_results("SHOW CREATE TABLE  $table", ARRAY_A);
			if(!empty($createtable[0]))
			{		
				$createquery = $createtable[0]['Create Table'];		
				$query 	    .= 'DROP TABLE IF EXISTS '.$table.";\n";
				$query 	    .= $createquery.";\n\n";
				$data    	 = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
				foreach($data as $record)
				{
					if(count($record)>0)
					{
						$query.= 'INSERT INTO '.$table.' VALUES(';
						$i=0;
						foreach($record as $key=>$value)
						{
							$value = addslashes($value);
							if (isset($value))
								$query.= '"'.$value.'"' ;
							else
								$query.= '""';
							if ($i < (count($record)-1)) { $query.= ','; }
							$i++;
						}
						$query.= ");\n";
					}
				}
				$query.="\n\n";
			}
		}
		return $query;
	}


	private function create_db_backup_file($data)
	{   $folderName = date("Ymd");
		$basepath = get_home_path();
		if(!file_exists($basepath."db-backups")){
			mkdir($basepath."db-backups");
		}
               	$basepath = get_home_path().'db-backups/';
		$handler_obj = new Mo_lla_site_backup;
		$handler_obj->create_index_file($basepath);
		if(!file_exists($basepath.$folderName)){
			mkdir($basepath.$folderName);
		}
		
		$filename = 'db-backup-'.time().'.sql';
		$handle = fopen(get_home_path()."db-backups".DIRECTORY_SEPARATOR.$folderName.DIRECTORY_SEPARATOR.$filename,'w+');
		fwrite($handle,$data);
		fclose($handle);
		return $filename;
	}

	private function handle_feedback()
	{
		update_option('donot_show_feedback_message',1);
		wp_send_json('success');
	}

	private function whitelist_self()
	{
		global $moWpnsUtility;
		$moPluginsUtility = new Mo_lla_MoWpnsHandler();
		$moPluginsUtility->whitelist_ip($moWpnsUtility->get_client_ip());
		wp_send_json('success');
	}

}new Mo_lla_AjaxHandler;