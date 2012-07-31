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

function validate_execute() {	
	if($("#execute-btn").hasClass("disabled")) {
		return;
	}
	
	var g = false;
	var r = false;
	
	if($("#execute-groups").val() !== null && $("#execute-groups").val().length > 0) {
		$("#execute_groups_chzn").children("ul").css("border", "1px solid #aaaaaa");
		$("#execute-groups").parents(".control-group").removeClass("error");
		g = true;
	} else {
		$("#execute_groups_chzn").children("ul").css("border", "1px solid #B94A48");
		$("#execute-groups").parents(".control-group").addClass("error");
	}
	
	if($("#execute-recipe").val() !== null && $("#execute-recipe").val().length > 0) {
		$("#execute_recipe_chzn").children("a").css("border", "1px solid #aaaaaa");
		$("#execute-recipe").parents(".control-group").removeClass("error");
		r = true;
	} else {
		$("#execute_recipe_chzn").children("a").css("border", "1px solid #B94A48");
		$("#execute-recipe").parents(".control-group").addClass("error");
	}
	
	if(g && r) {
		$("#execute-btn").addClass("disabled");
		$("#execute-working").show();
		$("#search-results").attr("disabled", "disabled");
		$("#search-results").val("");
		$("#execute-results-jump").hide();
		$("#execute-results-jump").find("ul.dropdown-menu").html("");
		$("#execute-results-container").find("pre").unhighlight();
		
		if($("#execute-results-container").is(":visible")) {
			$("#execute-results-container").hide(200, execute);
		} else {
			execute();
		}
	}
}

function execute() {
	$("#execute-results-container").html("");
	
	Request.ajax("/actions/ssh_execute.php", {
		groups: $("#execute-groups").val(),
		recipe: $("#execute-recipe").val(),
		notes: $("#execute-notes").val()
	}, function(response) {			
		if(typeof this.ajaxError !== "undefined") {
			if(this.ajaxError == "timeout") {
				var error_message = "The request timed out.";
			} else {
				var error_message = this.ajaxError;
			}
			
			$("#execute-results-container").append('<div class="span12 well"><div style="margin-bottom: 10px;"><a class="btn btn-large btn-danger disabled">ERROR</a><span class="label label-important" style="position: relative; top: 6px; float: right;">error</span></div><pre class="alert alert-error red-back no-bottom-margin">' + error_message + '</pre></div>');
		}
		else if(typeof response.error !== "undefined") {
			$("#execute-results-container").append('<div class="span12 well"><div style="margin-bottom: 10px;"><a class="btn btn-large btn-danger disabled">ERROR</a><span class="label label-important" style="position: relative; top: 6px; float: right;">error</span></div><pre class="alert alert-error red-back no-bottom-margin">' + response.error.message + '</pre></div>');
		} else {
			$("#execute-results-jump").find("ul.dropdown-menu").append('<li class="nav-header">Servers</li>');
			
			for(var i = 0; i < response.length; i++) {
				if(response[i].stream == "stderr" || response[i].stream == "error") {
					$("#execute-results-container").append('<div id="' + response[i].server + '" class="span12 well"><div style="margin-bottom: 10px;"><a class="btn btn-large btn-danger disabled">' + response[i].server_label + '</a><span class="label label-important" style="position: relative; top: 6px; float: right;">' + response[i].stream + '</span></div><pre class="alert alert-error red-back no-bottom-margin">' + response[i].result + '</pre></div>');
				} else {
					$("#execute-results-container").append('<div id="' + response[i].server + '" class="span12 well"><div style="margin-bottom: 10px;"><a class="btn btn-large btn-primary disabled">' + response[i].server_label + '</a><span class="label" style="position: relative; top: 6px; float: right;">' + response[i].stream + '</span></div><pre class="execute-results">' + response[i].result + '</pre></div>');
				}
				
				$("#execute-results-jump").find("ul.dropdown-menu").append('<li><a onclick="$(\'#' + response[i].server + '\').scrollTo()">' + response[i].server_label + '</a></li>');
			}
		}
		
		$("#execute-working").hide();
		$("#search-results").removeAttr("disabled");
		
		if($("#execute-results-jump").find("ul.dropdown-menu").html().length > 0) {
			$("#execute-results-jump").show();
		}
		
		$("#execute-btn").removeClass("disabled");
		
		$("#execute-results-container").show(300, function() {
			$("#execute-results-container").scrollTo();
		});
	});
}

$(document).ready(function() {
	$("#execute-groups").chosen();	
	$("#execute-recipe").chosen();
	$("#execute-notes").autosize();
	
	$("#search-results").bind("keyup paste", function() {
		$("#execute-results-container").find("pre").unhighlight();
		
		if($(this).val().length === 0) {
			return;
		}
		
		$("#execute-results-container").find("pre").highlight($(this).val());
	});
});