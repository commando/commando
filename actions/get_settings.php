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
	
	require_once(dirname(__DIR__) . "/classes/Requires.php");
	
	$result = MySQLQueries::get_settings();
	
	$settings = null;
	$result = MySQLQueries::get_settings();
	$row = MySQLConnection::fetch_object($result);
	
	if(isset($row->data)) { 
		$row->data = json_decode($row->data);	
	}
	
	$settings = $row;
	
	Functions::format_dates($settings);
	
	echo json_encode($settings);
?>