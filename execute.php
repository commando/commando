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
	
	//Make sure we can connect and select the executions collection in MongoDB
	MongoConnection::connect();
	MongoConnection::selectCollection("executions");
	
	//Get groups
	$groups = array();
	$result = MySQLQueries::get_groups();
	while($row = MySQLConnection::fetch_object($result)) {
		$groups[] = $row;
	}
	
	//Get the servers in the default group
	$servers_in_default_group = array();
	$result = MySQLQueries::get_servers_by_groups(array());
	while($row = MySQLConnection::fetch_object($result)) {
		$servers_in_default_group[] = $row;
	}
	
	//Get recipes
	$recipes = array();
	$result = MySQLQueries::get_recipes();
	while($row = MySQLConnection::fetch_object($result)) {
		$recipes[] = $row;
	}
	
	Header::set_title("Commando.io - Execute");
	Header::render(array("chosen", "codemirror"));
?>
	<div id="execute-working" class="progress progress-striped active">
  		<div class="bar" style="width: 100%;"></div>
	</div>
<?php
	Navigation::right('<li id="execute-results-jump" class="dropdown" style="display: none;">
	    			   		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
	          					Jump To Results…
	          					<b class="caret"></b>
	    					</a>
	    					<ul class="dropdown-menu"></ul>
	    			   </li>');
						
	Navigation::render("execute");
?>    
    <div class="container">
           
      <h1 class="header">Execute</h1> 
      
      <div class="row">
   	  	<div class="span12 well">
   	  		<!-- Not implemented yet -->
   	 		<!-- <a href="/execution-history" class="btn btn-primary btn-large"><i class="icon-time icon-white"></i> Execution History</a> -->
			<div style="float: right">	
				<div class="input-prepend" style="float: right">
					<span class="add-on">
						<i class="icon-search"></i>
					</span><input id="search-results" type="text" class="span2" maxlength="100" placeholder="Search Results…" disabled="disabled" />
				</div>
			</div>
      	</div>
      </div>
      
	  <div class="row">
    	<div class="span12 well">
			<form id="form-settings" class="well form-horizontal">
		    	<div class="control-group">
		        	<label class="control-label" for="execute-groups">Groups</label>
		        	<div class="controls">
		          		<select id="execute-groups" name="groups" multiple="multiple" class="span4" data-placeholder="Select groups…">
		          			<?php if(count($servers_in_default_group) > 0): ?>
		          				<option value="">- DEFAULT - (<?= count($servers_in_default_group) ?>)</option>
		          			<?php endif; ?>
		          			<?php foreach($groups as $group): ?>
		          				<?php if($group->servers_count > 0): ?>
		          					<option value="<?= $group->id ?>"><?= $group->name ?> (<?= $group->servers_count ?>)</option>
		          				<?php endif; ?>
		          			<?php endforeach; ?>	
		          		</select>
		          		<p class="help-block">The group of servers to execute the recipe on. You may select multiple groups.</p>
		        	</div>
		        </div>
		    	<div class="control-group">
		        	<label class="control-label" for="execute-recipe">Recipe</label>
		        	<div class="controls">
		          		<select class="span3" id="execute-recipe" name="recipe" data-placeholder="Select a recipe...">
		          			<option value=""></option>
		          			<?php foreach($recipes as $recipe): ?>
		          				<option value="<?= $recipe->id ?>"><?= $recipe->name; ?></option>
		          			<?php endforeach; ?>	
		          		</select>
		          		<p class="help-block">The recipe to execute.</p>
		        	</div>
		        </div>
		        <div class="control-group">
			    	<label class="control-label" for="execute-notes">Notes</label>
			    	<div class="controls">
			    		<textarea id="execute-notes" name="notes"></textarea>
			    		<p class="help-block" style="clear: both;">Optional notes and comments you wish to attach to this execution. <a href="http://daringfireball.net/projects/markdown/">Markdown</a> is supported.</p>
			    	</div>
			    </div>
		        <div class="control-group">
					<div class="controls">
						<a class="btn btn-primary" onclick="validate_execute();" id="execute-btn"><i class="icon-ok-sign icon-white"></i> Execute</a>
					</div>
			    </div>
	        </form>
		</div>
	  </div>
	  
	  <div class="row" id="execute-results-container"></div>
<?php
	Footer::render(array("chosen", "codemirror", "highlight", "autosize", "execute"));
?>