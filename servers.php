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
		
	($_SERVER['SCRIPT_NAME'] !== "/controller.php") ? require_once(__DIR__ . "/classes/Requires.php") : Links::$pretty = true;
	
	//Get servers
	$servers = array();
	$result = MySQLQueries::get_servers();
	while($row = MySQLConnection::fetch_object($result)) {
		if(isset($servers[$row->group_name])) {
			array_push($servers[$row->group_name], $row);
		} else {
			$servers[$row->group_name] = array($row);
		}
	}
	
	//Get groups
	$groups = array();
	$result = MySQLQueries::get_groups();
	while($row = MySQLConnection::fetch_object($result)) {
		$groups[] = $row;
	}
	
	Header::set_title("Commando.io - Servers");
	Header::render(array("chosen", "code-pretty", "tagsinput"));
	
	Navigation::render("servers");
?>
	<!-- add server modal -->
	<div class="modal fade" id="add-server" style="display: none;">
	  <div class="modal-header">
	    <a class="close" data-dismiss="modal">×</a>
	    <h3>Add Server</h3>
	  </div>
	  <div class="modal-body">
	    <form id="form-add-server" class="well form-horizontal" method="post" action="/actions/add_server.php">
	    	<div class="control-group">
	        	<label class="control-label" for="add-server-label">Server Label</label>
	        	<div class="controls">
	          		<input type="text" class="input-large" id="add-server-label" name="label" placeholder="SERVER-NAME" maxlength="30" />
	          		<p class="help-block">A human readable label. Must be unique.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="add-server-group">Server Group</label>
	        	<div class="controls">
	          		<select class="span3" id="add-server-group" name="group" data-placeholder="">
	          			<option value="" selected="selected">- DEFAULT -</option>
	          			<?php foreach($groups as $group): ?>
	          				<option value="<?php echo $group->id; ?>"><?php echo $group->name; ?></option>
	          			<?php endforeach; ?>
	          		</select>
	          		<p class="help-block">The group to associated the server with.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="add-server-tags">Server Tags</label>
	        	<div class="controls">
	          		<input id="add-server-tags" name="tags" value="" />
	          		<p class="help-block">A list of attributes to assign to the server. Examples include datacenter, rack, OS, or manufacture.</p>
	        	</div>
	        </div>
	       	<div class="control-group">
	        	<label class="control-label" for="add-server-address"><i class="icon-lock"></i> Server Address</label>
	        	<div class="controls">
	          		<input type="text" class="input-large" id="add-server-address" name="address" placeholder="hostname.domain.com" maxlength="100" />
	          		<p class="help-block">A fully qualified hostname or an ip address.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="add-ssh-username"><i class="icon-lock"></i> SSH Username</label>
	        	<div class="controls">
	          		<input type="text" class="input-medium" id="add-ssh-username" name="ssh_username" placeholder="root" maxlength="100" />
	          		<p class="help-block">The username that SSH connects with.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="add-ssh-port"><i class="icon-lock"></i> SSH Port</label>
	        	<div class="controls">
	          		<input type="text" class="input-small" id="add-ssh-port" name="ssh_port" placeholder="22" maxlength="5" />
	          		<p class="help-block">The port that SSH connects on.</p>
	        	</div>
	        </div>
	       	<div class="aes-key">
	  			<i class="icon-lock"></i> Value stored <a href="http://en.wikipedia.org/wiki/Advanced_Encryption_Standard" target="_blank">AES</a> encrypted.
	  		</div>
	  		<div class="clear"></div>
	  	</form>
	  </div>
	  <div class="modal-footer">
	    <a class="btn btn-primary" id="btn-form-add-server" onclick="validate_server();"><i class="icon-ok-sign icon-white"></i> Add Server</a>
	    <a class="btn" data-dismiss="modal">Close</a>
	  </div>
	</div>
	
	<!-- edit server modal -->
	<div class="modal fade" id="edit-server" style="display: none;">
	  <div class="modal-header">
	    <a class="close" data-dismiss="modal">×</a>
	    <h3>Edit Server</h3>
	  </div>
	  <div class="modal-body">
	    <form id="form-edit-server" class="well form-horizontal" method="post" action="/actions/edit_server.php">
	    	<input type="hidden" id="edit-server-id" name="id" value="" />
	    	<div class="control-group">
	        	<label class="control-label" for="edit-server-label">Server Label</label>
	        	<div class="controls">
	          		<input type="text" class="input-large" id="edit-server-label" name="label" placeholder="SERVER-NAME" maxlength="30" />
	          		<p class="help-block">A human readable label. Must be unique.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="edit-server-group">Server Group</label>
	        	<div class="controls">
	          		<select class="span3" id="edit-server-group" name="group" data-placeholder="">
	          			<option value="" selected="selected">- DEFAULT -</option>
	          			<?php foreach($groups as $group): ?>
	          				<option value="<?php echo $group->id; ?>"><?php echo $group->name; ?></option>
	          			<?php endforeach; ?>
	          		</select>
	          		<p class="help-block">The group to associated the server with.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="edit-server-tags">Server Tags</label>
	        	<div class="controls">
	          		<input id="edit-server-tags" name="tags" value="" />
	          		<p class="help-block">A list of attributes to assign to the server. Examples include datacenter, rack, OS, or manufacture.</p>
	        	</div>
	        </div>
	       	<div class="control-group">
	        	<label class="control-label" for="edit-server-address"><i class="icon-lock"></i> Server Address</label>
	        	<div class="controls">
	          		<input type="text" class="input-large" id="edit-server-address" name="address" placeholder="hostname.domain.com" maxlength="100" />
	          		<p class="help-block">A fully qualified hostname or an ip address.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="edit-ssh-username"><i class="icon-lock"></i> SSH Username</label>
	        	<div class="controls">
	          		<input type="text" class="input-medium" id="edit-ssh-username" name="ssh_username" placeholder="root" maxlength="100" />
	          		<p class="help-block">The username that SSH connects with.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="edit-ssh-port"><i class="icon-lock"></i> SSH Port</label>
	        	<div class="controls">
	          		<input type="text" class="input-small" id="edit-ssh-port" name="ssh_port" placeholder="22" maxlength="5" />
	          		<p class="help-block">The port that SSH connects on.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="edit-server-added">Added</label>
	        	<div class="controls">
	        		<input type="text" class="input-large disabled" id="edit-server-added" disabled />
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="edit-server-modified">Modified</label>
	        	<div class="controls">
	        		<input type="text" class="input-large disabled" id="edit-server-modified" disabled />
	        	</div>
	        </div>
	       	<div class="aes-key">
	  			<i class="icon-lock"></i> Value stored <a href="http://en.wikipedia.org/wiki/Advanced_Encryption_Standard" target="_blank">AES</a> encrypted.
	  		</div>
	  		<div class="clear"></div>
	  	</form>
	  </div>
	  <div class="modal-footer">
	    <a class="btn btn-primary" id="btn-form-edit-server" onclick="validate_server();"><i class="icon-ok-sign icon-white"></i> Update Server</a>
	    <a class="btn" data-dismiss="modal">Close</a>
	  </div>
	</div>
	
	<!-- show add ssh-key instructions -->
	<div class="modal fade" id="server-add-ssh-key-instructions" style="display: none;">
	  <div class="modal-header">
	    <a class="close" data-dismiss="modal">×</a>
	    <h3>Add Public SSH Key Instructions</h3>
	  </div>
	  <div class="modal-body">
	  	<div class="alert alert-info">Execute the following command <u><strong>locally</strong></u> to add our public SSH key to the server.</div>
	  	<pre class="prettyprint lang-sh linenums">echo "<span id="server-add-ssh-key-public-key"></span>" | ssh -p <span id="server-add-ssh-key-port"></span> <span id="server-add-ssh-key-username"></span>@<span id="server-add-ssh-key-address"></span> "mkdir ~/.ssh 2> /dev/null; cat >> ~/.ssh/authorized_keys2"</pre>
	  </div>
	  <div class="modal-footer">
	    <a class="btn btn-primary" data-dismiss="modal"><i class="icon-ok-sign icon-white"></i> Ok</a>
	  </div>
	</div>
    
    <div class="container">
    
   	  <h1 class="header">Servers</h1>
   	  
   	  <div class="row">
   	  	<div class="span12 well">
   	 		<a class="btn btn-primary btn-large action-add-server"><i class="icon-plus-sign icon-white"></i> Add Server</a>
   	 		<a id="refresh-server-status" class="btn btn-large disabled"><i class="icon-refresh"></i> Refresh SSH Status</a>  
      	</div>
      </div>
      
	  <div class="row">
		<div class="span12 well">
			<div id="no-servers" class="alert alert-grey no-bottom-margin" <?php if(count($servers) > 0): ?>style="display: none;"<?php endif; ?>>
				 No servers added. <a class="action-add-server">Add</a> a server now.
			</div>
			<?php foreach($servers as $group => $group_servers): ?>
				<ul class="breadcrumb">
				  <li>
				  	<h3 class="grey"><?php echo empty($group) ? 'DEFAULT' : $group; ?></h3>
				  </li>
				</ul>
				<div class="row">
				<?php foreach($group_servers as $server): ?>
		    		<div class="span4 well server" id="<?php echo $server->id; ?>" data-address="<?= $server->address ?>" data-port="<?= $server->ssh_port ?>" data-username="<?= $server->ssh_username ?>">						
						<div class="ssh-progress progress progress-striped active">
			         		<div class="bar" style="width: 100%;"></div>
						</div>
						
						<a class="close delete-server">&times;</a>
		         		<h2><a><?= strtoupper($server->label) ?></a></h2>
						
		         		<h4 class="grey"><?= $server->ssh_username ?>@<?= $server->address ?>:<?= $server->ssh_port ?></h4>
		         		
		         		
		         		<?php if(!empty($server->tags)): ?>
			         		<div class="tags">
								<?php
									foreach(explode(",", $server->tags) as $tag) {
										echo '<span class="label">' . $tag . '</span> ';
									}
								?>
							</div>
						<?php endif; ?>
		
						<a class="ssh-status btn btn-mini disabled"></a>
						
						<div class="container-server-add-ssh-key-instructions">
							<a class="btn btn-danger btn-mini btn-server-add-ssh-key-instructions" style="float: right;">Add Public SSH Key</a>
						</div>
		       		</div>
				<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	  </div>
<?php
	Footer::render(array("chosen", "code-pretty", "tagsinput", "bootbox", "servers"));
?>