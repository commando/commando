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
	
	require_once(__DIR__ . "/markdown/markdown.php");
	
	Functions::check_required_parameters(array($_GET['param1']));
	
	$result = MySQLQueries::get_recipe_head_version($_GET['param1']);
	$head = MySQLConnection::fetch_object($result);

	if(isset($_GET['param2']) && !empty($_GET['param2'])) {
		$recipe_version = $_GET['param2'];
	} else {
		$recipe_version = $head->recipe_version;
	}
	
	$result = MySQLQueries::get_recipe_by_version($_GET['param1'], $recipe_version);
	$recipe = MySQLConnection::fetch_object($result);
	$recipe = Functions::format_dates($recipe);
	
	//Get recipe versions
	$recipe_versions = array();
	$result = MySQLQueries::get_recipe_versions($_GET['param1']);
	while($row = MySQLConnection::fetch_object($result)) {		
		$recipe_versions[] = $row;
	}
	
	//Calculate Statistics
	$recipe->lines = (substr_count($recipe->content, "\n") + 1);
	$recipe->length = Functions::formatBytes(strlen($recipe->content));
	
	//Get the correct language for code-pretty
	switch($recipe->interpreter) {
		case 'shell':
			$code_pretty_lang = "lang-sh";
			break;
		case 'bash':
			$code_pretty_lang = "lang-bsh";
			break;
		case 'node.js':
			$code_pretty_lang = "lang-js";
			break;
		case 'perl':
			$code_pretty_lang = "lang-perl";
			break;
		case 'python':
			$code_pretty_lang = "lang-py";
			break;
	}
	
	Header::set_title("Commando.io - View Recipe");
	Header::render(array("chosen", "code-pretty"));
	
	Navigation::render("recipes");
?> 
    <div class="container">
           
      <div>
      	 <h1 class="header" style="float: left;"><?php echo $recipe->name ?></h1> 
     	 
     	 <div style="float: right;">
     	 	 <a class="btn btn-large disabled"><?php echo substr($recipe->version, 0, 10) ?><?php if($recipe->recipe_version === $head->recipe_version): ?> (HEAD)<?php endif; ?></a>
     	 </div>
      </div>
      
      <div class="row">
   	  	<div class="span12 well">
      		<?php if($recipe->recipe_version === $head->recipe_version): ?>
      			<a href="<?php echo Links::render("edit-recipe", array($recipe->id)) ?>" class="btn btn-primary btn-large"><i class="icon-edit icon-white"></i> Edit Recipe</a>
      			<a id="delete-recipe" href="/actions/delete_recipe.php?id=<?php echo $recipe->id ?>" class="btn btn-large"><i class="icon-remove"></i> Delete Recipe</a>
      		<?php else: ?>
      			<div class="alert alert-info no-bottom-margin">
					<h4>Notice!</h4>
					You are viewing an <strong><u>old version</u></strong> of this recipe. Only the <strong><u>head</u></strong> version of recipes may be edited. If you would like to make modifications to this recipe, navigate to the <a href="<?php echo Links::render("view-recipe", array($recipe->id)) ?>">head</a>.
	  			</div>
	  		<?php endif; ?>
      	</div>
      </div>
      
	  <div class="row">
    	<div class="span12 well">
			<div id="recipe-notes" class="alert alert-grey fade in" <?php if(empty($recipe->notes)): ?>style="display: none;"<?php endif; ?>>
				 <a class="close" data-dismiss="alert">&times;</a>
				 <?php echo Markdown($recipe->notes) ?>
			</div>
			<div class="navbar navbar-static">
            	<div class="navbar-inner">
              		<div class="container" style="width: auto;">
                		<a class="brand"><?php echo ucfirst($recipe->interpreter) ?></a>
               			<ul class="nav">
		                	<li class="divider-vertical"></li>
		                	<li>
		                		<a><?php echo $recipe->lines ?> <?php echo $recipe->lines == 1 ? 'line' : 'lines'; ?> / <?php echo $recipe->length ?></a>
		                	</li>
		                	<li class="divider-vertical"></li>
		                	<li>
		                		<a>Added: <?php echo $recipe->added ?></a>
		                	</li>
                		</ul>
                		<ul class="navbar-form nav pull-right">
                			<li>
                				<select name="versions" id="recipe-versions" class="span2" data-placeholder="">
									<?php foreach($recipe_versions as $recipe_version): ?>
										<option value="
										
											<?php
												if($recipe_version->id !== $head->recipe_version) {
													echo Links::render("view-recipe", array($recipe->id, $recipe_version->id));
												} else {
													echo Links::render("view-recipe", array($recipe->id));
												}
											?>

										" <?php if($recipe_version->id === $recipe->recipe_version) { echo 'selected="selected"'; } ?>><?php echo substr($recipe_version->version, 0, 10) ?><?php if($recipe_version->id === $head->recipe_version): ?> (HEAD)<?php endif; ?></option>
									<?php endforeach; ?>
								</select>
                			</li>
                		</ul>
              		</div>
            	</div>
          	</div>
			<pre class="prettyprint <?php echo $code_pretty_lang ?> linenums"><?php echo $recipe->content ?></pre>
		</div>
	  </div>   
<?php
	Footer::render(array("chosen", "code-pretty", "bootbox", "view-recipe"));
?>