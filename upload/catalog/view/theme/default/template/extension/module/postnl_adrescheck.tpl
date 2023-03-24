<?php /*
 * Copyright (c) De Webmakers
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */ ?>
<?php if(isset($module_postnl_adrescheck_status) && $module_postnl_adrescheck_status == 1) { ?>
<script type="text/javascript">
var postnl_adrescheck_data = JSON.parse('<?php echo $module_postnl_adrescheck_json ?>');
(function() {
    function postnl_loadScript(url, callback) {
        var script = document.createElement("script");
        script.type = "text/javascript";
        if(callback) script.onload = callback;
        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);
    }
    function postnl_loadStyle(url) {
        var css = document.createElement("link");
        css.setAttribute("rel", "stylesheet");
        css.setAttribute("type", "text/css");
        css.setAttribute("href", url);
        document.getElementsByTagName("head")[0].appendChild(css);
    }
    postnl_loadStyle("catalog/view/theme/css/postnl_adrescheck/postnl_adrescheck.css");
    postnl_loadScript("catalog/view/javascript/postnl_adrescheck/postnl_adrescheck.js");
})();
</script>
<?php }
