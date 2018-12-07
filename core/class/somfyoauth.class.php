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
require_once dirname(__FILE__) . '/../../../gsh/core/class/gsh.class.php';



class apiai extends eqLogic {
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
			$oAuthClientID = config::byKey('OAuthClientID', 'somfyoauth');
			$oAuthClientID = config::byKey('OAuthClientID', 'somfyoauth');
	
			$url = "https://accounts.somfy.com/oauth/oauth/v2/token?"
				. "client_id=" . config::byKey("OAuthVerificationCode", "OAuthClientID")
			    . "&client_secret=" . config::byKey("OAuthVerificationCode", "OAuthClientSecret")
			    . "&grant_type=authorization_code&code=" . $authorizationCode 
			    . "&redirect_uri=" . urlencode (network::getNetworkAccess('external','proto:ip'));
	
	
			log::add('somfyoauth', 'debug', 'Contacting ' . print_r($url, true) .'...');
	
			//Initiate cURL.
			$ch = curl_init($url);
	
		    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
		
		    $data = curl_exec($ch);
		    curl_close($ch);
	
			$array = json_decode($data, TRUE);
			
			log::add('somfyoauth', 'debug', print_r($json, true));
	
		} catch (Exception $e) {
			var_dump($e->getMessage());
		}
			    
	}
}

class apiaiCmd extends cmd {

}

?>
