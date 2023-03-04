<?php 

	const IP_API_URL = 'http://ip-api.com/json/';
	const IP_CACHE_FILE = 'ip_cache.json';
	const IP_LOG_FILE = 'logs.csv';

	$ip_cache = array();
	
	$extracted_data = array();

	if (file_exists(IP_CACHE_FILE)){
		$ip_cache = json_decode(file_get_contents(IP_CACHE_FILE), true);
	}

	if (file_exists(IP_LOG_FILE) && is_readable(IP_LOG_FILE)){

		$handle = fopen(IP_LOG_FILE, 'r');

		if ($handle !== FALSE) {

			while (($line = fgetcsv($handle)) !== FALSE){

				//Search the CSV row for 'DoS'.
				foreach ($line AS $key => $value){

					if (str_contains($value, 'DoS')){

						$explode = explode('SRC=', $line[1]);
						$explode = explode(' ', $explode[1]);

						$extracted_data[] = [
							'time' => $line[0],
							'date' => substr($line[1], 0,7),
							'source_ip' => $explode[0],
							'log' => $line[1]
						];

					}
				}

			}

			fclose($handle);

		} else {
			echo "Error opening file: " . IP_LOG_FILE;
		}

	} else {
		echo "The file was not found or not readable: " . IP_LOG_FILE;
	}

	/*

	while (($line = fgetcsv($logs)) !== FALSE){
		foreach ($line AS $key => $el) {
			if (str_contains($el, 'DoS')){

				$explode = explode('SRC=', $line[1]);
				$explode = explode(' ', $explode[1]);

				$new_log_temp = [
					'time' => $line[0],
					'date' => substr($line[1], 0,7),
					'source_ip' => $explode[0],
					'log' => $line[1]
				];

				//The line below will gather that data between the two values.
				//if ($count > 0 && $count < 100){

					$url = IP_API_URL . $explode[0];
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_URL,$url);
					$result=curl_exec($ch);
					curl_close($ch);

					$get_source_ip_info = json_decode($result);

					$new_log_temp = [
						'time' => $line[0],
						'date' => substr($line[1], 0,7),
						'source_ip' => $explode[0],
						'log' => $line[1],
						'source_region' => $get_source_ip_info->regionName,
						'source_country' => $get_source_ip_info->country,
						'source_city' => $get_source_ip_info->city,
						'source_zip' => $get_source_ip_info->zip,
						'source_coords' => $get_source_ip_info->lat . ', ' . $get_source_ip_info->lon
					];
					
					array_push($new_logs, $new_log_temp);

					//Horrific method, however haven't got premium access to the API key.
					sleep(1);
				//} 
				$count++;

				
			}
		}
		
	}
	*/
