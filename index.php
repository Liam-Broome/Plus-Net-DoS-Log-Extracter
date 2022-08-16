<style>
	.bordered {
		border: solid black;
	}

	table {
		border-collapse: collapse;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	th {
		font-size: 150%;
	}

	td {
		text-align: center;
		font-size: 145%;
	}
</style>

<?php 

	$new_logs = array();

	$logs = fopen('logs.csv', 'r');
	
	$count = 0;

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

					$url = 'http://ip-api.com/json/'. $explode[0];
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
	
	$table = '<table>';
		$table .= '<tr>';
			$table .= '<th>Date Time</th>';
			$table .= '<th>Source Attacker</th>';
			$table .= '<th>Region</th>';
			$table .= '<th>Country</th>';
			$table .= '<th>City</th>';
			$table .= '<th>ZIP/Postal Code</th>';
			$table .= '<th>Coordinates</th>';
		$table .= '</tr>';
		
		foreach ($new_logs AS $key => $data){
			$table .= '<tr>';
			$table .= '<td class="bordered">' . $data['date'] . ' - ' . $data['time'] .  '</td>';
			$table .= '<td class="bordered">' . $data['source_ip'] .  '</td>';
			$table .= '<td class="bordered">' . $data['source_region'] .  '</td>';
			$table .= '<td class="bordered">' . $data['source_country'] .  '</td>';
			$table .= '<td class="bordered">' . $data['source_city'] .  '</td>';
			$table .= '<td class="bordered">' . $data['source_zip'] .  '</td>';
			$table .= '<td class="bordered">' . $data['source_coords'] . '</td>';
			$table .= '</tr>';
		}


die($table);
