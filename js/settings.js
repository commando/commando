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

function validate_edit_settings() {	
	clear_errors();
	
	$(".modal-footer .btn").addClass("disabled");
	$("#form-settings").submit();
}

$(document).ready(function() {
	$("#timezone-offset").val($("#timezone-offset").attr("data-value"));
	$("#timezone-offset").chosen();
	$("#timezone-offset").trigger("liszt:updated");

	$("#default-interpreter").chosen();
	$("#default-interpreter").trigger("liszt:updated");
	
	Request.ajax("/actions/get_public_ssh_key.php", {}, function(response) {
		if(typeof response !== "undefined") {
			$("#settings-public-ssh-key").html("");
			$("#settings-public-ssh-key").append('<pre class="prettyprint lang-html linenums" style="display: none;">' + response.public_ssh_key + '</pre>');
			prettyPrint();
			$("#settings-public-ssh-key").children("pre").slideDown(200);
		}
	});

	$("#default-ssh-port").numericOnly();
	
	setTimeout(function() {
		$("#settings-saved-alert").hide("slow");
	}, 3000);
});