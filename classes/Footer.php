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
	
 	class Footer {		
		public static function render(array $additional_js_files = array()) {
			echo '<hr>
				  <footer>
        		  	<p style="float: left;">Copyright &copy; ' . date("Y") . ' <a href="http://commando.io">Commando.io</a>. All rights reserved.<br />Created by <a href="http://www.nodesocket.com">NodeSocket</a> LLC.</p>
        		  	<p style="float: right;">v' . Version::current . '</p>
      			  </footer>
      		     </div>';
        
        	////
        	// JavaScript files that are always loaded
        	////
        	echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        	      <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>
        		  <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        		  <script type="text/javascript" src="/js/common.js"></script>';
        	
        
        	////
			// Remove possible duplicates from additional_js_files
			////
			if(sizeof($additional_js_files) > 1) {
				$additional_js_files = array_unique($additional_js_files);
			}
        
        	////
        	// Additional JavaScript files to load
        	////
        	foreach($additional_js_files as $additional_js_file) {
        		////
        		// Handle the special case of codemirror
        		///
        		if($additional_js_file === "codemirror") {
        			echo '<script type="text/javascript" src="/js/codemirror/codemirror.js"></script>
        				  <script type="text/javascript" src="/js/codemirror/shell.js"></script>
        				  <script type="text/javascript" src="/js/codemirror/perl.js"></script>
        				  <script type="text/javascript" src="/js/codemirror/python.js"></script>
        				  <script type="text/javascript" src="/js/codemirror/javascript.js"></script>';
        		} 
        		////
        		// Handle the special case of code-pretty
        		///
        		else if($additional_js_file === "code-pretty") {
        			echo '<script type="text/javascript" src="/js/code-pretty/prettify.js"></script>';
        		} else {
        			////
        			//Check to make sure the JavaScript file exists
        			////
        			if(file_exists(dirname(dirname(__FILE__)) . "/js/" . $additional_js_file . ".js")) {
        				echo '<script type="text/javascript" src="/js/' . $additional_js_file . '.js"></script>';
        			} else {
        				Error::halt(404, 'not found', 'The included JavaScript file \'/js/' . $additional_js_file . '.js\' does not exist.');
        			}
        		}
        	}

  			echo'</body>
  			    </html>';
		}
	}
?>