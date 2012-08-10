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
	
	//Get recipes
	$recipes = array();
	$result = MySQLQueries::get_recipes();
	while($row = MySQLConnection::fetch_object($result)) {
		$recipes[$row->id] = $row;
	}
	
	//Get number of versions for all recipes
	$result = MySQLQueries::get_number_of_recipe_versions();
	while($row = MySQLConnection::fetch_object($result)) {
		$recipes[$row->id]->number_of_versions = $row->count;
	}
	
	$recipes = Functions::format_dates($recipes);
	
	Header::set_title("Commando.io - Recipes");
	Header::render();
	
	Navigation::render("recipes");
?>    
    <div class="container">
           
      <h1 class="header">Recipes</h1> 
      
	  <div class="row">
   	  	<div class="span12 well">
      		<a href="<?= Links::render("add-recipe") ?>" class="btn btn-primary btn-large"><i class="icon-plus-sign icon-white"></i> Add Recipe</a>
      	</div>
      </div>
      
      <div class="row">
		<div class="span12 well">
		      <div class="alert alert-info fade in" <?php if(count($recipes) > 0): ?>style="display: none;"<?php endif; ?>>
	  	  		<a class="close" data-dismiss="alert">&times;</a>
	  	  		<h4>Did You Know?</h4>
	  	  		Recipes are containers of commands that are fully versioned. Recipes can be written in pure <i><strong>shell</strong></i>, <i><strong>bash</strong></i>, <i><strong>perl</strong></i>, <i><strong>python</strong></i>, or <i><strong>node.js</strong></i>.
      		  </div>
		      <div id="no-recipes" class="alert alert-grey no-bottom-margin" <?php if(count($recipes) > 0): ?>style="display: none;"<?php endif; ?>>
		      	No recipes added. <a href="<?= Links::render("add-recipe") ?>">Add</a> a recipe now.
			  </div>
	      	  <?php if(count($recipes) > 0): ?>
		      	  <div id="table-container">
			      	  <div class="control-group">
			      	  	<div class="controls">
			      	 		<a id="delete-recipes" class="btn disabled"><i class="icon-remove"></i> Delete Selected</a>
			      	  	</div>
			      	  </div>
				      <table class="table table-striped table-bordered table-condensed">
				      	<thead>
				      		<tr>
				      			<th><input type="checkbox" id="recipe-delete-all-check" /></th>
				      			<th>Name</th>
				      			<th>Interpreter</th>
				      			<th>Number Of Versions</th>
				      			<th>Added</th>
				      			<th>Modified</th>
				      		</tr>
				      	</thead>
				      	<tbody>
			      			<?php foreach($recipes as $recipe): ?>	
			      				<tr id="<?= $recipe->id ?>" class="recipe">
				      				<td><input type="checkbox" class="recipe-delete-check" value="<?= $recipe->id ?>" /></td>
				      				<td><a href="<?= Links::render("view-recipe", array($recipe->id)) ?>"><?= $recipe->name ?></a></td>
				      				<td><?= ucfirst($recipe->interpreter) ?></td>
				      				<td><span class="badge"><?= $recipe->number_of_versions ?></span></td>
				      				<td><?= $recipe->added ?></td>
				      				<td><?= $recipe->modified ?></td>
			      				</tr>
			      			<?php endforeach; ?>
				      	</tbody>
				      </table>
		      	  </div>
		      <? endif; ?>
	    </div>
	  </div>
<?php
	Footer::render(array("bootbox", "recipes"));
?>