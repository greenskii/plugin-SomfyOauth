<?php
/*
 * This file is part of the NextDom software (https://github.com/NextDom or http://nextdom.github.io).
 * Copyright (c) 2018 NextDom.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>


<form class="form-horizontal">
    <fieldset>
    <legend><i class="fa fa-key"></i>&nbsp; {{Configuration OAuth}}</legend>
    	<div class="form-group">
		    <label class="col-lg-4 control-label">{{OAuth Client ID}}</label>
		    <div class="col-lg-2">
		        <input class="configKey form-control" data-l1key="OAuthClientID" />
		    </div>
		</div>       	
    	<div class="form-group">
		    <label class="col-lg-4 control-label">{{OAuth Client Secret}}</label>
		    <div class="col-lg-2">
		        <input class="configKey form-control" data-l1key="OAuthClientSecret" />
		    </div>
		</div>
    	<div class="form-group">
		    <label class="col-lg-4 control-label">{{OAuth Verification Code}}</label>
		    <div class="col-lg-2">
		        <input class="configKey form-control" data-l1key="OAuthVerificationCode" />
		    </div>
		</div>
		<div class="form-group">
		  <label class="col-lg-4 control-label">{{OAuth URL de retour}}</label>
		  <div class="col-lg-2">
		    	<span><?php echo network::getNetworkAccess('external','proto:ip') . '/plugins/somfyoauth/desktop/modal/OauthReturn.php';?></span>
			</div>		
		</div>
      <div class="form-group">
	        <label class="col-lg-4 control-label">{{Lier le compte Somfy}}</label>
	        <div class="col-lg-2">
	        <!--a class="btn btn-default" id="bt_syncWithSomfy"><i class='fa fa-key'></i> {{Cliquez-ici pour lier votre compte avec Somfy}}</a-->
	        <a class="btn btn-default" id="bt_syncWithSomfyTab"><i class='fa fa-key'></i>{{Cliquez-ici pour lier votre compte avec Somfy}}</a>
	        </div>
	</div>    
    	<div class="form-group">
		    <label class="col-lg-4 control-label">{{OAuth Authorization Code}}</label>
		    <div class="col-lg-6">
		        <input class="configKey form-control" data-l1key="OAuthAuthorizationCode" readonly/>
		    </div>
		</div>		
    	<div class="form-group">
		    <label class="col-lg-4 control-label">{{OAuth Access Token}}</label>
		    <div class="col-lg-4">
		        <input class="configKey form-control" data-l1key="OAuthAccessToken" readonly/>
		    </div>
		</div>       	
    	<div class="form-group">
		    <label class="col-lg-4 control-label">{{OAuth Refresh Token}}</label>
		    <div class="col-lg-4">
		        <input class="configKey form-control" data-l1key="OAuthRefreshToken" readonly/>
		    </div>
		</div>
  </fieldset>
     <fieldset>
    <legend><i class="fa fa-download"></i>&nbsp; {{Gestion des équipements}}</legend>
      <div class="form-group">
	        <label class="col-lg-4 control-label">{{Equipements}}</label>
	        <div class="col-lg-2">
	        <!--a class="btn btn-default" id="bt_syncWithSomfy"><i class='fa fa-key'></i> {{Cliquez-ici pour lier votre compte avec Somfy}}</a-->
	        <a class="btn btn-default" id="bt_syncEQWithSomfy"><i class='fa fa-download'></i>{{Télécharger et synchroniser la liste de vos équipements Somfy}}</a>
	        </div>
		</div>
  </fieldset>
</form>


<script>
    $('#bt_syncWithSomfy').on('click', function () {
	//https://accounts.somfy.com/oauth/oauth/v2/auth?response_type=code&client_id=YOUR_CONSUMER_KEY&redirect_uri=https%3A%2F%2Fyour-domain.com%2Fsomewhere&state=YOUR_UNIQUE_VALUE&grant_type=authorization_code
		var destinationURL = "https://accounts.somfy.com/oauth/oauth/v2/auth?response_type=code&client_id=" + 
			$('input[data-l1key="OAuthClientID"]').val() + 
			"&redirect_uri=" + "<?php echo urlencode (network::getNetworkAccess('external','proto:ip')) . '/plugins/somfyoauth/desktop/modal/OauthReturn.php'; ?>" +	"&state=" + 
			$('input[data-l1key="OAuthVerificationCode"]').val() + 
			"&grant_type=" + 
			"authorization_code";
		console.log(destinationURL);
	    var wWidth = $(window).width();
	    var dWidth = wWidth * 0.8;
	    var wHeight = $(window).height();
	    var dHeight = wHeight * 0.8;
	
		var $dialog = $('<div id="modalSomfyOauth"></div>')
		               .html('<iframe style="border: 0px; " src="' + destinationURL + '" width="100%" height="100%"></iframe>')
		               .dialog({
		                   autoOpen: false,
		                   modal: true,
						   width: dWidth,
					       height: dHeight,
		                   title: "Somfy Oauth Connect"
		               });
		$dialog.dialog('open');
    });
    
    $('#bt_syncWithSomfyTab').on('click', function () {
		var destinationURL = "https://accounts.somfy.com/oauth/oauth/v2/auth?response_type=code&client_id=" + 
			$('input[data-l1key="OAuthClientID"]').val() + 
			"&redirect_uri=" + "<?php echo urlencode (network::getNetworkAccess('external','proto:ip')) . '/plugins/somfyoauth/desktop/modal/OauthReturn.php'; ?>" +	"&state=" + 
			$('input[data-l1key="OAuthVerificationCode"]').val() + 
			"&grant_type=" + 
			"authorization_code";
    	window.open(destinationURL);
		return false;
    });
    
    $('#bt_syncEQWithSomfy').on('click', function () {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/somfyoauth/core/ajax/somfyoauth.ajax.php", // url du fichier php
            data: {
                action: "syncEQWithSomfy",
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_alert').showAlert({message: '{{Synchronisation réussie}}', level: 'success'});
            }
        });
    });   
</script>

