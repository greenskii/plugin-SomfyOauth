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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


class somfyoauth extends eqLogic {
	/*     * *************************Attributs****************************** */

	/*
	**
	** This function resync all EQ with Google HomeGraph
	** Doing so delete every EQ room assignments made in Google Home app
	**
    */	
	public static function getSomfyToken() {
		
		try {
			// on récupère les codes et clés
			$oAuthClientID = config::byKey('OAuthClientID', 'somfyoauth');
			$oAuthClientSecret = config::byKey('OAuthClientSecret', 'somfyoauth');
			$oAuthAuthorizationCode = config::byKey('OAuthAuthorizationCode', 'somfyoauth');

			$url = "https://accounts.somfy.com/oauth/oauth/v2/token?"
				. "client_id=" . $oAuthClientID
			    . "&client_secret=" . $oAuthClientSecret
			    . "&grant_type=authorization_code&code=" . $oAuthAuthorizationCode 
			    . "&redirect_uri=" . urlencode (network::getNetworkAccess('external','proto:ip') . '/plugins/somfyoauth/desktop/modal/OauthReturn.php');
	
	
			log::add('somfyoauth', 'debug', 'Contacting ' . print_r($url, true) .'...');
	
			//Initiate cURL.
			$ch = curl_init($url);

		
		    $data = curl_exec($ch);
		    curl_close($ch);
			$array = json_decode($data, TRUE);
			
			log::add('somfyoauth', 'debug', 'fin appel URL');

			log::add('somfyoauth', 'debug', print_r($array, true));
	
		} catch (Exception $e) {
			var_dump($e->getMessage());
		}
			    
	}
}

class somfyoauthCmd extends cmd {

}

?>
