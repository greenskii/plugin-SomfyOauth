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

 $('#bt_syncWithSomfy').on('click', function () {
    syncWithSomfy();
});


function syncWithSomfy() {

//https://accounts.somfy.com/oauth/oauth/v2/auth?response_type=code&client_id=YOUR_CONSUMER_KEY&redirect_uri=https%3A%2F%2Fyour-domain.com%2Fsomewhere&state=YOUR_UNIQUE_VALUE&grant_type=authorization_code
	var page = "https://accounts.somfy.com/oauth/oauth/v2/auth?response_type=code&client_id=" +	"YOUR_CONSUMER_KEY" 
	+ "&redirect_uri=" + "REDIRECT_URL" + "&state=" + "YOUR_UNIQUE_VALUE" + "&grant_type=" + "authorization_code";
	
	var $dialog = $('<div></div>')
	               .html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
	               .dialog({
	                   autoOpen: false,
	                   modal: true,
	                   height: 625,
	                   width: 500,
	                   title: "Some title"
	               });
	$dialog.dialog('open');
}