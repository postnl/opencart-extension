<!-- Copyright (c) De Webmakers -->
<!-- All rights reserved. -->

<!-- This source code is licensed under the license found in the -->
<!-- LICENSE file in the root directory of this source tree. -->
<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-postnl-adrescheck" data-toggle="tooltip" title="<?php echo $button_save ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel ?>" data-toggle="tooltip" title="<?php echo $button_cancel ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?php echo $heading_title ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href'] ?>"><?php echo $breadcrumb['text'] ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <?php if ($error_warning) { ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?php echo $error_warning ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($success_message) { ?>
            <div class="alert alert-success">
                <i class="fa fa-exclamation-circle"></i> <?php echo $success_message ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action ?>" method="post" id="form-postnl-adrescheck" class="form-horizontal">

                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <h3><?php echo $text_heading ?></h3>
                            <?php echo $text_body ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="module_postnl_adrescheck_status"><?php echo $label_api ?></label>
                        <div class="col-sm-10">
                            <select name="module_postnl_adrescheck_api_type" id="module_postnl_adrescheck_api_type" class="form-control">
                                <option value="checkout_postalcode_check" selected="selected"><?php echo $option_checkout_postalcode_check ?></option>
                            </select>
                            <span class="help-block"><?php echo $text_api_type ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="module_postnl_adrescheck_api_key"><?php echo $label_api_key ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="module_postnl_adrescheck_api_key" value="<?php echo $module_postnl_adrescheck_api_key ?>"
                                placeholder="<?php echo $label_api_key ?>" id="module_postnl_adrescheck_api_key" class="form-control" />
                            <span class="help-block"><?php echo $text_api_key ?></span>
                        </div>
                    </div>

                    <?php if ($module_postnl_adrescheck_api_key) { ?>

                        <?/*<div class="form-group form-group-onoff">
                            <div class="col-sm-2 control-label"><div class="checkbox">
                                <input type="radio" name="module_postnl_adrescheck_type" value="zipcode_housenumber" id="zipcode_housenumber"<?php if ($module_postnl_adrescheck_zipcode_housenumber) { ?> checked<?php } ?> disabled>
                            </div></div>
                            <div class="col-sm-10">
                                <label for="zipcode_housenumber">
                                    <?php echo $label_zipcode_housenumber ?>
                                    <span class="help-block"><?php echo $help_zipcode_housenumber ?></span>
                                </label>
                            </div>
                            <div class="col-sm-12">&nbsp;</div>
                            <div class="col-sm-2 control-label"><div class="checkbox">
                                <input type="radio" name="module_postnl_adrescheck_type" value="autocomplete" id="autocomplete"<?php if ($module_postnl_adrescheck_autocomplete) { ?> checked<?php } ?> disabled>
                            </div></div>
                            <div class="col-sm-10">
                                <label for="autocomplete">
                                    <?php echo $label_autocomplete ?> <sup>beschikbaar in versie 2</sup>
                                    <span class="help-block"><?php echo $help_autocomplete ?></span>
                                </label>
                            </div>
                        </div>*/?>
                        <input type="hidden" name="module_postnl_adrescheck_type" value="zipcode_housenumber">
                        <div class="form-group form-group-onoff">
                            <div class="col-sm-2 control-label"><div class="checkbox">
                                <input type="checkbox" name="module_postnl_adrescheck_manual_input" id="manual_input"<?php if ($module_postnl_adrescheck_manual_input) { ?> checked<?php } ?>>
                            </div></div>
                            <div class="col-sm-10">
                                <label for="manual_input">
                                    <?php echo $label_manual_input ?>
                                    <span class="help-block"><?php echo $help_manual_input ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group form-group-onoff">
                            <div class="col-sm-2 control-label"><div class="checkbox">
                                <input type="checkbox" name="module_postnl_adrescheck_showlabel" id="showlabel"<?php if ($module_postnl_adrescheck_showlabel) { ?> checked<?php } ?>>
                            </div></div>
                            <div class="col-sm-10">
                                <label for="showlabel">
                                    <?php echo $label_showlabel ?>
                                    <span class="help-block"><?php echo $help_showlabel ?>
                                    <img src="<?php echo $label_img ?>" width="250" class="img-responsive" /></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="timeout"><?php echo $label_timeout ?></label>
                            <div class="col-sm-10">
                                <select name="module_postnl_adrescheck_timeout" id="timeout" class="form-control">
                                    <option value="5"<?php if ($module_postnl_adrescheck_timeout == 5) { ?>selected="selected"<?php } ?>>5 seconden</option>
                                    <option value="10"<?php if ($module_postnl_adrescheck_timeout == 10) { ?>selected="selected"<?php } ?>>10 seconden</option>
                                </select>
                                <span class="help-block"><?php echo $help_timeout ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-onoff">
                            <div class="col-sm-2 control-label"><div class="checkbox">
                                <input type="checkbox" name="module_postnl_adrescheck_debug" id="debug"<?php if ($module_postnl_adrescheck_debug) { ?> checked<?php } ?>>
                            </div></div>
                            <div class="col-sm-10">
                                <label for="debug">
                                    <?php echo $label_debug ?>
                                    <span class="help-block"><?php echo $help_debug ?></span>
                                </label>
                            </div>
                        </div>

                    <?php } ?>
                </form>

                <hr size="1">
                <p class="text-right">
                    <strong>PostNL Adrescheck</strong> <small><?php echo $text_plugin_version ?></small><br>
                    <small><?php echo $text_manual_docs ?></small><br>
                    <small><?php echo $text_terms ?></small><br>
                </p>

            </div>
        </div>
    </div>
</div>
<style>
    .form-group-onoff .control-label { padding-top:0; }
    .form-group-onoff label { display:block; margin-bottom: 0; }
    .form-group-onoff .checkbox input[type="checkbox"] { margin-left:0; }
    #form-postnl-adrescheck .help-block { display:block; margin-top: 0; margin-bottom: 0; font-size:11px; font-weight:normal; }
</style>
<?php echo $footer; ?>