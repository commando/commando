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
	
	////
	// Minimum seed length is 30 characters (enforced)
	//
 	// Do not change once set
 	////	
 	define("CRYPTO_SEED", "");
 	
 	////
 	// Example output: 4/1/2012 4:23:35 PM
 	////
 	define("DATE_FORMAT", "n/j/Y g:i:s A");
 	
 	////
 	// The full path to the public key used for SSH
 	////
 	define("SSH_PUBLIC_KEY_PATH", __DIR__ . "/keys/public-key.pub");
 	
 	////
 	// The full path to the private key used for SSH
 	////
 	define("SSH_PRIVATE_KEY_PATH", __DIR__ . "/keys/private-key");
 	
 	////
 	// Make sure CRYPTO_SEED is set, and at least 30 characters in length
 	////
 	if(!defined("CRYPTO_SEED") || strlen(CRYPTO_SEED) < 30) {
 		Error::halt(409, 'conflict', 'The crypto seed defined in \'app.config.php\' must be configured, and at least 30 characters in length.');
 	}
?>