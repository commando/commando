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
	
	class Prerequisites {
		public static function php_version() {
			if(version_compare(PHP_VERSION, '5.3.0', '<')) {
				trigger_error("<h1>PHP 5.3 or greater is required. You are running " . PHP_VERSION . ".</h1>", E_USER_ERROR);
			}
		}
			
		public static function extension($extension) {
			if(!extension_loaded($extension)) {
				trigger_error("<h1>Failed to load required PHP extension " . $extension . "</h1>", E_USER_ERROR);
			}
		}
	}
	
	Prerequisites::php_version();
	Prerequisites::extension("mysqli");
	Prerequisites::extension("mongo");
	Prerequisites::extension("json");
	Prerequisites::extension("ssh2");
?>