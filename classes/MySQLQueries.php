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
	
	class MySQLQueries {
		public static function add_server($server_label, $server_group, $server_tags, $server_address, $server_ssh_username, $server_ssh_port) {
			$SQL = "INSERT INTO servers
			                   (`id`,
			                    label,
			                    `group`,
			                    tags,
			                    address,
			                    ssh_username,
			                    ssh_port,
			                    added,
			                    modified)
			            VALUES (" . MySQLConnection::smart_quote(Functions::generate_id('srv')) . ",
			            		" . MySQLConnection::smart_quote($server_label) . ",
			            		" . MySQLConnection::smart_quote($server_group) . ",
			            		" . MySQLConnection::smart_quote($server_tags) . ",
			            		AES_ENCRYPT(" . MySQLConnection::smart_quote($server_address) . ", " . MySQLConnection::smart_quote(CRYPTO_SEED) . "),
			                    AES_ENCRYPT(" . MySQLConnection::smart_quote($server_ssh_username) . ", " . MySQLConnection::smart_quote(CRYPTO_SEED) . "),
			                    AES_ENCRYPT(" . MySQLConnection::smart_quote($server_ssh_port) . ", " . MySQLConnection::smart_quote(CRYPTO_SEED) . "),
			                    UTC_TIMESTAMP(),
			                    UTC_TIMESTAMP())";
			 
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		}
		
		public static function is_server_label_unique($server_label) {
			//Fast way to check
			$SQL = "SELECT label
			          FROM servers
			         WHERE label = " . MySQLConnection::smart_quote($server_label) . "";
			                       
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			$numb_rows = MySQLConnection::numb_rows($result);
			
			if($numb_rows > 0) {
				return false;
			} else {
				return true;
			}
		}
		
		public static function get_servers() {
			$SQL = "SELECT s.`id`,
			               s.label,
			               s.`group`,
			               g.name AS group_name,
			               s.tags,
			               AES_DECRYPT(s.address, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS address,
			               AES_DECRYPT(s.ssh_username, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS ssh_username,
			               AES_DECRYPT(s.ssh_port, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS ssh_port,
			               CONVERT_TZ(s.added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added,
			               CONVERT_TZ(s.modified, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()) . ") AS modified
			          FROM servers s
		   LEFT OUTER JOIN groups g
		                ON s.group = g.`id`
			      ORDER BY s.added ASC";    
			           
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;
		}
		
		public static function get_server($server_id) {
			$SQL = "SELECT s.`id`,
			               s.label,
			               s.`group`,
						   g.name AS group_name,
			               s.tags,
			               AES_DECRYPT(s.address, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS address,
			               AES_DECRYPT(s.ssh_username, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS ssh_username,
			               AES_DECRYPT(s.ssh_port, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS ssh_port,
			               CONVERT_TZ(s.added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added,
			               CONVERT_TZ(s.modified, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()) . ") AS modified
			          FROM servers s
		   LEFT OUTER JOIN groups g
		                ON s.group = g.`id`
		             WHERE s.`id` = " . MySQLConnection::smart_quote($server_id) . "";    
			           
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;
		}
		
		public static function get_servers_by_groups(array $groups) {
			//Special case for the 'default' group
			if(count($groups) === 0) {
				$SQL = "SELECT `id`,
			                   label,
			                   `group`,
			                   NULL as group_name,
			                   tags,
			                   AES_DECRYPT(address, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS address,
			                   AES_DECRYPT(ssh_username, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS ssh_username,
			                   AES_DECRYPT(ssh_port, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS ssh_port,
			               	   CONVERT_TZ(added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added,
			              	   CONVERT_TZ(modified, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()) . ") AS modified
			              FROM servers
		                 WHERE `group` IS NULL
			          ORDER BY added ASC";
			} else {
				//Smart quote each group
				$groups = array_map(function($element) {
					if(empty($element)) {
						if(!defined('INCLUDE_DEFAULT_GROUP_SERVERS')) {
							define('INCLUDE_DEFAULT_GROUP_SERVERS', true);
						}
						
						return;
					} else {
						return MySQLConnection::smart_quote($element);
					}
				}, $groups);
						
				//Removes empty elements (including 0) from the array completey
				$groups = array_filter($groups);
				
				$SQL = "SELECT s.`id`,
				               s.label,
				               s.`group`,
				               g.name AS group_name,
				               s.tags,
				               AES_DECRYPT(s.address, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS address,
				               AES_DECRYPT(s.ssh_username, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS ssh_username,
				               AES_DECRYPT(s.ssh_port, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS ssh_port,
			               	   CONVERT_TZ(s.added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added,
			              	   CONVERT_TZ(s.modified, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()) . ") AS modified
				          FROM servers s
               LEFT OUTER JOIN groups g
                            ON s.group = g.`id`
			             WHERE g.`id` IN (" . implode(',', $groups) . ")";
			             
			    if(defined('INCLUDE_DEFAULT_GROUP_SERVERS')) {
			      $SQL .= " OR g.`id` IS NULL";
			    }
			         
				$SQL .= " ORDER BY s.added ASC";
		    }
			           
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;	
		}
		
		public static function delete_server($server_id) {
			$SQL = "DELETE
			          FROM servers
			         WHERE `id` = " . MySQLConnection::smart_quote($server_id) . "";
	
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		}
		
		public static function edit_server($server_id, $server_label, $server_group, $server_tags, $server_address, $server_ssh_username, $server_ssh_port) {
			$SQL = "UPDATE servers
			           SET label = " . MySQLConnection::smart_quote($server_label) . ",
			               `group` = " . MySQLConnection::smart_quote($server_group) . ",
			               tags = " . MySQLConnection::smart_quote($server_tags) . ",
			               address = AES_ENCRYPT(" . MySQLConnection::smart_quote($server_address) . ", " . MySQLConnection::smart_quote(CRYPTO_SEED) . "),
			               ssh_username = AES_ENCRYPT(" . MySQLConnection::smart_quote($server_ssh_username) . ", " . MySQLConnection::smart_quote(CRYPTO_SEED) . "),
			               ssh_port = AES_ENCRYPT(" . MySQLConnection::smart_quote($server_ssh_port) . ", " . MySQLConnection::smart_quote(CRYPTO_SEED) . "),
			               modified = UTC_TIMESTAMP()
			         WHERE `id` = " . MySQLConnection::smart_quote($server_id) . "";
		
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		}
		
		public static function add_group($group_name) {
			$SQL = "INSERT INTO groups
			                   (`id`,
			                    name,
			                    added,
			                    modified)
			            VALUES (" . MySQLConnection::smart_quote(Functions::generate_id('grp')) . ",
			                 	" . MySQLConnection::smart_quote($group_name) . ",
			                    UTC_TIMESTAMP(),
			                    UTC_TIMESTAMP())";
			 
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		}
		
		public static function is_group_name_unique($group_name) {
			//Fast way to check
			$SQL = "SELECT name
			          FROM groups
			         WHERE name = " . MySQLConnection::smart_quote($group_name) . "";
			                       
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			$numb_rows = MySQLConnection::numb_rows($result);
			
			if($numb_rows > 0) {
				return false;
			} else {
				return true;
			}
		}
		
		public static function get_groups() {
			$SQL = "SELECT g.`id`,
			               g.name,
			               CONVERT_TZ(g.added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added,
			               CONVERT_TZ(g.modified, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()) . ") AS modified,
			               GROUP_CONCAT(s.label ORDER BY s.added ASC SEPARATOR '<br />') AS servers,
			               COUNT(s.label) AS servers_count
			          FROM groups g 
		   LEFT OUTER JOIN servers s
		                ON g.`id` = s.`group`
		          GROUP BY g.`id`
			      ORDER BY g.added ASC";
			          
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;
		}
		
		public static function get_group($group_id) {
			$SQL = "SELECT `id`,
			               name,
			               CONVERT_TZ(added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added,
			               CONVERT_TZ(modified, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()) . ") AS modified
			          FROM groups
			         WHERE `id` = " . MySQLConnection::smart_quote($group_id) . "";
			           
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;
		}
		
		public static function delete_group($group_id) {
			$SQL = "DELETE
			          FROM groups
			         WHERE `id` = " . MySQLConnection::smart_quote($group_id) . "";
	
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		}
		
		public static function edit_group($group_id, $group_name) {
			$SQL = "UPDATE groups
			           SET name = " . MySQLConnection::smart_quote($group_name) . ",
			               modified = UTC_TIMESTAMP()
			         WHERE `id` = " . MySQLConnection::smart_quote($group_id) . "";
			         
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		}
		
		public static function get_settings($include_modified = true) {
			if($include_modified) {
				$SQL = "SELECT AES_DECRYPT(data, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS data,
	               			   CONVERT_TZ(modified, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()) . ") AS modified
	                      FROM settings
	                     WHERE `id` = 1";
			} else {
				$SQL = "SELECT AES_DECRYPT(data, " . MySQLConnection::smart_quote(CRYPTO_SEED) . ") AS data
	                      FROM settings
	                     WHERE `id` = 1";
			}
		
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;	      
		}
		
		public static function edit_settings($data) {
			//Will always update if a row exists, otherwise will insert
			$SQL = "REPLACE INTO settings
							   (id,
							    data,
							    modified)
					    VALUES (1,
					            AES_ENCRYPT(" . MySQLConnection::smart_quote($data) . ", " . MySQLConnection::smart_quote(CRYPTO_SEED) . "),
					            UTC_TIMESTAMP())";
					            
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);		         
		}
		
		public static function add_recipe($recipe_name, $recipe_interpreter, $recipe_notes, $recipe_content) {
			////
			// Turn MySQL commiting off, run as a transaction
			////
			MySQLConnection::autocommit(false);
			
			$recipe_id = Functions::generate_id('rec');
			
			$SQL = "INSERT INTO recipes
			                   (`id`,
			                    name,
			                    added,
			                    modified)
			            VALUES (" . MySQLConnection::smart_quote($recipe_id) . ",
			                    " . MySQLConnection::smart_quote($recipe_name) . ",
			                    UTC_TIMESTAMP(),
			                    UTC_TIMESTAMP())";
			 
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			
			$recipe_version_id = Functions::generate_id('ver');
			
			$SQL = "INSERT INTO recipe_versions
			                   (`id`,
			                    recipe,
			                    version,
			                    interpreter,
			                    notes,
			                    content,
			                    added)
			            VALUES (" . MySQLConnection::smart_quote($recipe_version_id) . ",
			            		" . MySQLConnection::smart_quote($recipe_id) . ", 
			            		SHA1(" . MySQLConnection::smart_quote($recipe_id . $recipe_interpreter . $recipe_notes . $recipe_content) . "),
			            		" . MySQLConnection::smart_quote($recipe_interpreter) . ",
			            		" . MySQLConnection::smart_quote($recipe_notes) . ",
			            		" . MySQLConnection::smart_quote($recipe_content) . ",
			            		UTC_TIMESTAMP())";
			            		
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		
			$SQL = "INSERT INTO recipe_heads
			                   (recipe,
			                    recipe_version)
			            VALUES (" . MySQLConnection::smart_quote($recipe_id) . ",
			                    " . MySQLConnection::smart_quote($recipe_version_id) . ")";
			 
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		
			////
			// Commit the MySQL transaction
			////
			MySQLConnection::commit();
		}
		
		public static function edit_recipe($recipe_id, $recipe_name, $recipe_interpreter, $recipe_notes, $recipe_content) {
			$SQL = "UPDATE recipes
			           SET name = " . MySQLConnection::smart_quote($recipe_name) . ",
			               modified = UTC_TIMESTAMP()
			         WHERE `id` = " . MySQLConnection::smart_quote($recipe_id) . "";
		
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			
			////
			// Generate SHA1
			////
			$sha1 = sha1($recipe_id . $recipe_interpreter . $recipe_notes . $recipe_content);
			
			////
			// Get if head version matches generated sha1 above
			////
			$SQL = "SELECT rv.version
			          FROM recipe_heads rh,
			               recipe_versions rv
			         WHERE rh.recipe = rv.recipe
			           AND rh.recipe_version = rv.id 
			           AND rh.recipe = " . MySQLConnection::smart_quote($recipe_id) . "
			           AND rv.version = " . MySQLConnection::smart_quote($sha1) . "";
			
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			$numb_rows = MySQLConnection::numb_rows($result);
			
			////
			// This update is the same as head, don't store another version, simply return
			////
			if($numb_rows > 0) {
				return;
			}
			
			////
			// Turn MySQL commiting off, run as a transaction
			////
			MySQLConnection::autocommit(false);
			
			$recipe_version_id = Functions::generate_id('ver');
			
			$SQL = "INSERT INTO recipe_versions
			                   (`id`,
			                    recipe,
			                    version,
			                    interpreter,
			                    notes,
			                    content,
			                    added)
			            VALUES (" . MySQLConnection::smart_quote($recipe_version_id) . ",
			            		" . MySQLConnection::smart_quote($recipe_id) . ", 
			            		SHA1(" . MySQLConnection::smart_quote($recipe_id . $recipe_interpreter . $recipe_notes . $recipe_content) . "),
			            		" . MySQLConnection::smart_quote($recipe_interpreter) . ",
			            		" . MySQLConnection::smart_quote($recipe_notes) . ",
			            		" . MySQLConnection::smart_quote($recipe_content) . ",
			            		UTC_TIMESTAMP())";
			            		
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		
			$SQL = "UPDATE recipe_heads
			           SET recipe_version = " . MySQLConnection::smart_quote($recipe_version_id) . "
			         WHERE recipe = " . MySQLConnection::smart_quote($recipe_id) . "";
		
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
		
			////
			// Commit the MySQL transaction
			////
			MySQLConnection::commit();
		}
		
		public static function is_recipe_name_unique($recipe_name) {
			//Fast way to check
			$SQL = "SELECT name
			          FROM recipes
			         WHERE name = " . MySQLConnection::smart_quote($recipe_name) . "";
			                       
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			$numb_rows = MySQLConnection::numb_rows($result);
			
			if($numb_rows > 0) {
				return false;
			} else {
				return true;
			}
		}
		
		////
		// Select the latest (head) version of all recipes
		////
		public static function get_recipes() {
			$SQL = "SELECT r.`id`,
						   r.name,
						   rv.version,
						   rv.interpreter,
						   rv.notes,
						   rv.content,
			               CONVERT_TZ(r.added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added,
			               CONVERT_TZ(r.modified, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS modified
			          FROM recipe_heads rh,
			          	   recipes r,
			               recipe_versions rv
			         WHERE rh.recipe = r.`id`
			           AND rh.recipe_version = rv.`id`
			           AND r.`id` = rv.recipe
			      ORDER BY r.added ASC";
			
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;	 
		}
		
		////
		// Select the latest (head) version of a specific recipe
		////
		public static function get_recipe($recipe_id) {
			$SQL = "SELECT r.`id`,
						   r.name,
						   rv.`id` AS recipe_version,
						   rv.version,
						   rv.interpreter,
						   rv.notes,
						   rv.content,
			               CONVERT_TZ(rv.added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added
			          FROM recipe_heads rh,
			          	   recipes r,
			               recipe_versions rv
			         WHERE rh.recipe = r.`id`
			           AND rh.recipe_version = rv.`id`
			           AND r.`id` = rv.recipe
			           AND r.`id` = " . MySQLConnection::smart_quote($recipe_id) . "";
		
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;	
		}
		
		public static function get_recipe_head_version($recipe_id) {
			$SQL = "SELECT recipe_version
			          FROM recipe_heads
			         WHERE recipe = " . MySQLConnection::smart_quote($recipe_id) . "";
			                       
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;	
		}
		
		public static function get_recipe_by_version($recipe_id, $recipe_version_id) {
			$SQL = "SELECT r.`id`,
						   r.name,
						   rv.`id` AS recipe_version,
						   rv.version,
						   rv.interpreter,
						   rv.notes,
						   rv.content,
			               CONVERT_TZ(rv.added, '+00:00', " . MySQLConnection::smart_quote(Functions::get_timezone_offset()). ") AS added
			          FROM recipes r,
			               recipe_versions rv
			         WHERE r.`id` = rv.recipe
			           AND r.`id` = " . MySQLConnection::smart_quote($recipe_id) . "
			           AND rv.`id` = " . MySQLConnection::smart_quote($recipe_version_id) . "";
		
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;	
		}
		
		public static function get_recipe_versions($recipe_id) {
			$SQL = "SELECT `id`,
						   version
			          FROM recipe_versions
			         WHERE recipe = " . MySQLConnection::smart_quote($recipe_id) . "
			      ORDER BY added DESC";
		
			$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;	
		}
		
		////
		// Select the number of versions for all recipes
		////
		public static function get_number_of_recipe_versions() {
			$SQL = "SELECT r.`id`,
			               COUNT(rv.id) AS `count`
					  FROM recipes r,
					       recipe_versions rv
                     WHERE r.`id` = rv.recipe
                  GROUP BY rv.recipe
                  ORDER BY r.added ASC";
          	        
        	$result = MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);
			return $result;    
		}
		
		public static function delete_recipes(array $recipe_ids) {
			//Smart quote each recipe id
			$recipe_ids = array_map(function($element) {
				return MySQLConnection::smart_quote($element);
			}, $recipe_ids);
			
			$SQL = "DELETE FROM recipes
			              WHERE `id` IN (" . implode(',', $recipe_ids) . ")";
			             
			MySQLConnection::query($SQL) or Error::db_halt(500, 'internal server error', 'Unable to execute request, SQL query error.', __FUNCTION__, MySQLConnection::error(), $SQL);         
		}
	}
?>