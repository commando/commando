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
	
	($_SERVER['SCRIPT_NAME'] !== "/controller.php") ? header("Location: /") : null;
	
	Functions::check_required_parameters(array($_GET['param1']));
	
	//Get recipe
	$result = MySQLQueries::get_recipe($_GET['param1']);
	$recipe = MySQLConnection::fetch_object($result);
	$recipe = Functions::format_dates($recipe);
	
	$interpreters = array("shell", "bash", "perl", "python", "node.js");
	
	Header::set_title("Commando.io - Edit Recipe");
	Header::render(array("chosen", "codemirror"));
	
	Navigation::render("recipes");
?> 
    <div class="container">
           
      <div>
      	 <h1 class="header" style="float: left;"><?= $recipe->name ?></h1> 
     	 
     	<div style="float: right;">
     	 	<a href="/view-recipe/<?= $recipe->id ?>" class="btn btn-large"><?= substr($recipe->version, 0, 10) ?> (HEAD)</a>
     	 </div>
      </div>
      
	  <div class="row">
    	<div class="span12 well">
			<form id="form-edit-recipe" class="well form-horizontal" method="post" action="/actions/edit_recipe.php">
		    	<input type="hidden" name="id" value="<?= $recipe->id ?>" />
		    	<fieldset>
			    	<div class="control-group">
			        	<label class="control-label" for="recipe-name">Name</label>
			        	<div class="controls">
			          		<input type="text" class="input-large" id="recipe-name" name="name" placeholder="RECIPE NAME" maxlength="30" value="<?= $recipe->name ?>" />
			          		<p class="help-block">The recipe name. Must be unique.</p>
			        	</div>
			        </div>
			        <div class="control-group">
			        	<label class="control-label" for="recipe-interpreter">Interpreter</label>
			        	<div class="controls">
			          		<select name="interpreter" id="recipe-interpreter" class="span2" data-placeholder="">
								<? foreach($interpreters as $interpreter): ?>
									<option value="<?= $interpreter ?>" <? if($interpreter === $recipe->interpreter): ?>selected="selected"<? endif; ?>><?= ucfirst($interpreter) ?></option>	
								<? endforeach; ?>
							</select>
			          		<p class="help-block">The interpreter to execute the recipe with. If you wish to write scripts with control structures and functions select an interpreter other than shell.</p>
			        	</div>
			        </div>
			        <div class="control-group">
			    		<label class="control-label" for="recipe-notes">Notes</label>
			    		<div class="controls">
			    			<textarea id="recipe-notes" name="notes" class="notes span9" maxlength="5000"><?= $recipe->notes ?></textarea>
			    			<p class="help-block" style="clear: both;">Optional notes and comments you wish to attach to the recipe. <a href="http://daringfireball.net/projects/markdown/">Markdown</a> is supported.</p>
			    		</div>
			    	</div>
			    	<div class="control-group">
			    		<label class="control-label" for="recipe-editor">Recipe</label>
			    		<div class="controls">
			    			<textarea id="recipe-editor" name="content"><?= $recipe->content ?></textarea>
			    			<p class="help-block" style="clear: both;"></p>
			    		</div>
			    	</div>
			    	<div class="control-group">
						<div class="controls">
							<a class="btn btn-primary" id="edit-recipe-submit" onclick="validate_edit_recipe();"><i class="icon-ok-sign icon-white"></i> Update Recipe</a>
							<a class="btn" href="/view-recipe/<?= $recipe->id ?>">Cancel</a>
						</div>
			       </div>
			    </fieldset>
	        </form> 
		</div>
	  </div>   
<?php
	Footer::render(array("chosen", "codemirror", "autosize", "edit-recipe"));
?>