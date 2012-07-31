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
	
 	class SSH {
		private $ssh_connection;
		private $hostname;
		private $port;
		private $fingerprint;
 		
 		public function __construct($hostname, $port = 22, $throw_error = false) {
 			$this->ssh_connection = @ssh2_connect($hostname, $port);

			if(!$this->ssh_connection) {
 				unset($this->ssh_connection);
 				
 				if($throw_error) {
 					//Throw error details (string)
					throw new Exception(Error::out(504, 'gateway timeout', 'Failed to make a SSH connection to host \'' . $hostname . '\' on port \'' . $port . '\'.'));
 				} else {
 					//Output error details
					Error::halt(504, 'gateway timeout', 'Failed to make a SSH connection to host \'' . $hostname . '\' on port \'' . $port . '\'.');	
 				}
			}
			
			$this->hostname = $hostname;
			$this->port = $port;
			$this->fingerprint = ssh2_fingerprint($this->ssh_connection, SSH2_FINGERPRINT_SHA1 | SSH2_FINGERPRINT_HEX);
			
			return true;
 		}
 		
 		public function auth($username, $public_key_path, $private_key_path, $throw_error = false) {
 			////
		 	// Confirm that the public key exists
		 	////
		 	if(!file_exists($public_key_path)) {
		 		unset($this->ssh_connection);
		 		
		 		if($throw_error) {
		 			//Throw error details (string)
		 			throw new Exception(Error::out(404, 'not found', 'The public SSH key file \'' . $public_key_path . '\' does not exist.'));
		 		} else {
		 			//Output error details
		 			Error::halt(404, 'not found', 'The public SSH key file \'' . $public_key_path . '\' does not exist.');
		 		}
		 	}
		 	
		 	////
		 	// Confirm that the private key exists
		 	////
		 	if(!file_exists($private_key_path)) {
		 		unset($this->ssh_connection);
		 		
		 		if($throw_error) {
		 			//Throw error details (string)
		 			throw new Exception(Error::out(404, 'not found', 'The private SSH key file \'' . $private_key_path . '\' does not exist.'));
		 		} else {
		 			//Output error details
		 			Error::halt(404, 'not found', 'The private SSH key file \'' . $private_key_path . '\' does not exist.');
		 		}
		 	}
 			
 			if(!@ssh2_auth_pubkey_file($this->ssh_connection, $username, $public_key_path, $private_key_path)) {
				unset($this->ssh_connection);
				
				if($throw_error) {
					//Throw error details (string)
					throw new Exception(Error::out(504, 'gateway timeout', 'Failed to authenticate a SSH connection with username \'' . $username . '\' on host \'' . $this->hostname . '\' on port \'' . $this->port . '\'.'));
				} else {
					//Output error details
					Error::halt(504, 'gateway timeout', 'Failed to authenticate a SSH connection with username \'' . $username . '\' on host \'' . $this->hostname . '\' on port \'' . $this->port . '\'.');
				}	
			}
			
			return true;
 		}
 		
 		public function execute($command) {
 			$result_stream = ssh2_exec($this->ssh_connection, $command);
 			$error_stream = ssh2_fetch_stream($result_stream, SSH2_STREAM_STDERR);
 			stream_set_blocking($result_stream, true);
 			stream_set_blocking($error_stream, true);
 			
 			$result = "";
 			while($line = fgets($result_stream)) { 
            	flush(); 
                $result .= $line;
            }
            
            $result_error = "";
 			while($line = fgets($error_stream)) { 
            	flush(); 
                $result_error .= $line;
            }
 			
 			fclose($result_stream);
 			fclose($error_stream);
 			
 			if(!empty($result_error)) {
 				return (object)array("stream" => "stderr", "result" => $result_error); 
 			} else {
 				return (object)array("stream" => "stdout", "result" => $result); 
 			}
 		}
	}
?>