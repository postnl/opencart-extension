<?php
/**
 * Copyright (c) De Webmakers
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

// Heading
$_['heading_title']             = 'PostNL Address Validation';

// Text
$_['text_extension']            = 'Extensions';
$_['text_success']              = 'Success: You have modified module PostNL Address Validation!';
$_['text_edit']                 = 'Edit PostNL Address Validation Extension module';
$_['text_heading']              = 'Welkom bij de plug-in van PostNL';
$_['text_body']                 = '<p>Kies de adrescheck die je wilt gebruiken.
    Om gebruik te kunnen maken van de  adrescheck API en de plug in te activeren hebben we je PostNL API key nodig.</p>
<p>De CPC is gratis voor Zakelijke PostNL klanten. Wil je meer? Bekijk dan <a href="%link_products%" target="_blank">hier</a> al onze producten en abonnementen.</p>
<p>Of <a href="%link_api%" target="_blank">log in</a> met je PostNL Zakelijk account om jouw API key en abonnementen op te zoeken.</p>
';
$_['text_api_type']             = 'Kies de API die je wilt gebruiken';
$_['text_api_key']              = 'Vul hier jouw API key in';

$_['text_upgrade_message']      = 'A newer version of the PostNL Address Validation Extension plugin is available. To download it, go to <a href="%link_extension%" target="_blank">OpenCart marketplace</a>.';
$_['text_plugin_version']       = 'Plugin Version %s';
$_['text_manual_docs']          = '<a href="%link_manual_docs%" target="_blank">Handleiding en documentatie</a>';
$_['text_terms']                = '<a href="%link_terms%" target="_blank">Algemene voorwaarden</a>';

$_['label_api']                 = 'API';
$_['option_checkout_postalcode_check'] = 'Checkout Postalcode Check';
$_['label_api_key']             = 'API Key';
$_['label_status']              = 'Status';
$_['label_zipcode_housenumber'] = 'Zipcode and house number';
$_['help_zipcode_housenumber']  = 'Klant voert postcode en huisnummer in en het adres wordt ingevuld.';
$_['label_manual_input']        = 'Handmatig invoeren';
$_['help_manual_input']         = 'Handmatige invoer indien suggestie niet correct is.';
$_['label_autocomplete']        = 'Auto complete';
$_['help_autocomplete']         = 'Indien klant straatnaam begint te typen tonen we direct een lijst met opties waar de klant uit kan kiezen. Klant kan optie kiezen en hoeft dan niet verder te typen.';
$_['label_showlabel']           = 'Toon PostNL Address Validation label';
$_['help_showlabel']            = 'Voorbeeld: ';
$_['label_timeout']             = 'Time out';
$_['help_timeout']              = 'Indien er geen respons is binnen de opgegeven time out dan kan de gebruiker zelf het adres invoeren.';
$_['label_debug']               = 'Debug frontend';
$_['help_debug']                = 'Vink aan als de plugin niet goed werkt om foutmeldingen te zien';

// Error
$_['error_api_key']             = 'Api key invalid';
$_['error_permission']          = 'Warning: You do not have permission to modify PostNL Address Validation Extension!';
$_['error_maintenance_mode']    = 'Warning: We have detected that your OpenCart store is in maintenance mode. To disable maintenance mode, access Settings -> Store -> Server tab, and select No for the Maintenance Mode option.';
