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
	
	require_once(dirname(__DIR__) . "/classes/Requires.php");
	
	$data = array("default_ssh_username" => $_POST['default_ssh_username'],
				  "default_ssh_port" => $_POST['default_ssh_port'],
				  "default_interpreter" => $_POST['default_interpreter'],
				  "timezone_offset" => $_POST['timezone_offset'],
				  "timezone_daylight_savings" => $_POST['timezone_daylight_savings']);
				  
	$result = MySQLQueries::edit_settings(json_encode((object)$data));
	
	Functions::redirect(Links::render("settings", array("saved")));
?>