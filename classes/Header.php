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
	
 	class Header {		
		private static $title = "Commando.io";
		
		public static function set_title($title) {
			Header::$title = $title;
		}
		
		public static function render(array $additional_css_files = array()) {
			$output = '<!DOCTYPE html>
					<html lang="en">
					  <head>
					    <meta charset="utf-8">
					    <title>' . Header::$title . '</title>
					    <meta name="viewport" content="width=device-width, initial-scale=1.0">
					    <meta name="description" content="A web-based interface for streamlining the use of SSH for application deployment and system administration tasks across remote servers.">
					    <meta name="author" content="NodeSocket, LLC">
					
					    <link rel="shortcut icon" href="/img/favicon.ico">
					    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/img/apple-touch-114.png">
					    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/img/apple-touch-72.png">
					    <link rel="apple-touch-icon-precomposed" href="/img/apple-touch-57.png">
					
					    <link href="/css/bootstrap.min.css" rel="stylesheet">
					    <style type="text/css">
					      body {
					        padding-top: 60px;
					        padding-bottom: 40px;
					      }
					    </style>
					    
					    <link href="/css/bootstrap-responsive.min.css" rel="stylesheet">
					    <link href="/css/additional-styles.css" rel="stylesheet">';
					    
					    ////
					    // Remove possible duplicates from additional_css_files
					    ////
					    if(count($additional_css_files) > 1) {
					    	 $additional_css_files = array_unique($additional_css_files);
					    }
					   
					    ////
					    // Additional CSS files to load
					    ////
					    foreach($additional_css_files as $additional_css_file) {
					    	////
		        			//Make sure the CSS file exists
		        			////
		        			if(file_exists(dirname(__DIR__) . "/css/" . $additional_css_file . ".css")) {
		        				$output .= '<link href="/css/' . $additional_css_file . '.css" rel="stylesheet">';
		        			} else {
		        				Error::halt(404, 'not found', 'The included CSS file \'/css/' . $additional_css_file . '.css\' does not exist.');
		        			}
					    }
					
				  $output .= '<!--[if lt IE 9]>
					      <script src="/js/html5.js"></script>
					    <![endif]-->
					  </head>
					<body>';
					
			echo $output;
		}
	}
?>