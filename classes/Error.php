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
	
 	class Error {		
		////
		//	Stop execution, output error json object
		////
		public static function halt($status_code, $status, $message) {
			die('{"error":{"status_code":' . $status_code . ',"status":"' . $status . '","message":"' . $message . '"}}');
		}
		
		////
		//	Output error object
		////
		public static function out($status_code, $status, $message) {
			return '{"error":{"status_code":' . $status_code . ',"status":"' . $status . '","message":"' . $message . '"}}';
		}
		
		////
		//	Fatal database query exception occurred, stop execution, output error json object
		////
		public static function db_halt($status_code, $status, $message, $function_context, $sql_error_details, $sql_query) {					
			die('{"error":{"status_code":' . $status_code . ',"status":"' . $status . '","message":"' . $message . '","sql_message":"' . $sql_error_details . '"}}');
			
			////
			// Right now we, aren't doing anything with $function_context, and $sql_query.
			////
		}
	}
?>