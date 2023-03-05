<?php 
    
	use GuzzleHttp\Client;
	require_once 'vendor/autoload.php';

	const IP_API_URL = 'http://ip-api.com/json/';
	const IP_CACHE_FILE = 'ip_cache.json';
	const IP_LOG_FILE = 'logs.csv';

	$ip_cache = array();
	$extracted_logs = array();

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

						$extracted_logs[] = [
							'time' => $line[0],
							'date' => substr($line[1], 0,7),
							'attacker_ip' => $explode[0],
							'log' => $line[0] . $line[1]
						];

					}
				}

			}

			if (class_exists('\GuzzleHttp\Client')){
				foreach ($extracted_logs as $key => $log){
					
					$attacker_ip = $log['attacker_ip'];
			
					// check if the cache file exists
					if (file_exists(IP_CACHE_FILE)) {
						// read the contents of the cache file and decode the JSON string
						$cache_data = json_decode(file_get_contents(IP_CACHE_FILE), true);
			
						// check if the IP address is in the cache
						if (isset($cache_data[$attacker_ip])) {
							// retrieve the cached information
							$cached_info = $cache_data[$attacker_ip];
			
							// use the cached information
							$extracted_logs[$key] += [
								'source_region' => $cached_info['regionName'],
								'source_country' => $cached_info['country'],
								'source_city' => $cached_info['city'],
								'source_zip' => $cached_info['zip'],
								'source_coords' => $cached_info['lat'] . ', ' . $cached_info['lon']
							];
							
							continue; // skip fetching new information
						}
					}
			
					// fetch new information
					$url = IP_API_URL . $attacker_ip;
					$client = new Client();
					$response = $client->request('GET', $url);
					$result = $response->getBody()->getContents();
			
					$get_source_ip_info = json_decode($result);
					
					$extracted_logs[$key] += [
						'source_region' => $get_source_ip_info->regionName,
						'source_country' => $get_source_ip_info->country,
						'source_city' => $get_source_ip_info->city,
						'source_zip' => $get_source_ip_info->zip,
						'source_coords' => $get_source_ip_info->lat . ', ' . $get_source_ip_info->lon
					];
			
					// cache the new information
					$cache_data[$attacker_ip] = [
						'regionName' => $get_source_ip_info->regionName,
						'country' => $get_source_ip_info->country,
						'city' => $get_source_ip_info->city,
						'zip' => $get_source_ip_info->zip,
						'lat' => $get_source_ip_info->lat,
						'lon' => $get_source_ip_info->lon
					];
					file_put_contents(IP_CACHE_FILE, json_encode($cache_data));
				}
			} else {
				echo 'Guzzle is not installed.';
			}

			fclose($handle);

		} else {
			echo "Error opening file: " . IP_LOG_FILE;
		}

	} else {
		echo "The file was not found or not readable: " . IP_LOG_FILE;
	}


