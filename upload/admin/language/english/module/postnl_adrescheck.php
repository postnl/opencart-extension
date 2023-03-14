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
$_['text_heading']              = 'Welcome at the PostNL extension';
$_['text_body']                 = '<p>Please select the prefered Address Validation.
Before using the OpenCart extension, you need to register your PostNL API-key.</p>
<p>This service can be used free of charge for business customers of PostNL Parcels. You use the API key of the Checkout Postcode Check or the Address Check Nederland.
<a href="%link_api%" target="_blank">Log in to PostNL</a> to find and activate the API key.
Or apply <a href="%link_products%" target="_blank">here</a> for the free Address Check Nederland and receive the activation link by email within 10 minutes.</p>
<p>Are you not a PostNL business parcel customer, but are you interested to use the plug-in?
In that case apply for a bundle <a href="%link_products%" target="_blank">here</a> or browse all our products and subscriptions first.';
$_['text_api_type']             = 'Select the API you want to use';
$_['text_api_key']              = 'Place your API-key here';

$_['text_upgrade_message']      = 'A newer version of the PostNL Address Validation Extension is available. To download it, go to <a href="%link_extension%" target="_blank">OpenCart marketplace</a>.';
$_['text_plugin_version']       = 'Plugin Version %s';
$_['text_manual_docs']          = '<a href="%link_manual_docs%" target="_blank">Installation Instructions</a>';
$_['text_terms']                = '<a href="%link_terms%" target="_blank">Terms and Conditions</a>';

$_['label_api']                 = 'API';
$_['option_checkout_postalcode_check'] = 'Checkout Postalcode Check';
$_['label_api_key']             = 'API Key';
$_['label_status']              = 'Status';
$_['label_zipcode_housenumber'] = 'Zipcode and house number';
$_['help_zipcode_housenumber']  = 'Customers fill out zipcode and house number to help and prefill other address components.';
$_['label_manual_input']        = 'Manual input';
$_['help_manual_input']         = 'Allow for manual input in case of wrong suggestion.';
$_['label_autocomplete']        = 'Autocomplete';
$_['help_autocomplete']         = 'When the user starts typing street name, the extension offers a list with suggestions to choose from. The user can pick one, instead of typing all address data.';
$_['label_showlabel']           = 'Show PostNL Address Validation label';
$_['help_showlabel']            = 'Example: ';
$_['label_timeout']             = 'Time out';
$_['help_timeout']              = 'When there is no response within the set time, the user can fill out the address manually.';
$_['label_debug']               = 'Debug frontend';
$_['help_debug']                = 'Check for debugging and showing errors.';

// Error
$_['error_api_key']             = 'Api key invalid';
$_['error_permission']          = 'Warning: You do not have permission to modify PostNL Address Validation Extension!';
$_['error_maintenance_mode']    = 'Warning: We have detected that your OpenCart store is in maintenance mode. To disable maintenance mode, access Settings -> Store -> Server tab, and select No for the Maintenance Mode option.';
