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
	
	Functions::check_required_parameters(array($_POST['groups'], $_POST['recipe']));
	
	$result = MySQLQueries::get_recipe($_POST['recipe']);
	$recipe = MySQLConnection::fetch_object($result);
	
	if(empty($recipe)) {
		//Output error details
		Error::halt(400, 'bad request', 'The recipe \'' . $_POST['recipe'] . '\' does not exist.');
	}
	
	//Default group handling
	if(count($_POST['groups']) === 1 && empty($_POST['groups'][0])) {
		$_POST['groups'] = array();
	}
	
	$servers = array();
	$results = MySQLQueries::get_servers_by_groups($_POST['groups']);
	while($row = MySQLConnection::fetch_object($results)) {
		$servers[] = $row;
	}

	$returned_results = array();
	foreach($servers as $server) {
		try {
			$ssh = new SSH($server->address, $server->ssh_port, true);
		} catch(Exception $ex) {
			$ex = json_decode($ex->getMessage());
			$returned_results[] = array("server" => $server->id, "server_label" => $server->label, "stream" => "error", "result" => $ex->error->message);
			continue;
		}
		
		try {
			$ssh_auth = $ssh->auth($server->ssh_username, SSH_PUBLIC_KEY_PATH, SSH_PRIVATE_KEY_PATH, true);
		} catch(Exception $ex) {
			$ex = json_decode($ex->getMessage());
			$returned_results[] = array("server" => $server->id, "server_label" => $server->label, "stream" => "error", "result" => $ex->error->message);
			continue;
		}
		
		////
		// Build the correct interpreter and command
		////
		switch($recipe->interpreter) {
			case "shell":
				$command = $recipe->content;
				break;
			case "bash":
				$command = "bash -c $'" . str_replace("'", "\'", $recipe->content) . "'";
				break;
			case "perl":
				$command = "perl -e $'" . str_replace("'", "\'", $recipe->content) . "'";
				break;
			case "python":
				$command = "python -c $'" . str_replace("'", "\'", $recipe->content) . "'";
				break;
			case "node.js":
				$command = "node -e $'" . str_replace("'", "\'", $recipe->content) . "'";
				break;
		}
		
		$result = $ssh->execute($command);
		$returned_results[] = array("server" => $server->id, "server_label" => $server->label, "stream" => $result->stream, "result" => $result->result);
	}
	
	MongoConnection::connect();
	MongoConnection::selectCollection("executions");
	MongoConnection::insert(Functions::build_execution_history_object($_POST['notes'], $_POST['groups'], $recipe, $servers, $returned_results));
	
	echo json_encode($returned_results);
?>