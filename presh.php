<?php

/**
 * @file
 * presh is a command line interface for Prestashop e-commerce sites
 *
 * @requires PHP CLI 5.3.0, or newer.
 */

class Presh {

  /**
  * Constructor
  */
  public function __construct() {
    define('_PS_ROOT_DIR_', getcwd());
    define('_PRESH_DIR_', dirname(__FILE__));
    require_once(_PS_ROOT_DIR_ . '/config/config.inc.php');
    require_once(_PS_ROOT_DIR_ . '/classes/Configuration.php');
    require_once(_PS_ROOT_DIR_ . '/classes/Tools.php');
    require_once(_PRESH_DIR_ . '/utils.php');
   }

  /**
  * Enables or disables the front of the shop
  *
  * @param string $status status to set
  */
  public function set_maintenance_status($status) {
    $this->update_global_value('PS_SHOP_ENABLE', $status);
  }

  /**
  * Toggles the maintenance status of the shop
  *
  */
  public function toggle_maintenance_status() {
    $current_status = $this->get_global_value('PS_SHOP_ENABLE');
    $this->update_global_value('PS_SHOP_ENABLE', !$current_status);
  }

  /**
  * Enables or disables the demo mode on the Dashboard
  *
  * @param string $status status to set
  */
  public function set_demo_mode($status) {
    $this->update_global_value('PS_DASHBOARD_SIMULATION', $status);
  }

  /**
  * Toggles the demo mode on the Dashboard
  *
  */
  public function toggle_demo_mode() {
    $current_status = $this->get_global_value('PS_DASHBOARD_SIMULATION');
    $this->update_global_value('PS_DASHBOARD_SIMULATION', !$current_status);
  }

  /**
  * Toggles the Friendly URLs setting
  *
  */
  public function toggle_friendly_urls() {
    $current_status = $this->get_global_value('PS_REWRITING_SETTINGS');
    $this->update_global_value('PS_REWRITING_SETTINGS', !$current_status);
  }

  /**
  * Enables or disables the Friendly URLs setting
  *
  * @param string $status status to set
  */
  public function set_friendly_urls($status) {
    $this->update_global_value('PS_REWRITING_SETTINGS', $status);
  }

  /**
  * Updates a global configuration variable, given its key name and new value
  *
  * @param string $key name of the variable to globally update
  * @param string $value new value to globally set on the given variable 
  * @return bool true when successfully updated, false otherwise
  */
  public function update_global_value($key, $value) {
    return Configuration::updateGlobalValue($key, $value);
  }

  /**
  * Gets a global configuration variable, given its key name
  *
  * @param string $key name of the variable to globally get
  * @return string value of the given key
  */
  public function get_global_value($key) {
    return Configuration::getGlobalValue($key);
  }
  
  /**
  * Installs a module given its name. The module has to be present on Prestashop
  * modules directory
  *
  * @param string $name name of the module to install
  * @return bool true when successfully installed, false otherwise
  */
  public function install_module($name) {
    if (!Module::getInstanceByName($name)) {
      $module_instance = Module::getInstanceByName($name);
      return $module_instance->install();
    }
    return false;
  }

  /**
  * Downloads a module given its url to the Prestashop modules directory.
  * The downloaded file is expected to be in zip format.
  * It is also expected the uncompressed file will contain a module as first directory.
  *
  * @param string $url url to download the module from
  * @return bool true when successfully downloaded, false otherwise
  */
  public function download_foreign_module($url) {
    # TODO: some servers, like www.eolia.o2switch.net for newsletteradmin module
    # require the User Agent header, so something like curl will be needed
    # to download files from them
    $file_handle = Utils::write_to_temp_file(Tools::file_get_contents($url));
    $file_path = Utils::get_path_of_file_handle($file_handle);
    $file_type = Utils::get_file_magic_type($file_path);
    switch ($file_type) {
      case "application/zip":
        return Tools::ZipExtract($file_path, _PS_ROOT_DIR_ . '/modules/');
        break;
      default:
        return false;
    }
  }

  /**
  * Updates as many modules as possible from the Prestashop modules directory.
  *
  */
  public function update_modules() {
    $lang = $this->get_global_value('PS_LOCALE_LANGUAGE');
    $country = $this->get_global_value('PS_LOCALE_COUNTRY');
    $xml_file = _PS_ROOT_DIR_ . '/config/xml/modules_native_addons.xml';
    $xml_content = Tools::file_get_contents($xml_file);
    $xml_tree = @simplexml_load_string($xml_content, null, LIBXML_NOCDATA);
    $modules_to_update = array();
    $modules_on_disk = Module::getModulesOnDisk(true, false, 1);
    foreach($modules_on_disk as $module) {
      if ($module->installed != true)
        continue;
      Module::initUpgradeModule($module);
      $module_instance = Module::getInstanceByName($module->name);
      if (Module::needUpgrade($module_instance) == true) {
        $module_to_update[$module->name] = null;
        foreach($xml_tree->module as $modaddons) {
          if($module->name == $modaddons->name)
            $modules_to_update[$module->name]['id'] = $modaddons->id;
        }
      }
    }
    foreach($modules_to_update as $k => $module) {
      $file_handle = Utils::write_to_temp_file(Tools::addonsRequest('module',
        array('iso_lang' => $lang, 'iso_code' => $country,
        'id_module' => $module['id'])));
      $file_path = Utils::get_path_of_file_handle($file_handle);
      $file_type = Utils::get_file_magic_type($file_path);
      switch($file_type) {
        case "application/zip":
          Tools::ZipExtract($file_path, _PS_ROOT_DIR_ . '/modules/');
          # TODO: log result of extraction/updating
          # because I am not sure if updating is done at this step
        default:
          continue;
      }
    }
  }

  /**
  * Gets the installation directory of Prestashop
  *
  * @return string installation directory
  */
  public function get_install_dir() {
    return _PS_ROOT_DIR_;
  }

  /**
  * Gets the running version of Prestashop
  *
  * @return string running Prestashop version
  */
  public function get_running_version() {
    return $this->get_global_value('PS_VERSION_DB');
  }

  /**
  * Tries to fix Prestashop issues with mail sending via SSL/TLS
  *
  */
  public function fix_mail($reverse = false) {
    require_once(_PRESH_DIR_ . '/fix_mail_encryption/fix.php');
    $fm = new FixMail();
    $fm->apply_patch($this->get_running_version(), $this->get_install_dir(), $reverse);
  }

  /**
  * Shows the available functionality of Presh
  *
  */
  public function help() {
    $methods = get_class_methods($this);
    foreach($methods as $m) {
      if ($m != '__construct')
        echo "\t$m\n";
    }
  }

  /**
  * Shows the current version of Presh
  *
  */
  public function version() {
    echo "0.2.0-alpha";
    echo "\n";
  }
}
?>
