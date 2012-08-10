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
	
	//Get settings
	$settings = null;
	$result = MySQLQueries::get_settings();
	$row = MySQLConnection::fetch_object($result);
	
	if(isset($row->data)) { 
		$row->data = json_decode($row->data);	
	}

	$settings = $row;
	$settings = Functions::format_dates($settings);
	
	$interpreters = array("shell", "bash", "perl", "python", "node.js");
	
	Header::set_title("Commando.io - Settings");
	Header::render(array("chosen", "code-pretty"));
	
	Navigation::render("settings");
?>   
    <div class="container">
           
      <h1 class="header">Settings</h1> 
      
	  <div class="row">
    	<div class="span12 well">
    		<?php if(isset($_GET['param1']) && $_GET['param1'] == "saved"): ?>     
		      	<div class="alert alert-success" id="settings-saved-alert">
		 			<strong>Settings saved successfully.</strong>
		      	</div>    
			<?php endif; ?>

			<form id="form-settings" class="well form-horizontal" method="post" action="/actions/edit_settings.php">
		    	 <?php if(isset($settings->modified)): ?>
		    	 	<div style="float: right;">
		    	 		<a class="btn disabled">Modified <?php $settings->modified ?></a>
		    	 	</div>
		    	 <?php endif; ?>
		    	 <fieldset>
			    	<legend>Commando.io Public SSH Key</legend>
			    	<div class="control-group span11" id="settings-public-ssh-key"><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div>
			     </fieldset>
		    	 <fieldset>
			    	<legend>Defaults</legend>
			    	<div class="control-group">
			        	<label class="control-label" for="default-ssh-username">Default SSH Username</label>
			        	<div class="controls">
			          		<input type="text" class="input-medium" id="default-ssh-username" name="default_ssh_username" value="<?php echo isset($settings->data->default_ssh_username) ? $settings->data->default_ssh_username: null; ?>" maxlength="100" />
			          		<p class="help-block">The default SSH username that is pre-filled when adding a new server.</p>
			        	</div>
			        </div>
			        <div class="control-group">
			        	<label class="control-label" for="default-ssh-port">Default SSH Port</label>
			        	<div class="controls">
			          		<input type="text" class="input-small" id="default-ssh-port" name="default_ssh_port" value="<?php echo isset($settings->data->default_ssh_port) ? $settings->data->default_ssh_port : null; ?>" maxlength="5" />
			          		<p class="help-block">The default SSH port that is pre-filled when adding a new server.</p>
			        	</div>
			        </div>
			        <div class="control-group">
			        	<label class="control-label" for="default-ssh-port">Default Interpreter</label>
			        	<div class="controls">
			          		<select name="default_interpreter" id="default-interpreter" class="span2" data-placeholder="">
								<?php foreach($interpreters as $interpreter): ?>
									<option value="<?php $interpreter ?>" <?php if(isset($settings->data->default_interpreter) && $interpreter === $settings->data->default_interpreter): ?>selected="selected" <?php endif; ?>><?php ucfirst($interpreter) ?></option>	
								<?php endforeach; ?>
							</select>
			          		<p class="help-block">The default interpreter that is pre-filled when adding a new recipe.</p>
			        	</div>
			        </div>
			     </fieldset>
			     <fieldset>
			     	<legend>Date/Time</legend>
			        <div class="control-group">
			        	<label class="control-label" for="timezone-offset">Timezone Offset</label>
			        	<div class="controls">
			        		<select name="timezone_offset" id="timezone-offset" class="span4" data-placeholder="" data-value="<?php echo isset($settings->data->timezone_offset) ? $settings->data->timezone_offset : "+00:00" ?>">
								<option value="-12:00">(GMT -12:00) Eniwetok, Kwajalein</option>
								<option value="-11:00">(GMT -11:00) Midway Island, Samoa</option>
								<option value="-10:00">(GMT -10:00) Hawaii</option>
								<option value="-09:50">(GMT -9:30) Taiohae</option>
								<option value="-09:00">(GMT -9:00) Alaska</option>
								<option value="-08:00">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
								<option value="-07:00">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
								<option value="-06:00">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
								<option value="-05:00">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
								<option value="-04:50">(GMT -4:30) Caracas</option>
								<option value="-04:00">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
								<option value="-03:50">(GMT -3:30) Newfoundland</option>
								<option value="-03:00">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
								<option value="-02:00">(GMT -2:00) Mid-Atlantic</option>
								<option value="-01:00">(GMT -1:00) Azores, Cape Verde Islands</option>
								<option value="+00:00" selected="selected">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
								<option value="+01:00">(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
								<option value="+02:00">(GMT +2:00) Kaliningrad, South Africa</option>
								<option value="+03:00">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
								<option value="+03:50">(GMT +3:30) Tehran</option>
								<option value="+04:00">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
								<option value="+04:50">(GMT +4:30) Kabul</option>
								<option value="+05:00">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
								<option value="+05:50">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
								<option value="+05:75">(GMT +5:45) Kathmandu, Pokhara</option>
								<option value="+06:00">(GMT +6:00) Almaty, Dhaka, Colombo</option>
								<option value="+06:50">(GMT +6:30) Yangon, Mandalay</option>
								<option value="+07:00">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
								<option value="+08:00">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
								<option value="+08:75">(GMT +8:45) Eucla</option>
								<option value="+09:00">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
								<option value="+09:50">(GMT +9:30) Adelaide, Darwin</option>
								<option value="+10:00">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
								<option value="+10:50">(GMT +10:30) Lord Howe Island</option>
								<option value="+11:00">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
								<option value="+11:50">(GMT +11:30) Norfolk Island</option>
								<option value="+12:00">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
								<option value="+12:75">(GMT +12:45) Chatham Islands</option>
								<option value="+13:00">(GMT +13:00) Apia, Nukualofa</option>
								<option value="+14:00">(GMT +14:00) Line Islands, Tokelau</option>
							</select>
							<p class="help-block">The timezone offset to apply to all dates and times.</p>
			        	</div>
			        </div>
			        <div class="control-group">
			        	<label class="control-label" for="timezone-daylight-savings">Is Daylight Savings</label>
			        	<div class="controls">
			        		<input type="checkbox" id="timezone-daylight-savings" name="timezone_daylight_savings" value="true" <?php echo isset($settings->data->timezone_daylight_savings) ? 'checked="checked"' : null ?> />
			        		<p class="help-block">Add one hour to the timezone offset in observance of daylight savings.</p>
			        	</div>
			        </div>
			        <div class="control-group">
						<div class="controls">
							<a class="btn btn-primary" onclick="validate_edit_settings();"><i class="icon-ok-sign icon-white"></i> Save Settings</a>
						</div>
			        </div>
				</fieldset>
	        </form>
	       	<div class="aes-key">
	        	<i class="icon-lock"></i> Settings are stored <a href="http://en.wikipedia.org/wiki/Advanced_Encryption_Standard" target="_blank">AES</a> encrypted.
	        </div>
		</div>
	  </div>
<?php
	Footer::render(array("chosen", "code-pretty", "settings"));
?>