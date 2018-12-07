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

 
require_once __DIR__ . '/../../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}

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

		} else {
			 throw new Exception('Not Authorized!');
		}
		    
	}
	log::add('somfyoauth', 'debug', 'Fin oAuth');
 
} catch (Exception $e) {
    var_dump($e->getMessage());
}
