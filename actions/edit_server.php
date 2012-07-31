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
	
	Functions::check_required_parameters(array($_POST['id'], $_POST['label'], $_POST['address'], $_POST['ssh_username'], $_POST['ssh_port']));
	
	MySQLQueries::edit_server($_POST['id'], $_POST['label'], $_POST['group'], $_POST['tags'], $_POST['address'], $_POST['ssh_username'], $_POST['ssh_port']);
	
	Functions::redirect("/servers");
?>