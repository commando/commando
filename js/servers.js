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
var server_label_original = '';

function isServerLabelUnique(throttle, callback) {
	clearTimeout(timer);
	
	var server_label = $("#add-server").is(":visible") ? $("#add-server-label") : $("#edit-server-label");
	
	if($(server_label).val().length === 0) {
		if(!$(server_label).parents(".control-group").hasClass("error")) {
			$(server_label).parents(".control-group").addClass("error");		
		}
		
		typeof callback === "function" ? callback.call(this, false) : null;
		return;
	}
	
	//Server label not updated, return true, do not check for duplicates
	if(server_label_original === $(server_label).val()) {
		if($(server_label).parents(".control-group").hasClass("error")) {
			$(server_label).parents(".control-group").removeClass("error");
		}
		
		typeof callback === "function" ? callback.call(this, true) : null;
		return;
	}
	
	var wait_time = (throttle) ? 200 : 0;
	
	timer = setTimeout(function() {
		Request.ajax("/actions/check_server_label.php", {
			label: $(server_label).val()
		}, function(response) {
			if(typeof response !== "undefined") {
				if(response.unique === true) {
					if($(server_label).parents(".control-group").hasClass("error")) {
						$(server_label).parents(".control-group").removeClass("error");
					}

					typeof this.callback === "function" ? this.callback.call(this, true) : null;
				} else {
					if(!$(server_label).parents(".control-group").hasClass("error")) {
						$(server_label).parents(".control-group").addClass("error");
					}

					typeof this.callback === "function" ? this.callback.call(this, false) : null;
				}
			}
		}.bind({ callback: this.callback }));
	}.bind({ callback: callback }), wait_time);
}

function validate_server() {	
	if($(".modal-footer .btn-primary").hasClass("disabled")) {
		return;
	}
	
	clear_errors();
	$(".modal-footer .btn-primary").addClass("disabled");
	
	isServerLabelUnique(false, function(unique) {
		var ul = unique;
		var server_address = $("#add-server").is(":visible") ? $("#add-server-address") : $("#edit-server-address");
		var ssh_username = $("#add-server").is(":visible") ? $("#add-ssh-username") : $("#edit-ssh-username");
		var ssh_port = $("#add-server").is(":visible") ? $("#add-ssh-port") : $("#edit-ssh-port");
	
		var a = $(server_address).isEmpty();
		var u = $(ssh_username).isEmpty();
		var p = $(ssh_port).isEmpty();
		
		if(ul && !a && !u && !p) {
			var server_form = $("#add-server").is(":visible") ? $("#form-add-server") : $("#form-edit-server");
			
			if($(ssh_username).val() === "root") {
				bootbox.confirm("It is not recommended to connect with the <strong>root</strong> user for security reasons. Are you sure?", function(confirmed) {
					if(confirmed) {
						$(server_form).submit();
					} else {
						$(server_form).modal("show");
						$(".modal-footer .btn-primary").removeClass("disabled");
					}
				});
			} else {
				$(server_form).submit();
			}
		} else {
			$(".modal-footer .btn-primary").removeClass("disabled");
		}
	});
}

var ssh_connect = function(index) {
	$(this).children("a.delete-server").hide();
	$(this).children("div.container-server-add-ssh-key-instructions").hide();
	
	if($(this).hasClass("not-connected")) {
		$(this).removeClass("not-connected");
	}
	
	if($(this).children("div.ssh-progress:hidden")) {
		if($(this).children("a.ssh-status:visible")) {
			$(this).children("a.ssh-status").hide();
			$(this).children("div.ssh-progress").slideDown(200);
		} else {
			$(this).children("div.ssh-progress").slideDown(200);
		}
	}
	
	Request.ajax("/actions/ssh_connect.php", {
		address: $(this).attr("data-address"),
		port: $(this).attr("data-port"),
		username: $(this).attr("data-username")
	}, function(response) {
		if(typeof response === "undefined" || response.error) {
			$(this.server).children("div.ssh-progress").slideUp(200, function() {
				$(this.server).addClass("not-connected");
				
				$(this.server).children("a.ssh-status")
				              .html("SSH Not Connected")
				              .removeClass("btn-success")
				              .addClass("btn-danger")
				              .css("display", "inline-block"); 
			
				$(this.server).children("div.container-server-add-ssh-key-instructions")
							  .css("display", "block");
			
				$(this.server).children("a.delete-server").fadeIn(200);
			}.bind({ server: $(this.server) }));
		} else {
			$(this.server).children("div.ssh-progress").slideUp(200, function() {
				$(this.server).removeClass("not-connected");
			
				$(this.server).children("a.ssh-status")
				              .html("SSH Connected")
				              .removeClass("btn-danger")
				              .addClass("btn-success")
				              .css("display", "inline-block");
			
				$(this.server).children("a.delete-server").fadeIn(200);
			}.bind({ server: $(this.server) }));
		}
				
		if(this.index === ($(".server").length - 1)) {
			$("#refresh-server-status").click(function() {
				if($("#refresh-server-status").hasClass("disabled")) {
					return;
				}
				
				$("#refresh-server-status").unbind("click");
				$("#refresh-server-status").addClass("disabled");
				$(".server").each(ssh_connect);
			});
			
			$("#refresh-server-status").removeClass("disabled");
		}
	}.bind({ server: $(this), index: index }));
}

