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
require_once dirname(__FILE__) . '/../../core/class/somfyoauth.class.php';


try {
	log::add('somfyoauth', 'debug', 'Début oAuth');
	
	if (isset($_GET['state'])) {
	
		log::add('somfyoauth', 'debug', 'Communication du code par Somfy '. print_r($_GET, true));
		$authorizationCode = $_GET['code'];
		$state = $_GET['state'];
	
		if ($state == jeedom::getApiKey('somfyoauth')) {
			
			log::add('somfyoauth', 'debug', 'Code de verification valide');
			
			config::save("OAuthAuthorizationCode", $authorizationCode, "somfyoauth");
			echo "<script>
			$('#modalSomfyOauth', window.parent.document).dialog('close');
			</script>
			Vous pouvez fermer la fenêtre.
			";
			
			somfyoauth::getSomfyToken () ;

		log::add('somfyoauth', 'debug', 'Fin oAuth');
		}
	}
} catch (Exception $e) {
    var_dump($e->getMessage());
}

