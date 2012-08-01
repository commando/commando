<?php
	////
	//	Load Functions class
	////
	require_once(__DIR__ . "/classes/Functions.php");
	
	////
	// /app.config.php
	////
	if(!file_exists(__DIR__ . "/app.config.php")) {
		if(copy(__DIR__ . "/app.config.default.php", __DIR__ . "/app.config.php")) {
			print "-> Created file '/app.config.php'.\n";
		}
		
		$app_config = file_get_contents(__DIR__ . "/app.config.php");
		
		if($app_config !== false) {
			if(file_put_contents(__DIR__ . "/app.config.php", str_replace("{{CRYPTO_SEED}}", Functions::generate_random(64), $app_config)) !== false) {
				print "-> Wrote randomly generated CRYPTO_SEED to '/app.config.php'.\n";
			}
		}
	}
	
	/////
	// /classes/MySQLConfiguration.php
	////
	if(!file_exists(__DIR__ . "/classes/MySQLConfiguration.php")) {
		if(copy(__DIR__ . "/classes/MySQLConfiguration.default.php", __DIR__ . "/classes/MySQLConfiguration.php")) {
			print "-> Created file '/classes/MySQLConfiguration.php'.\n";
		}
	}
	
	////
	// /classes/MongoConfiguration.php
	////
	if(!file_exists(__DIR__ . "/classes/MongoConfiguration.php")) {
		if(copy(__DIR__ . "/classes/MongoConfiguration.default.php", __DIR__ . "/classes/MongoConfiguration.php")) {
			print "-> Created file '/classes/MongoConfiguration.php'.\n";
		}
	}
	
	////
	// /keys directory
	////
	if(!is_dir(__DIR__ . "/keys")) {
		if(mkdir(__DIR__ . "/keys", 0755, false)) {
			print "-> Created directory '/keys'.\n";
		}
	}
?>