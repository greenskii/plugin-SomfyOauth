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

  	public static function executeQuery($url, $params = array(), $includeAuthentificationHeaders = true) {
		try {
			$ch = curl_init();
			
			//Initiate cURL.
			$defaults = array( 
				CURLOPT_HEADER => 0, 
				CURLOPT_URL => $url, 
				CURLOPT_FRESH_CONNECT => 1, 
				CURLOPT_RETURNTRANSFER => 1, 
				CURLOPT_FORBID_REUSE => 1, 
				CURLOPT_TIMEOUT => 4
			); 
			
			curl_setopt_array($ch, ($defaults)); 
			
			if ($includeAuthentificationHeaders == true) {
				// on récupère les codes et clés
		    	$accessToken = config::byKey('OAuthAccessToken', 'somfyoauth');
				$headers = array();
				$headers[] = "Content-Type: application/json";
				$headers[] = "Authorization: Bearer " . $accessToken . "";
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			} 
			 
			if ( ! $result = curl_exec($ch)) { 
				log::add('somfyoauth', 'debug', 'erreur résultat');
				trigger_error(curl_error($ch)); 
			} 

			curl_close ($ch);  
			log::add('somfyoauth', 'debug', 'Fin de lexecution de la requete sur l API SOMFY. Résultat : ');
			log::add('somfyoauth', 'debug', print_r($result, true));
			$result = json_decode($result, TRUE);
			if (isset($result) && ! isset($result['fault'])) {
				return $result;
			} else if ($result['fault']['detail']['errorcode'] == 'oauth.v2.InvalidAccessToken') {
				trigger_error('Athentification error'); 
			} else if ($result['fault']['detail']['errorcode'] == 'keymanagement.service.access_token_expired') {
				self::getSomfyToken(true);
				return self::executeQuery($url, $params, $includeAuthentificationHeaders);
			}
		}
		catch (Exception $e) {
			log::add('somfyoauth', 'debug', $e->getMessage());
			var_dump($e->getMessage());
		}
    }
    
	public static function createEqFromSomfy($deviceParams) {
		$eqLogic = new somfyoauth();
		$eqLogic->setEqType_name('somfyoauth');
		$eqLogic->setIsEnable(1);
		if ( isset($deviceParams['name']) ) {
			$eqLogic->setName($deviceParams['name']);
		} else {
			$eqLogic->setName($deviceParams['type']);
		}
		$eqLogic->setLogicalId($deviceParams['id']);
		$eqLogic->setCategory('heating', 1);
		$eqLogic->setIsVisible(1);
		$eqLogic->save();
		return $eqLogic;
	}
	
	public static function createCapabilityCommand($eqLogic, $capability) {
		
		$actionCommand = $eqLogic->getCmd('action', $capability['name']);
		if (!is_object($actionCommand)) {
			$actionCommand = new somfyoauthCmd();
			$actionCommand->setName(__(lcfirst($capability['name']), __FILE__));
		}
		$actionCommand->setLogicalId($capability['name']);
		log::add('somfyoauth', 'debug', 'Rattachement de la commande Action à ' . $eqLogic->getId());

		$actionCommand->setEqLogic_id($eqLogic->getId());
		$actionCommand->setType('action');
		if (isset($capability['parameters']) && $capability['parameters']['name'] == 'position' && $capability['parameters']['type'] == 'integer') {
			$actionCommand->setSubType('slider');
		} else {
			$actionCommand->setSubType('other');
		}
		$actionCommand->save();	 
		log::add('somfyoauth', 'debug', 'Création de la commande Action' . $capability['name']);
		
	}
	
	public static function createStateCommand($eqLogic, $state) {

		// création de la commande position
		$infoCommand = $eqLogic->getCmd(null, $state['name']);
		if (!is_object($infoCommand)) {
			$infoCommand = new somfyoauthCmd();
			$infoCommand->setName(__(lcfirst($state['name']), __FILE__));
		}
		$infoCommand->setLogicalId('positioninfo');
		$infoCommand->setEqLogic_id($eqLogic->getId());
		$infoCommand->setType('info');
		$infoCommand->setSubType('string');
		$infoCommand->save();	 
		log::add('somfyoauth', 'info', 'Création de la commande info ' . $state['name']);
	}
   
	public static function syncEQWithSomfy() {
		
		$urlSites = "https://api.somfy.com/api/v1/site";
		$sites = self::executeQuery($urlSites);

		foreach ($sites as $site) {
			log::add('somfyoauth', 'debug', print_r($array, true));
			$siteId = $site['id']; 
			$label = $site['label'];
			log::add('somfyoauth', 'debug', 'Site found : Id  ' . $siteId . ' - Label : ' . $label);

			$urlDevices = "https://api.somfy.com/api/v1/site/" . $siteId . "/device";
			$devices = self::executeQuery($urlDevices);
			log::add('somfyoauth', 'debug', 'Retour avec la liste des devices');
			
			foreach ($devices as $device) {
				log::add('somfyoauth', 'debug', 'Traitement device : Id  ' . $device['id']);

				$logicId = $device['id'];
				$eqLogic = eqLogic::byLogicalId($logicId, 'somfyoauth');
				if (!is_object($eqLogic)) {
					$eqLogic = self::createEqFromSomfy($device);
					foreach($device['capabilities'] as $capability) {
						self::createCapabilityCommand ($eqLogic, $capability);
					}
					foreach($device['states'] as $state) {
						self::createStateCommand ($eqLogic, $state);
					}
				}
				log::add('somfyoauth', 'debug', 'Fin Traitement device : Id  ' . $device['id']);
	      	}
		}
	}
  
	public static function getSomfyToken($refresh = false) {
		
		try {
			// on récupère les codes et clés
			$oAuthClientID = config::byKey('OAuthClientID', 'somfyoauth');
			$oAuthClientSecret = config::byKey('OAuthClientSecret', 'somfyoauth');
			
			if ($refresh == true) {
				$oAuthRefreshToken = config::byKey('OAuthRefreshToken', 'somfyoauth');
				$url = "https://accounts.somfy.com/oauth/oauth/v2/token?"
				. "client_id=" . $oAuthClientID
			    . "&client_secret=" . $oAuthClientSecret
			    . "&grant_type=refresh_token&refresh_token=" . $oAuthRefreshToken;
			} else {
				$oAuthAuthorizationCode = config::byKey('OAuthAuthorizationCode', 'somfyoauth');
				$url = "https://accounts.somfy.com/oauth/oauth/v2/token?"
				. "client_id=" . $oAuthClientID
			    . "&client_secret=" . $oAuthClientSecret
			    . "&grant_type=authorization_code&code=" . $oAuthAuthorizationCode 
			    . "&redirect_uri=" . urlencode (network::getNetworkAccess('external','proto:ip') . '/plugins/somfyoauth/desktop/modal/OauthReturn.php');
			}
	
			$array = self::executeQuery($url, [], false);

          	$accessToken = $array['access_token'];
			$refreshToken = $array['refresh_token'];
			config::save("OAuthAccessToken", $accessToken, "somfyoauth");
			config::save("OAuthRefreshToken", $refreshToken, "somfyoauth");
	
		} catch (Exception $e) {
			
			log::add('somfyoauth', 'debug', $e->getMessage());
			var_dump($e->getMessage());
		}
	}

}

class somfyoauthCmd extends cmd {
	
    public function execute($_options = array()) {
	   	$eqLogic = $this->getEqLogic();
	   	$eqId = $eqLogic->getLogicalId();
 
    }

}

?>
