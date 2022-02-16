# PostNL Address Check

This extension will help your visitors fill in their address.
- A quick note: install via OC extentions / installer.  
  The extension will only be listed in the opencart.com marketplace, not within the OpenCart 3.0 admin panel area. 
  This means you cannot find the extension via the Extensions > Marketplace Search from within the backend. Just use the Extensions > Installer, like in OC 2.0
- Do you wish to change settings after installation?  
  Login via /admin or /beheer, go to Extensions > Extensions > Modules and scroll to PostNL listings. Use the Edit button.

- Use your PostNL Business Account get your API-key. Apply this key in the extension settings to activate the underlying API. Read more at the [OpenCart marketplace](https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=43309).
- More info on plugins [here](https://www.postnl.nl/zakelijke-oplossingen/pakket-versturen/it-koppelingen-webwinkels/plug-ins/)

# Pre-requisites prior to installing the plugin
  1. The plugin supports these OpenCart versions - 2.0.x.x to 2.2.x.x, 2.3.x.x, 3.x.x.x and above. The plugin is compatible with the default Open Cart Themes OC2, OC3 plus Journal 2 and Journal 3.

  2. If you were previously using the PostNL Adrescheck Extension plugin with an older version, please follow the Uninstall Guides below to uninstall the existing plugin before attempting to install the new plugin:

  3. Ensure that your webserver uses PHP version 7.2 and above.

  5. Download the latest version of the plugin file, ```PostNL_Adrescheck-OCxxx.ocmod.zip```, where xxx is the OpenCart version you are using.
      - You can get the latest version of the plugin from :
        - [OpenCart marketplace](https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=43309)
        - [Github](https://github.com/postnl/opencart-extension)

      - Ensure that the plugin file ends with ```.ocmod.zip``` extension.

      - Ensure that you've downloaded the correct zip file for your OpenCart installation.

  6. Installation of the plugin is via the OpenCart Extension Installer for all OpenCart versions. For OpenCart 2.0.x to 2.2.x, the plugin uses OCMOD (OpenCart Modifications) as well.
      - For OpenCart 2.0.x to 2.2.x:
        - You need to either
          - Option 1: Enable FTP option.
            - Go to the admin panel of OpenCart and click on Settings.
            - Click on Edit button of your store.
            - Click on FTP tab and setup your FTP details.
          - Option 2: Install QuickFix: Extensions Installer issue when FTP support disabled.
            - [Download Link](https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=18892)
        - Installation of the plugin through manual upload of the plugin's files onto your web server is not recommended as the OCMOD script will not be installed correctly. You should only install the plugin through manual upload if you are able to install the OCMOD script correctly. Otherwise, the extension will not work correctly.
        - vQmod installation is NOT supported.

      - For OpenCart 2.3.x or OpenCart 3.x:
        - There is no additional setup required as the extension uses OpenCart Events instead of OCMOD.
        - Installation of the plugin through manual upload of the plugin's files onto your web server is supported.

  7. Our plugin uses the default OpenCart folder structure, i.e. admin, catalog and system.
      - If your folder structure is not the same **AND** you are performing the installation of the plugin through manual upload, you will need to change the directory names of the plugin accordingly or ensure that you are uploading into the correct directories.
      - If you are performing installation through the OpenCart Extension Installer, you do not need to take any actions.

# Plugin installation
  1. Download the PostNL Adrescheck plugin from:
      - [OpenCart Marketplace](https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=43309)
      - [Github latest release](https://github.com/postnl/opencart-extension)

  2. Install the PostNL Adrescheck plugin via the OpenCart Installer.
      - Installation instructions:
        - Go to your OpenCart store's admin panel, and click on:
          - Extensions -> Extension Installer. (For OpenCart 2.x)
          - Extensions -> Installer. (For OpenCart 3.x)

        - Click on the 'Upload' button and select the plugin zip file (Please ensure that the zip file ends with ```.ocmod.zip```). Click on the 'Continue' button if required.

        - Go to your OpenCart store's admin panel again, and click on:
          - Extensions -> Modules (For OpenCart 2.0.x to 2.2.x)
          - Extensions -> Extensions -> Choose 'Modules' from the Extension type dropdown list

        - Locate PostNL Adrescheck Extension and click on Install button.

        - (Additional Step) **Only for OpenCart 2.0.x to 2.2.x:**
          - Go to the admin panel of OpenCart and click on Extensions -> Modifications.
          - Click on the Refresh button.

        - Installation has been completed.


  3. Setup the permission rights for PostNL Adrescheck Extension if you encounter "Permission Denied".
      - Ensure you are on the latest version of the PostNL Adrescheck Extension plugin.
      - The PostNL Adrescheck Extension plugin will automatically enable the permission access for the default Administrator group. For other user groups, you will need to provide the permission access manually.
      - Follow the below steps to provide the permission access:
        - Go to the admin panel and click on Menu -> Settings -> Users -> User Groups. Locate your user group and click on the Edit button.
        - Locate ```extension/module/postnl_adrescheck``` for both Access and Modify permissions. Ensure the permissions are selected and click on Save button.
        - Access the PostNL Adrescheck Extension to verify that you are able to view the plugin.

# Delete the existing settings for PostNL Adrescheck
  1. Delete the existing settings.
      - You must already have completed the setup for PostNL Adrescheck Extension.
      - Go to the admin panel and:
        - Click on PostNL Adrescheck Extension (for all OpenCart versions)
        - Or, (for OpenCart 2.0.x to 2.2.x) click on Extensions -> Modules -> PostNL Adrescheck Extension
        - Or, (for OpenCart 2.3.x to 3.x.x) click on Extensions -> Extensions -> Choose 'Modules' from the Extension type dropdown list -> PostNL Adrescheck Extension
      - You should see an 'Uninstall' button at your PostNL Adrescheck Extension plugin page.
      - A pop up confirmation should appear. Click on the OK button to proceed with deletion.
  2. Once deleted successfully, you should see the page with 'Get Started' button.

# Uninstall the plugin

  - If you are using PostNL Adrescheck Extension v1.0.0 and above:

      - For OpenCart 2.0.x to 2.2.x:

        1. You must already have installed the PostNL Adrescheck plugin on your OpenCart website.

        2. Go to the admin panel of your OpenCart store and click on Extensions -> Modules -> PostNL Adrescheck Extension. Click on 'Uninstall'.

        3. Then, proceed to Extensions -> Modifications, and locate 'PostNL Adrescheck Extension'.

        4. Click on the 'Uninstall' button for 'PostNL Adrescheck Extension'.

        5. Click on the 'Refresh' button to refresh the existing plugins on your OpenCart website.

        6. Do note that if you want to remove the plugin files from your web server, you will have to remove them manually.

      - For OpenCart 2.3.x:

        1. You must already have installed the PostNL Adrescheck plugin on your OpenCart website.

        2. Go to the admin panel of your OpenCart website and click on Extensions -> Extensions -> Modules -> PostNL Adrescheck Extension. Click on 'Uninstall'.

        3. Do note that if you want to remove the plugin files from your web server, you will have to remove them manually.

      - For OpenCart 3 and above:

        1. You must already have installed the PostNL Adrescheck plugin on your OpenCart website.

        2. Go to the admin panel of your OpenCart website and click on Extensions -> Extensions -> Modules -> PostNL Adrescheck Extension. Click on 'Uninstall'.

        3. To remove the plugin files from your web server, proceed to Extensions -> Installer, and locate the ```.ocmod.zip``` file you uploaded previously to install the PostNL Adrescheck Extension plugin. Then, click on the 'Uninstall' button.

