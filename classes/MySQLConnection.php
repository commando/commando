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
	
	class MySQLConnection {
		private static $db_connection;
		private static $numb_queries = 0;
		
		public static function connect() {
			MySQLConnection::$db_connection = mysqli_init();
			
			$connected = @mysqli_real_connect(MySQLConnection::$db_connection, MySQLConfiguration::host, MySQLConfiguration::username, MySQLConfiguration::password, MySQLConfiguration::database, MySQLConfiguration::port, MySQLConfiguration::socket, MYSQLI_CLIENT_COMPRESS);	
		
			if(!$connected) {
				//Output error details
				Error::halt(503, 'service unavailable', 'Temporarily unable to process request. Failed to establish a connection with MySQL. Please retry.');
			}
		}
		
		////
		// Ex: Localhost via UNIX socket
		////
		public static function get_host_information() {
			return mysqli_get_host_info(MySQLConnection::$db_connection);
		}
		
		public static function get_server_version() {
			return mysqli_get_server_info(MySQLConnection::$db_connection);
		}
		
		public static function smart_quote($sql) {
		   if (get_magic_quotes_gpc()) {
			   $sql = stripslashes($sql);
		   }
		   
		   if($sql == "") {
		   	  return "NULL";
		   }
		   
		   if($sql == "NULL" || $sql == "null") {
		   	  return $sql;
		   }
		   
		   if (!is_numeric($sql)) {
			  $sql = "'" . mysqli_real_escape_string(MySQLConnection::$db_connection, $sql) . "'";
		   }
		   return $sql;
		}
		
		public static function query($sql) {
			++MySQLConnection::$numb_queries;
			return mysqli_query(MySQLConnection::$db_connection, $sql);
		}
		
		public static function get_previous_query_affected_rows() {
			return mysqli_affected_rows(MySQLConnection::$db_connection);
		}
		
		public static function previous_insert_id() {
			return mysqli_insert_id(MySQLConnection::$db_connection);
		}
		
		public static function fetch_object($result) {
			return mysqli_fetch_object($result);
		}
		
		public static function fetch_assoc($result) {
			return mysqli_fetch_assoc($result);
		}
		
		public static function fetch_row($result) {
			return mysqli_fetch_row($result);
		}
		
		public static function numb_rows($result) {
			return mysqli_num_rows($result);
		}
		
		public static function numb_columns($result) {
			return mysqli_num_fields($result);
		}
		
		public static function column_details($result, $column_index) {
			return mysqli_fetch_field_direct($result, $column_index);
		}
		
		public static function error() {
			return ("#" . mysqli_errno(MySQLConnection::$db_connection) . " - " . mysqli_error(MySQLConnection::$db_connection));
		}
				
		public static function get_numb_queries() {
			return MySQLConnection::$numb_queries;
		}
		
		public static function autocommit($state) {
			if(is_bool($state)) {
				return mysqli_autocommit(MySQLConnection::$db_connection, $state);
			}
		}
		
		public static function commit() {
			return mysqli_commit(MySQLConnection::$db_connection);
		}
		
		public static function close() {
			////
			// Make sure a database connection has been established before attemping to close
			////
			if(!empty(MySQLConnection::$db_connection)) {
				return @mysqli_close(MySQLConnection::$db_connection);
			}
		}
	}
	
	MySQLConnection::connect();
?>