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
	
	//Get groups
	$groups = array();
	$result = MySQLQueries::get_groups();
	while($row = MySQLConnection::fetch_object($result)) {
		$groups[] = $row;
	}
	
	Header::set_title("Commando.io - Groups");
	Header::render();
	
	Navigation::render("groups");
?>
	<!-- add group modal -->
	<div class="modal fade" id="add-group" style="display: none;">
	  <div class="modal-header">
	    <a class="close" data-dismiss="modal">×</a>
	    <h3>Add Group</h3>
	  </div>
	  <div class="modal-body">
	    <form id="form-add-group" class="well form-horizontal" method="post" action="/actions/add_group.php">
	    	<div class="control-group">
	        	<label class="control-label" for="add-group-name">Group Name</label>
	        	<div class="controls">
	          		<input type="text" class="input-large" id="add-group-name" name="name" placeholder="GROUP NAME" maxlength="25" />
	          		<p class="help-block">The group name. Must be unique.</p>
	        	</div>
	        </div>
	  	</form>
	  </div>
	  <div class="modal-footer"> 
	    <a class="btn btn-primary" onclick="validate_group();"><i class="icon-ok-sign icon-white"></i> Add Group</a>
	    <a class="btn" data-dismiss="modal">Close</a>
	  </div>
	</div>
	
	<!-- edit group modal -->
	<div class="modal fade" id="edit-group" style="display: none;">
	  <div class="modal-header">
	    <a class="close" data-dismiss="modal">×</a>
	    <h3>Edit Group</h3>
	  </div>
	  <div class="modal-body">
	    <form id="form-edit-group" class="well form-horizontal" method="post" action="/actions/edit_group.php">
	    	<input type="hidden" id="edit-group-id" name="id" value="" />
	    	<div class="control-group">
	        	<label class="control-label" for="edit-group-name">Group Name</label>
	        	<div class="controls">
	          		<input type="text" class="input-large" id="edit-group-name" name="name" placeholder="GROUP NAME" maxlength="25" />
	          		<p class="help-block">The group name. Must be unique.</p>
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="edit-group-added">Added</label>
	        	<div class="controls">
	        		<input type="text" class="input-large disabled" id="edit-group-added" disabled />
	        	</div>
	        </div>
	        <div class="control-group">
	        	<label class="control-label" for="edit-group-modified">Modified</label>
	        	<div class="controls">
	        		<input type="text" class="input-large disabled" id="edit-group-modified" disabled />
	        	</div>
	        </div>
	  	</form>
	  </div>
	  <div class="modal-footer">
	    <a class="btn btn-primary" onclick="validate_group();"><i class="icon-ok-sign icon-white"></i> Update Group</a>
	    <a class="btn" data-dismiss="modal">Close</a>
	  </div>
	</div>
    
    <div class="container">
    	
      <h1 class="header">Groups</h1>
    
   	  <div class="row">
   	  	<div class="span12 well">
   	 		<a class="btn btn-primary btn-large action-add-group"><i class="icon-plus-sign icon-white"></i> Add Group</a>   
      	</div>
      </div>
            
	  <div class="row">
    	<div class="span12 well">
			<div id="no-groups" class="alert alert-grey no-bottom-margin" <?php if(count($groups) > 0): ?>style="display: none;"<?php endif; ?>>
				 No groups added. <a class="action-add-group">Add</a> a group now.
			</div>
			<div class="row">
				<?php foreach($groups as $group): ?>
		    		<div class="span4 well group" id="<?php echo $group->id; ?>" data-content="<?= $group->servers; ?>" data-title="Servers In Group">
						<a class="close delete-group">&times;</a>
		         		<h3>
		         			<?php if($group->servers_count > 0): ?>
		         				<a class="btn btn-primary btn-mini disabled" style="margin-right: 10px;"><?= $group->servers_count ?></a>
		         			<?php endif; ?>
		         			<a><?= strtoupper($group->name) ?></a>
		         		</h3>
		       		</div>
				<?php endforeach; ?>
			</div>
		</div>
	  </div>
<?php
	Footer::render(array("bootbox", "groups"));
?>  