$(document).ready(function() {	
	$("#add-server").modal({
		show: false,
		backdrop: 'static'
	});
	
	$("#edit-server").modal({
		show: false,
		backdrop: 'static'
	});
	
	$("#server-add-ssh-key-instructions").modal({
		show: false,
		backdrop: 'static'
	});
	
	$(".server").each(ssh_connect);
	
	prettyPrint();
	
	$(".container-server-add-ssh-key-instructions").click(function() {
		var element = this;
		
		if($(element).children("a").hasClass("disabled")) {
			return;
		}

		$(element).children("a").addClass("disabled");
		
		Request.ajax("/actions/get_public_ssh_key.php", {}, function(response) {
			if(typeof response !== "undefined") {
				$("#server-add-ssh-key-public-key").html(response.public_ssh_key);
				$("#server-add-ssh-key-port").html($(element).parent("div").attr("data-port"));
				$("#server-add-ssh-key-username").html($(element).parent("div").attr("data-username"));
				$("#server-add-ssh-key-address").html($(element).parent("div").attr("data-address"));
				$("#server-add-ssh-key-instructions").modal('show');
			}
			
			$(element).children("a").removeClass("disabled");
		});
	});
	
	$("#add-server-group, #edit-server-group").chosen();
	
	$("#add-server-tags, #edit-server-tags").tagsInput({
		'height': '60px',
		'interactive': true,
		'defaultText': '',
		'removeWithBackspace' : true,
		'placeholderColor': '#555555'
	});
	
	$("#add-server-tags_tag, #edit-server-tags_tag").upperCase();
	
	$("#add-server-label, #edit-server-label").bind("keyup paste", function() {
		isServerLabelUnique(true);
	});
	
	$("#add-server-label, #edit-server-label").upperCase();
	$("#add-server-label, #edit-server-label").alphaNumeric();
	$("#add-ssh-port, #edit-ssh-port").numericOnly();
	
	$("#add-server, #edit-server").on("shown", function() {		
		$("#add-server, #edit-server").find(".modal-body").animate({
			scrollTop: 0
		}, 50);
	});
	
	$(".action-add-server").click(function() {
		var element = this;
		
		if($(element).hasClass("disabled")) {
			return;
		}
		
		$(element).addClass("disabled");
		
		Request.ajax("/actions/get_settings.php", {}, function(response) {
			if(typeof response !== "undefined") {
				if(response !== null && response.data !== "undefined" && response.data.default_ssh_username.length > 0) {
					$("#add-ssh-username").val(response.data.default_ssh_username);
				}
				
				if(response !== null && response.data !== "undefined" && response.data.default_ssh_port.length > 0) {
					$("#add-ssh-port").val(response.data.default_ssh_port);
				}
				
				$("#add-server").modal("show");
				
				$(element).removeClass("disabled");
			}
		});
	});
	
	$(".server").click(function(e) {
		var element = $(this);
		
		if(!$(e.target).hasClass('delete-server') && !$(e.target).hasClass('btn-server-add-ssh-key-instructions')) {
			Request.ajax("/actions/get_server.php", {
				id: $(element).attr("id")
			}, function(response) {
				if(typeof response !== "undefined") {
					$("#edit-server-id").val(response.id);
					$("#edit-server-label").val(response.label);	
					server_label_original = response.label;
					$("#edit-server-group").val(response.group);
					$("#edit-server-group").trigger("liszt:updated");
					
					if(response.tags !== null && response.tags.length > 0) {
						$("#edit-server-tags").val(response.tags);
						$("#edit-server-tags").importTags(response.tags);
					} else {
						$("#edit-server-tags").val("");
						$("#edit-server-tags").importTags("");
					}
					
					$("#edit-server-address").val(response.address);
					$("#edit-ssh-username").val(response.ssh_username);
					$("#edit-ssh-port").val(response.ssh_port);
					$("#edit-server-added").val(response.added);
					$("#edit-server-modified").val(response.modified);
					$("#edit-server").modal("show");
				}
			});
		}
	});
	
	$(".delete-server").click(function() {
		var id = $(this).parent("div").attr("id");
		
		bootbox.setIcons({
			"CONFIRM" : "icon-ok-sign icon-white"
        });
		
		bootbox.confirm("Are you sure you wish to delete this server?", function(confirmed) {
			if(confirmed) {
				Request.ajax("/actions/delete_server.php", {  
					id: id
				}, function(response) {
					if(typeof response !== "undefined") {
						$("#" + id).fadeOut("slow", function() {
							$("#" + id).remove(); 

							if($(".server").length === 0) {
								$("#no-servers").show();
								$("#refresh-server-status").addClass("disabled");
							}
						});

						if($("#" + id).parent(".row").children(".server").length === 1) {
							$("#" + id).parent(".row").prev(".breadcrumb").fadeOut("slow", function() {
								$("#" + id).parent(".row").prev(".breadcrumb").remove();
							});
						}	
					}
				});
			} else {
				return;
			}
	    });
	});
});