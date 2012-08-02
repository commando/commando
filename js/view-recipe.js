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

$(document).ready(function() {
	prettyPrint();
	
	$("#recipe-versions").chosen();
	$("#recipe_versions_chzn").css("margin-top", "7px");
	
	$('#recipe-versions').change(function() {
  		window.location = $(this).val();
	});
	
	$("#delete-recipe").click(function(e) {
		e.preventDefault();
		
		bootbox.setIcons({
			"CONFIRM" : "icon-ok-sign icon-white"
        });
		
		bootbox.confirm("Are you sure you wish to delete this recipe? All previous versions of the recipe will be deleted as well.", function(confirmed) {
			if(confirmed) {
				window.location = $('#delete-recipe').attr('href');
			}
		});	
	});
});