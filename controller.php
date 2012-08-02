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
	
	////
	//	Load requires
	////
	require_once(__DIR__ . "/classes/Requires.php");
	
	////
	// Parse uri and return the request array
	////
	$request = Functions::parse_uri_to_request();
	
	////
	// No request passed, require index page
	////
	if(empty($request)) {
		require_once(__DIR__ . "/index.php");
	}
	else {
		////
		// Build page
		////
		if(isset($request[0])) {
			if($request[0] === "controller.php") {
				Functions::redirect("/");
				die();
			}
			
			$page = $request[0] . ".php";
		} else {
			$page = "index.php";
		}
		
		////
		// Set the rest of the request elements as query string parameters
		////
		for($i = 1; $i < sizeof($request); $i++) {
			$_GET['param' . $i] = $request[$i];
		}
		
		if(!@include_once(__DIR__ . "/" . $page)) {
			Error::halt(404, 'not found', 'File \'' . $page . '\' does not exist.');
		}
	}
?>