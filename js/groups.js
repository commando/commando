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

var timer;
var group_name_original = '';

function isGroupNameUnique(throttle, callback) {
	clearTimeout(timer);
	
	var group_name = $("#add-group").is(":visible") ? $("#add-group-name") : $("#edit-group-name");
	
	if($(group_name).val().length === 0) {
		if(!$(group_name).parents(".control-group").hasClass("error")) {
			$(group_name).parents(".control-group").addClass("error");		
		}
		
		typeof callback === "function" ? callback.call(this, false) : null;
		return;
	}
	
	//Group name not updated, return true, do not check for duplicates
	if(group_name_original === $(group_name).val()) {
		if($(group_name).parents(".control-group").hasClass("error")) {
			$(group_name).parents(".control-group").removeClass("error");
		}
		
		typeof callback === "function" ? callback.call(this, true) : null;
		return;
	}
	
	var wait_time = (throttle) ? 200 : 0;
	
	timer = setTimeout(function() {
		Request.ajax("/actions/check_group_name.php", {
			name: $(group_name).val()
		}, function(response) {
			if(typeof response !== "undefined") {
				if(response.unique === true) {
					if($(group_name).parents(".control-group").hasClass("error")) {
						$(group_name).parents(".control-group").removeClass("error");
					}

					typeof this.callback === "function" ? this.callback.call(this, true) : null;
				} else {
					if(!$(group_name).parents(".control-group").hasClass("error")) {
						$(group_name).parents(".control-group").addClass("error");
					}

					typeof this.callback === "function" ? this.callback.call(this, false) : null;
				}
			}
		}.bind({ callback: this.callback }));
	}.bind({ callback: callback }), wait_time);
}

function validate_group() {	
	if($(".modal-footer .btn-primary").hasClass("disabled")) {
		return;
	}
	
	clear_errors();
	$(".modal-footer .btn-primary").addClass("disabled");
	
	isGroupNameUnique(false, function(unique) {
		if(unique) {
			var group_form = $("#add-group").is(":visible") ? $("#form-add-group") : $("#form-edit-group");
			$(group_form).submit();
		} else {
			$(".modal-footer .btn-primary").removeClass("disabled");
		}
	});
}

$(document).ready(function() {
	$("#add-group").modal({
		show: false,
		backdrop: 'static'
	});
	
	$("#edit-group").modal({
		show: false,
		backdrop: 'static'
	});
	
	$(".group").each(function() {
		if($(this).attr("data-content").length > 0) {
			$(this).popover({
				placement: 'top',
				delay: { show: 0, hide: 0 }
			});
		}
	});
	
	$("#add-group-name, #edit-group-name").bind("keyup paste", function() {
		isGroupNameUnique(true);
	});
	
	$("#add-group-name, #edit-group-name").upperCase();
	
	$(".action-add-group").click(function() {
		$("#add-group").modal("show");
	});
	
	$(".group").click(function(e) {
		var element = $(this);
		
		$(".popover").fadeOut("fast");
		
		if(!$(e.target).hasClass('delete-group')) {
			Request.ajax("/actions/get_group.php", {
				id: $(element).attr("id")
			}, function(response) {
				if(typeof response !== "undefined") {
					$("#edit-group-id").val(response.id);
					$("#edit-group-name").val(response.name);
					group_name_original = response.name;
					$("#edit-group-added").val(response.added);
					$("#edit-group-modified").val(response.modified);
					$("#edit-group").modal("show");
				}
			});
		}
	});
	
	$(".delete-group").click(function() {
		var id = $(this).parent("div").attr("id");
		
		$(".popover").fadeOut("fast");
		
		bootbox.setIcons({
			"CONFIRM" : "icon-ok-sign icon-white"
        });
		
		bootbox.confirm("Are you sure you wish to delete this group? Any servers currently assigned to this group will be moved to the <strong>default</strong> group.", function(confirmed) {
			if(confirmed) {
				Request.ajax("/actions/delete_group.php", {  
					id: id
				}, function(response) {
					if(typeof response !== "undefined") {
						$("#" + id).fadeOut("slow", function() {
							$("#" + id).remove();

							if($(".group").length === 0) {
								$("#no-groups").show();
							}
						});	
					}
				});
			} else {
				return;
			}
	    });
	});
});