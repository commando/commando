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

	Header::set_title("Commando.io - Dashboard");
	Header::render();
	
	Navigation::render("dashboard");
?>
    <div class="container">

      <div class="hero-unit">
		<h2 class="headline">A better way to do <strong>dev-ops</strong>.</h2>
      </div>

      <div class="row">
        <div class="span4 well" style="width: 330px">
          <h1 style="margin-bottom: 10px;"><a href="/servers">Servers</a></h1>
          <p>Servers can either be physical hardware, or virtualized/cloud instances.</p>
          <p><a class="btn btn-primary btn-large" href="/servers">Servers &raquo;</a></p>
        </div>
        <div class="span4 well" style="width: 330px">
          <h1 style="margin-bottom: 10px;"><a href="/groups">Groups</a></h1>
          <p>Groups are a way to organize servers into collections. You may choose to create groups based on server role or location.</p>
          <p><a class="btn btn-primary btn-large" href="/groups">Groups &raquo;</a></p>
        </div>
        <div class="span4 well" style="width: 330px">
          <h1 style="margin-bottom: 10px;"><a href="/recipes">Recipes</a></h1>
          <p>Recipes are containers of commands that are fully versioned. Recipes can be written in pure <i><strong>shell</strong></i>, <i><strong>bash</strong></i>, <i><strong>perl</strong></i>, <i><strong>python</strong></i>, or <i><strong>node.js</strong></i>.</p>
          <p><a class="btn btn-primary btn-large" href="/recipes">Recipes &raquo;</a></p>
        </div>
      </div>
<?php
	Footer::render();
?>