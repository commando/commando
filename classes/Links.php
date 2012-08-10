<?php
	/*
	# Copyright 2012 NodeSocket, LLC
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
	
	class Links {
		public static $pretty = false;
		
		public static function render($page, array $query_params = array()) {
			if(substr($page, 0, 1) !== "/") {
				$page = "/" . $page;
			}
			
			////
			// Fix for actions, look for '.php' in the referer
			//
			// There may be a better way to do this. Do you know?
			////
			if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
				if(strpos($_SERVER['HTTP_REFERER'], ".php") === false) {
					Links::$pretty = true;
				}
			}
				
			if(Links::$pretty) {
				if(count($query_params) > 0) {
					return $page . "/" . implode("/", $query_params);
				}
				
				return $page;	
			} else {
				if(count($query_params) > 0) {
					$params = array();
					for($i = 0; $i < count($query_params); $i++) {
						$params['param' . ($i + 1)] = $query_params[$i];
					}
				
					return $page . ".php?" . http_build_query($params);
				}
				
				return $page . ".php";
			}
		}
	}
?>