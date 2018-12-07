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
	public static function syncWithGoogle() {
		
		// on récupère la clé API
		$apiKey = config::byKey('homeGraphAPIKey', 'apiai');

		if (!isset($apiKey)) return 0;
		
		log::add('apiai', 'debug', 'Lancement de la resynchro avec Google');

		//API Url
		$url = 'https://homegraph.googleapis.com/v1/devices:requestSync?key=' . $apiKey;
		
		//Initiate cURL.
		$ch = curl_init($url);

		$data = array('agent_user_id' => 'jeedom-apiaiplugin-' . jeedom::getApiKey('apiai'));

		//Encode the array into JSON.
		$jsonDataEncoded = json_encode($data);
		//Tell cURL that we want to send a POST request.
		curl_setopt($ch, CURLOPT_POST, 1);
		//Attach our encoded JSON string to the POST fields.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		//Set the content type to application/json
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	    $result_req = curl_exec($ch);		
		
		if (isset($result_req)) {
			log::add('apiai', 'debug', 'Résultat de la synchro : ' .print_r($result_req, true));
			$json = json_decode($result_req, true);
			if(isset($json['error'])) {
				log::add('apiai', 'debug', 'Error ' . $json['error']['code'] . ': ' . $json['error']['message']);
				return 0;
			} else return 1;
		}
		log::add('apiai', 'debug', 'Erreur inconnue');
		return 0;
	}
}

class apiaiCmd extends cmd {

}

?>
