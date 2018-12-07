<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */


require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';


try {
	log::add('somfyoauth', 'debug', 'Début oAuth');
	
	
//	https://your-domain.com/somewhere?code=CODE_GENERATED_BY_SOMFY&state=YOUR_UNIQUE_VALUE

	if (isset($_GET['state'])) {
	
		log::add('somfyoauth', 'debug', 'Communication du code par Somfy '. print_r($_GET, true));
		$authorizationCode = $_GET['code'];
		$state = $_GET['state'];
	
		if ($state == config::byKey("OAuthVerificationCode", "somfyoauth")) {
			
			log::add('somfyoauth', 'debug', 'Code de verification valide');
			
			config::save("OAuthAuthorizationCode", $authorizationCode, "somfyoauth");
			echo "<script>
			$('#modalSomfyOauth', window.parent.document).dialog('close');
			</script>
			Vous pouvez fermer la fenêtre.
			";


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


		} else {
			 throw new Exception('Not Authorized!');
		}
		    
	}
	log::add('somfyoauth', 'debug', 'Fin oAuth');
 
} catch (Exception $e) {
    var_dump($e->getMessage());
}