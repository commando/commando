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
	
	//Get settings
	$settings = null;
	$result = MySQLQueries::get_settings();
	$row = MySQLConnection::fetch_object($result);
	
	if(isset($row->data)) { 
		$row->data = json_decode($row->data);	
	}
	
	$settings = $row;
	
	$interpreters = array("shell", "bash", "perl", "python", "node.js");
	
	Header::set_title("Commando.io - Add Recipe");
	Header::render(array("chosen", "codemirror"));
	
	Navigation::render("recipes");
?> 
    <div class="container">
           
      <h1 class="header">Add Recipe</h1> 
      
	  <div class="row">
    	<div class="span12 well">
			<form id="form-add-recipe" class="well form-horizontal" method="post" action="/actions/add_recipe.php">
		    	<fieldset>
			    	<div class="control-group">
			        	<label class="control-label" for="recipe-name">Name</label>
			        	<div class="controls">
			          		<input type="text" class="input-large" id="recipe-name" name="name" placeholder="RECIPE NAME" maxlength="30" />
			          		<p class="help-block">The recipe name. Must be unique.</p>
			        	</div>
			        </div>
			        <div class="control-group">
			        	<label class="control-label" for="recipe-interpreter">Interpreter</label>
			        	<div class="controls">
			          		<select name="interpreter" id="recipe-interpreter" class="span2" data-placeholder="">
								<? foreach($interpreters as $interpreter): ?>
									<option value="<?= $interpreter ?>" <? if(isset($settings->data->default_interpreter) && $interpreter === $settings->data->default_interpreter): ?>selected="selected" <? endif; ?>><?= ucfirst($interpreter) ?></option>	
								<? endforeach; ?>
							</select>
			          		<p class="help-block">The interpreter to execute the recipe with. If you wish to write scripts with control structures and functions select an interpreter other than shell.</p>
			        	</div>
			        </div>
			        <div class="control-group">
			    		<label class="control-label" for="recipe-notes">Notes</label>
			    		<div class="controls">
			    			<textarea id="recipe-notes" name="notes"></textarea>
			    			<p class="help-block" style="clear: both;">Optional notes and comments you wish to attach to the recipe. <a href="http://daringfireball.net/projects/markdown/">Markdown</a> is supported.</p>
			    		</div>
			    	</div>
			    	<div class="control-group">
			    		<label class="control-label" for="recipe-editor">Recipe</label>
			    		<div class="controls">
			    			<textarea id="recipe-editor" name="content"></textarea>
			    			<p class="help-block" style="clear: both;"></p>
			    		</div>
			    	</div>
			    	<div class="control-group">
						<div class="controls">
							<a class="btn btn-primary" id="add-recipe-submit" onclick="validate_add_recipe();"><i class="icon-ok-sign icon-white"></i> Add Recipe</a>
							<a class="btn" href="<?= Links::render("recipes") ?>">Cancel</a>
						</div>
			       </div>
			    </fieldset>
	        </form> 
		</div>
	  </div>   
<?php
	Footer::render(array("chosen", "codemirror", "autosize", "add-recipe"));
?>