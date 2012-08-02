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
	
	class MongoConnection {
		/**
 		 * @staticvar Resource
 		 */
		private static $mongo_connection;
		private static $mongo_db;
		private static $mongo_collection;
		
		public static function connect($replica_set = false) {
			try {
				MongoConnection::$mongo_connection = new Mongo("mongodb://" . MongoConfiguration::username . ":" . MongoConfiguration::password . "@" . MongoConfiguration::hosts . ":" . MongoConfiguration::port . "/" .  MongoConfiguration::database, array("timeout" => 5000, "replicaSet" => $replica_set));
			} catch(Exception $mongoException) { 
				//Output error details
				Error::halt(503, 'service unavailable', 'Temporarily unable to process request. Failed to establish a connection with MongoDB. Please retry.');
			}
			
			if(empty(MongoConnection::$mongo_connection)) {
				Error::halt(503, 'service unavailable', 'Temporarily unable to process request. Failed to establish a connection with MongoDB. Please retry.');
			}
			
			try {
				MongoConnection::$mongo_db = MongoConnection::$mongo_connection->selectDB(MongoConfiguration::database);
			} catch(Exception $mongoException) {
				//Output error details
				Error::halt(503, 'service unavailable', 'Temporarily unable to process request. Failed to select the database from MongoDB. Please retry.');
			}
			
			if(empty(MongoConnection::$mongo_db)) {
				//Output error details
				Error::halt(503, 'service unavailable', 'Temporarily unable to process request. Failed to select the database from MongoDB. Please retry.');
			}
		}
		
		public static function selectCollection($collection_name) {
			try {
				$collection = MongoConnection::$mongo_db->selectCollection($collection_name);
			} catch(Exception $mongoException) {
				//Output error details
				Error::halt(503, 'service unavailable', 'Temporarily unable to process request. Failed to select the MongoDB collection. Please retry.');	
			}
			
			if(empty($collection)) {
				//Output error details
				Error::halt(503, 'service unavailable', 'Temporarily unable to process request. Failed to select the MongoDB collection. Please retry.');
			}
			
			MongoConnection::$mongo_collection = $collection;
		}
		
		public static function find($query = array(), Array $fields = array(), $allow_slave_query = false) {
			if(!is_array($query)) {
				$query = json_decode($query);
			}
			
			if($allow_slave_query) {
				return MongoConnection::$mongo_collection->find($query, $fields)->slaveOkay();
			} else {
				return MongoConnection::$mongo_collection->find($query, $fields);
			}
		}
		
		////
		// Options
		// 		safe (int | 'majority'): Number of servers that have to successfully acknowledge the write before returning success. Majority automagically calculates the number of servers needed for a majority.
		//		fsync (bool): Force the database to fsync all writes up to this point to disk (by default, MongoDB fsyncs writes once per minute). Don't enable this unless you have a good reason, it kills performance.
		////
		public static function insert(Array $data, Array $options = array("safe" => 1, "fsync" => false)) {
			try {
				return MongoConnection::$mongo_collection->insert($data, $options);
			} catch(Exception $MongoCursorException) {
				//Output error details
				Error::halt(503, 'service unavailable', 'Temporarily unable to process request. Failed to insert into the MongoDB collection. Please retry.');	
			}
		}
		
		public static function close() {
			////
			// Make sure a connection has been established before attemping to close
			////
			if(!empty(MongoConnection::$mongo_connection)) {
				return MongoConnection::$mongo_connection->close();
			}
		}
	}
?>