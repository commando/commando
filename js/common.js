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

/***********************************************/
/*            Maxlength On Textareas           */
/***********************************************/
$(document).ready(function() {  
    $("textarea[maxlength]").bind("keyup input paste", function() {
        var limit = parseInt($(this).attr('maxlength'));  
        var text = $(this).val();  
        var chars = text.length;  
  
        if(chars > limit) {
            $(this).val(text.substr(0, limit));  
        }  
    });
});

/***********************************************/
/*                  Functions                  */
/***********************************************/	
function clear_errors() {
	$(document).find("div.control-group.error").each(function() {
		$(this).removeClass("error");	
	});
}

function check_uncheck(p_element_control, p_elements, p_callback) {	
	$(p_element_control).click(function() {
		if($(p_element_control).attr("checked")) {
			$(p_elements).attr("checked", true);
		} else {
			$(p_elements).attr("checked", false);
		}
		
		typeof p_callback === "function" ? p_callback.call(this, false) : null;
	});
}

function get_checked_values(p_elements) {
	var checked = new Array();
	
	$(p_elements).each(function() {
		if($(this).is(":checked")) {
			checked.push($(this).val());
		}
	});
	
	return checked;
}

/***********************************************/
/*              AJAX Request Object            */
/***********************************************/
var Request = {
	////
	// Test if an object is empty
	////
	isEmptyObject: function(obj) {
		for(var i in obj) {
			return false;
		}
		return true;
	},
	////
	// AJAX request wrapper
	//   p_server_side_page: Ajax page to call
	//   p_parameters: Arguments to pass to the server side page
	//   p_callback: Handler to call on success
	//   p_options (Optional): Additional configuration options 
	//     -- dataType: {"xml","html","script","json","jsonp","text"} Data type of return object
	//     -- type: {"POST","GET"} Request type
	//     -- timeout: {int} Timeout in milliseconds before fail
	//     -- cache: {true|false} Whether to allow caching or not
	//     -- async: {true|false} Whether to send the request asynchronous, if you need synchronous set to false.
	////
	ajax: function(p_server_side_page, p_parameters, p_callback, p_options) {
		//Set defaults
		var data_type = "json";
		var type = "post";
		var timeout = 15000;
		var cache = false;
		var async = true;

		//Check option parameters
		if (typeof p_options !== "undefined") {
			if(typeof (p_options.dataType) !== "undefined") {
				data_type = p_options.dataType;
			}

			if(typeof (p_options.type) !== "undefined") {
				type = p_options.type;
			}

			if(typeof (p_options.timeout) !== "undefined") {
				timeout = p_options.timeout;
			}

			if(typeof (p_options.cache) !== "undefined") {
				cache = p_options.cache;
			}

			if(typeof (p_options.async) !== "undefined") {
				async = p_options.async;
			}
		}
			
		//Force a GET request, if the parameters is null, undefined, or empty.
		if(typeof p_parameters === "undefined" || p_parameters === null) {
			type = "get";					
		} else {
			if(this.isEmptyObject(p_parameters)) {
				type = "get";
			}
		}

		//Make AJAX request
		$.ajax({ url: p_server_side_page,
			data: p_parameters,
			success: p_callback,
			dataType: data_type,
			type: type,
			timeout: timeout,
			cache: cache,
			async: async,
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				p_callback.bind({ ajaxError: textStatus }).call();
			}
		});
	}
}

/***********************************************/
/*           jQuery Custom Extensions          */
/***********************************************/
if(jQuery) (function() {
	$.extend($.fn, {
		isEmpty: function() {
			var empty = false;
			
			$(this).each(function() {
				//Select
				if(this.tagName == "SELECT") {
					if($(this).children("option").length && $(this).val() !== null && $(this).val().length > 0) {
						$(this).parents(".control-group").removeClass("error");
					} else {
						$(this).parents(".control-group").addClass("error");
						empty = true;
					}	
				} 
				//Text
				else {
					if($(this).val().length > 0) {
						$(this).parents(".control-group").removeClass("error");
					} else {
						$(this).parents(".control-group").addClass("error");
						empty = true;
					}
				}
			});
			
			return empty;
		},
		isEmail: function() {
			var email = true;
			
			$(this).each(function() {
				var reg = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{1,6})$/;
				
				if(reg.test($(this).val()) === false) {
					$(this).parents(".control-group").addClass("error");
					email = false;
				} else {
					$(this).parents(".control-group").removeClass("error");
				}
			});
			
			return email;
		},
		isChecked: function() {
			var checked = false;
			
			$(this).each(function() {
				if($(this).attr("checked")) {
					$(this).parents(".control-group").removeClass("error");
					checked = true;	
				} else {
					$(this).parents(".control-group").addClass("error");
				}
			});
			
			return checked;
		},
		////
		// Check if a field is p_length number of characters
		////
		isLength: function(p_length) {
			var valid = true;
			
			$(this).each(function () {
				if($(this).val().length !== p_length) {
					$(this).parents(".control-group").addClass("error");
					valid = false;
				} else {
					$(this).parents(".control-group").removeClass("error");
				}
			});
			
			return valid;
		},
		startsWith: function(p_starts_with_char) {
			var valid = true;
			
			$(this).each(function () {
				if($(this).val().substring(0, 1) !== p_starts_with_char) {
					$(this).parents(".control-group").addClass("error");
					valid = false;
				} else {
					$(this).parents(".control-group").removeClass("error");
				}
			});
			
			return valid;
		},
		upperCase: function() {
			$(this).each(function() {
				var obj = this;
				
				$(this).bind("keyup input paste", function() {
					$(obj).val($(obj).val().toUpperCase());
				});
			});
		},
		////
		// Force a field to accept only numeric characters
		////
		numericOnly: function() {	
			$(this).each(function() {
				var element = this;
				
				$(this).bind("keyup input paste", function() {
					var reg = /[^0-9]/;
					
					if(reg.test($(element).val())) {
						$(element).val($(element).val().replace(reg, ''));
					}
				});
			});
		},
		////
		// Force a field to accept only [ 0-9 a-z A-Z - _ + . ] characters
		////
		alphaNumeric: function() {
			$(this).each(function() {
				var element = this;
				
				$(this).bind("keyup input paste", function() {
					var reg = /[^0-9a-zA-Z\-\_\+\.]/;
					
					if(reg.test($(element).val())) {
						$(element).val($(element).val().replace(reg, ''));
					}
				});
			});
		},
		////
		// Force a field to remove all spaces
		////
		noSpacesNewlinesTabs: function() {
			$(this).each(function() {
				var element = this;
				
				$(this).bind("keyup input paste", function() {
					var reg = /[ \n\t\r]/;
					
					if(reg.test($(element).val())) {
						$(element).val($(element).val().replace(reg, ''));
					}
				});
			});
		},
		scrollTo: function() {
			$("html, body").animate({
				scrollTop: ($(this).offset().top - 60)
			}, 200);
		}
	});
})(jQuery);