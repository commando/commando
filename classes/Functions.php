<?php
	/*
	# Copyright 2012 NodeSocket LLC
	#
	# Licensed under the Apache License, Version 2.0 (the "License");
	# you may not use this file except in compliance with the License.
	# You may obtain a copy of the License at
	#
	# http://www.apache.org/licenses/LICENSE-2.0
	#
	# Unless required by applicable law or agreed to in writing, software
	# distributed under the License is distributed on an "AS IS" BASIS,
	# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	# See the License for the specific language governing permissions and
	# limitations under the License.
	*/
	
 	class Functions {
 		public static function parse_uri_to_request() {
 			$parse = parse_url($_SERVER['REQUEST_URI']);
 			$path = ltrim($parse['path'], '/');
 			$pieces = explode('/', $path);
 			
 			for($i = 0; $i < sizeof($pieces); $i++) {
 				if($pieces[$i] === null || $pieces[$i] === '') {
 					unset($pieces[$i]);
 				}
 			}
 			
 			return $pieces;
 		}
 		
 		public static function generate_random($length = 15) {
			$random = "";
			$possible = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			
	    	$maxlength = strlen($possible);
	    	
	    	if ($length > $maxlength) {
	      		$length = $maxlength;
	    	}
	    	
	    	$i = 0;
	    	
	    	while ($i < $length) {
	     		$random .= substr($possible, mt_rand(0, $maxlength-1), 1);
	       		$i++;
	    	}
	    	
	    	return $random;
	    }
 		
 		public static function generate_id($prefix = "") { 			
 			if(empty($prefix)) {
 				return Functions::generate_random(9) . uniqid() . Functions::generate_random(3);
 			} else {
 				////
 				// If prefix is less than 3 characters, pad with '_' so it is exactly 3 characters
 				////
 				if(strlen($prefix) < 3) {
 					$prefix = str_pad($prefix, 3, '__', STR_PAD_RIGHT);
 				}
 				
 				////
 				// Prefix has a maximum length of 3 characters
 				////
 				$prefix = substr($prefix, 0, 3);
 				
 				return $prefix . "_" . Functions::generate_random(5) . uniqid() . Functions::generate_random(3);
 			}
 		}
 		
 		public static function check_required_parameters($params) {
			if(is_array($params)) {
				foreach($params as $param) {
					if(!isset($param) || empty($param)) {
						if($param != 0) {
							//Output error details
							Error::halt(400, 'bad request', 'Missing required parameter.');
						}
					}	
				}
			} else {
				if(!isset($params) || empty($params)) {
					if($param != 0) {
						//Output error details
						Error::halt(400, 'bad request', 'Missing required parameter.');
					}
				}
			}
		}
		
		public static function redirect($url) {
			header("Location: " . $url);
		}
		
		public static function add_ellipsis($text, $max_length) {
			if(strlen($text) > $max_length) {
				return (substr($text, 0, $max_length) . '…');
			}
			
			return $text;
		}
		
		public static function get_timezone_offset() {
			if(defined("TIMEZONE_OFFSET")) {
				return TIMEZONE_OFFSET;
			}
			
			//Get settings
			$settings = null;
			$result = MySQLQueries::get_settings(false);
			$row = MySQLConnection::fetch_object($result);
			
			if(isset($row->data)) { 
				$row->data = json_decode($row->data);	
			}
			
			$settings = $row;
			
			if(isset($settings->data->timezone_offset)) {
				if(isset($settings->data->timezone_daylight_savings) && $settings->data->timezone_daylight_savings) {
					 $hours = substr($settings->data->timezone_offset, 0, 3);
				     $offsetted_hours = ($hours + 1);
				     
				     if($offsetted_hours < 0 && substr($offsetted_hours, 0, 1) == "-") {
				     	$offsetted_hours = "-" . str_pad(str_replace("-", "", $offsetted_hours), 2, "0", STR_PAD_LEFT);
				     } else {
				     	$offsetted_hours = "+" . str_pad($offsetted_hours, 2, "0", STR_PAD_LEFT);
				     }
				     
				     define("TIMEZONE_OFFSET", str_replace($hours, $offsetted_hours, $settings->data->timezone_offset));
				} else {
					define("TIMEZONE_OFFSET", $settings->data->timezone_offset);
				}
			} else {
				define("TIMEZONE_OFFSET", "+00:00");
			}
			
			return TIMEZONE_OFFSET;
		}
		
		public static function build_execution_history_object($execution_notes, array $groups, stdClass $recipe_object, array $servers, array $results) {
			////
			// For security we have to unset address, ssh_username, and ssh_port from the $servers array so we don't store those sensative values unencrypted
			////
			foreach($servers as $server) {
				unset($server->address);
				unset($server->ssh_username);
				unset($server->ssh_port);
			}
			
			$execution = new stdClass();
			$execution->executed = new MongoDate(gmmktime());
			$execution->notes = $execution_notes;
			$execution->groups = $groups;
			$execution->recipe = $recipe_object;
			$execution->servers = $servers;
			$execution->results = $results;
			return (array)$execution;
		}
		
		public static function get_public_ssh_key() {
			$public_ssh_key = file_get_contents(SSH_PUBLIC_KEY_PATH);
			return ($public_ssh_key) ? $public_ssh_key : null;
		}
		
		public static function format_dates($row) {
			if(!isset($row) || empty($row)) {
				return;
			}
			
			foreach($row as $property => $value) {
				if(is_object($value) || is_array($value)) {
					Functions::format_dates($value);
				} else{
					if(DateTime::createFromFormat('Y-m-d G:i:s', $value) !== FALSE) {
						$row->$property = date(DATE_FORMAT, strtotime($value));
					}	
				}
			}
			
			return $row;
		}
		
		public static function formatBytes($size, $precision = 2) {
			$base = log($size) / log(1024);
			$suffixes = array('bytes', 'kb', 'MB', 'GB', 'TB');
    		return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
		}
	}
?>