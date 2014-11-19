<?php

/**
 * @file
 * presh is a command line interface for Prestashop e-commerce sites
 *
 * @requires PHP CLI 5.3.0, or newer.
 */

class Presh {

  const SECONDS_IN_A_WEEK = 604800; // 60 * 60 * 24 * 7

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
  * @param string $status status to set: true to activate, false to deactivate
  */
  public function activate_shop($status) {
    $this->update_global_value('PS_SHOP_ENABLE', $status);
  }

  /**
  * Toggles the activation status of the shop
  *
  */
  public function toggle_activation_status() {
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
  * @param bool $generate_htaccess whether to invoke .htaccess file generation
  * functionality (true by default)
  */
  public function set_friendly_urls($status, $generate_htaccess = true) {
    $this->update_global_value('PS_REWRITING_SETTINGS', $status);
    Tools::generateHtaccess();
  }

  /**
  * Sets the domain URL of your shop 
  *
  * @param string $domain domain URL
  * @param bool $generate_htaccess whether to invoke .htaccess file generation
  * functionality (true by default)
  */
  public function set_shop_domain($domain, $generate_htaccess = true) {
    $this->update_global_value('PS_SHOP_DOMAIN', $domain);
    Tools::generateHtaccess();
  }

  /**
  * Enables or disables several settings for debugging purposes
  *
  * @param string $status status to set
  */
  public function set_debug_settings($status) {
    $this->update_global_value('PS_DISABLE_NON_NATIVE_MODULE', $status);
    $this->update_global_value('PS_DISABLE_OVERRIDES', $status);
  }

  /**
  * Enables or disables several settings for peformance purposes
  * through CCC (Combine, Compress and Cache)
  *
  * @param string $status status to set
  */
  public function optimize_via_ccc($status) {
    $this->update_global_value('PS_CSS_THEME_CACHE', $status);
    $this->update_global_value('PS_JS_THEME_CACHE', $status);
    $this->update_global_value('PS_HTML_THEME_COMPRESSION', $status);
    $this->update_global_value('PS_JS_HTML_THEME_COMPRESSION', $status);
    $this->update_global_value('PS_JS_DEFER', $status);
    $this->update_global_value('PS_HTACCESS_CACHE_CONTROL', $status);
  }

  /**
  * Enables SMTP mail method and configures its related fields
  *
  * @param string $mail_domain fully qualified domain name for mails
  * @param string $mail_server IP address or server name
  * @param string $mail_user optional smtp user
  * @param string $mail_passwd optional smtp password
  * @param string $encryption_mode encryption protocol to use ('off' by default)
  * @param string $port smtp port number to use (25 by default)
  */
  public function set_smtp_mailing($mail_domain, $mail_server, $mail_user,
      $mail_passwd, $encryption_mode = 'off', $port = '25') {
    $this->update_global_value('PS_MAIL_METHOD', 2); // "2" means "use SMTP"
    $this->update_global_value('PS_MAIL_DOMAIN', $mail_domain);
    $this->update_global_value('PS_MAIL_SERVER', $mail_server);
    if ($mail_user != null && strlen($mail_user) > 0) {
      $this->update_global_value('PS_MAIL_USER', $mail_user);
    }
    if ($mail_passwd != null && strlen($mail_passwd) > 0) {
      $this->update_global_value('PS_MAIL_PASSWD', $mail_passwd);
    }
    if ($encryption_mode != null) {
      $encryption_mode = strtolower($encryption_mode);
      if ($encryption_mode == 'off' || $encryption_mode == 'tls' || $encryption_mode == 'ssl')
        $this->update_global_value('PS_MAIL_SMTP_ENCRYPTION', $encryption_mode);
    }
    $this->update_global_value('PS_MAIL_SMTP_PORT', $port);
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
    $module_instance = Module::getInstanceByName($name);
    return $module_instance->install();
  }

  /**
  * Uninstalls a module given its name. The module has to be present on
  * Prestashop modules directory
  *
  * @param string $name name of the module to uninstall
  * @return bool true when successfully uninstalled, false otherwise
  */
  public function uninstall_module($name) {
    $module_instance = Module::getInstanceByName($name);
    return $module_instance->uninstall();
  }

  /**
  * Enables a module given its name.
  * It is expected to have a module directory inside
  * Prestashop modules directory named like the 'name' parameter
  *
  * @param string $name name of the module to enable
  * @return bool true when successfully enabled, false otherwise
  */
  public function enable_module($name) {
    return Module::enableByName($name);
  }

  /**
  * Disables a module given its name.
  * It is expected to have a module directory inside
  * Prestashop modules directory named like the 'name' parameter
  *
  * @param string $name name of the module to disable
  * @return bool true when successfully disabled, false otherwise
  */
  public function disable_module($name) {
    return Module::disableByName($name);
  }

  /**
  * Lists installed modules in the store
  *
  */
  public function list_modules_installed() {
    foreach(Module::getModulesInstalled() as $m)
      echo $m['name'] . "\n";
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
    $this->update_modules_xml_file($xml_file, 'native_all');
    $xml_content = Tools::file_get_contents($xml_file);
    $xml_tree = @simplexml_load_string($xml_content, null, LIBXML_NOCDATA);
    $modules_to_update = array();
    $modules_on_disk = Module::getModulesOnDisk(true, false, 1);

    foreach($modules_on_disk as $module) {
      // it seems some modules (like 'blockbanner') can have its 'installed'
      // attribute empty, so it's better to update it anyway
      //if ($module->installed != true)
      //  continue;
      Module::initUpgradeModule($module);

      foreach($xml_tree->module as $modaddons) {
        if($module->name == $modaddons->name) {
          if (Tools::version_compare($module->version, $modaddons->version, '<')) {
            $modules_to_update[$module->name]['id'] = $modaddons->id;
          }
        }
      }
    }

    foreach($modules_to_update as $module_name => $module) {
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
      $module_instance = Module::getInstanceByName($module_name);
      if (Module::needUpgrade($module_instance) == true) {
        $module_instance->runUpgradeModule();
      }
    }
  }

  /**
  * Updates a given file with the contents available in the Prestashop
  * addons online resource. It is assumed that the given file exists inside
  * the /config/xml/ subdirectory of a Prestashop installation.
  *
  * @param string $file xml file to update
  * @param string $request_type type of request to use with the addons web site
  * @param int $timeout max age, in seconds, of the given file to be considered
  * as updated before doing a new update
  */
  public function update_modules_xml_file($file, $request_type, $timeout = SECONDS_IN_A_WEEK) {
    $file_age = 0;
    if (file_exists($file) && filesize($file) > 0)
      $file_age = time() - filemtime($file);
    if ($file_age >= $timeout || $file_age == 0)
      file_put_contents($file, Tools::addonsRequest($request_type));
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
  * @param bool $reverse indicates wheter a reverse patch will be applied, so changes
  * to Prestashop core files will be reverted. It is false by default, so a forward
  * patch will be applied
  * @param bool $try_to_get_swift flags if a suitable version of Swift Mailer library
  * will be downloaded if needed. It is true by default
  * @return bool the result of trying to apply the patch file
  *
  */
  public function fix_mail($reverse = false, $try_to_get_swift = true) {
    if ($reverse == true)
      Tools::deleteDirectory(_PS_ROOT_DIR_ . '/tools/swift5/lib');
    // get Swift Mailer
    if ($try_to_get_swift == true && $reverse == false)
      $this->get_swift_mailer_library();
    require_once(_PRESH_DIR_ . '/fix_mail_encryption/fix.php');
    $fm = new FixMail();
    return $fm->apply_patch($this->get_running_version(),
      $this->get_install_dir(), $reverse);
  }

  /**
  * Downloads Swift Mailer library
  *
  * @param string $url URL to download Swift Mailer from. If none specified
  * it will be gotten from its official GitHub repository
  * @param string $version Swift Mailer version to get (5.2.2 by default)
  *
  */
  public function get_swift_mailer_library($url = '', $version = '5.2.2') {
    $dst_lib_dir = _PS_ROOT_DIR_ . '/tools/swift5/lib';
    if (file_exists($dst_lib_dir)) // do nothing if already exists
      return;
    else
      mkdir($dst_lib_dir, 0755, true); // or create it if not exists
    if ($url == null || $url == '') {
      $url = 'https://github.com/swiftmailer/swiftmailer/archive/v' . $version . '.zip';
    }
    $tmp_dir = Utils::create_temp_dir();
    $src_lib_dir = $tmp_dir . DIRECTORY_SEPARATOR . 'swiftmailer-' . $version . DIRECTORY_SEPARATOR . 'lib';
    $file_handle = Utils::write_to_temp_file(Tools::file_get_contents($url));
    $file_path = Utils::get_path_of_file_handle($file_handle);
    $file_type = Utils::get_file_magic_type($file_path);
    switch ($file_type) {
      case "application/zip":
        Tools::ZipExtract($file_path, $tmp_dir);
        break;
      default:
        return;
    }
    Tools::recurseCopy($src_lib_dir, $dst_lib_dir);
    Tools::deleteDirectory($tmp_dir);
  }

  /**
  * Wipes out the catalog, sales and stores data of a Prestashop installation,
  * hopefully being the sample data installed by default. This is a dangerous
  * and potentially destructive operation, because it can evaporate your real
  * products, categories, sales and related data. This function internally
  * uses the 'pscleaner' module
  *
  * @param string $confirmation Fixed string expected to confirm you are
  * not mad. It is null by default so it is required to explicitly pass a first
  * argument expected to be "Do It, I Am Completely Sure This Will Destroy My Data"
  * if you really want to evaporate the data (there is no 'undo' option)
  * @param bool $verify_is_only_sample_data flag to check if the database contains
  * only the sample data loaded by the installer, prior to attempt the cleaning
  * @param bool $restore_initial_status flag to indicate the uninstalling and/or
  * deletion of the 'pscleaner' module, so its state is the same it had at the
  * beginning of this function
  *
  */
  public function clean_sample_data($confirmation = null,
                                    $restore_initial_status = true,
                                    $verify_is_only_sample_data = true) {
    // TODO: review what is useful here
    // http://stackoverflow.com/questions/15277017/prestashop-delete-all-testing-data-before-production
    // http://maiolab.net/deleting-demo-data-in-default-prestashop-database/
    if ($confirmation == null || $confirmation != 'Do It, I Am Completely Sure This Will Destroy My Data')
      return;
    if ($verify_is_only_sample_data) {
      if (!$this->is_only_sample_data_installed())
        return;
    }
    $mod_dir = _PS_ROOT_DIR_ . '/modules/pscleaner/';
    $initially_installed = Module::isInstalled('pscleaner');
    $initially_enabled = Module::isEnabled('pscleaner');
    $initially_exists_dir = file_exists($mod_dir);
    if (!$initially_installed) {
      $xml_file = _PS_ROOT_DIR_ . '/config/xml/modules_native_addons.xml';
      $xml_raw_content = Tools::file_get_contents($xml_file);
      $id = null;
      if ($xml_tree = simplexml_load_string($xml_raw_content, null, LIBXML_NOCDATA))
        foreach($xml_tree->module as $modaddons) {
          if ('pscleaner' == $modaddons->name) {
            $id = $modaddons->id;
            break;
          }
        }
      if ($id) {
        $file_handle = Utils::write_to_temp_file(Tools::addonsRequest('module', array('id_module' => pSQL($id))));
        $file_path = Utils::get_path_of_file_handle($file_handle);
        $file_type = Utils::get_file_magic_type($file_path);
        switch ($file_type) {
          case "application/zip":
            Tools::ZipExtract($file_path, _PS_ROOT_DIR_ . '/modules/');
            break;
          default:
            return;
        }
        $this->install_module('pscleaner');
      }
    } // end if (!$initially_installed)
    if (!Module::isEnabled('pscleaner'))
      $this->enable_module('pscleaner');

    $module_instance = Module::getInstanceByName('pscleaner');
    if ($module_instance) {
      $module_instance->truncate('catalog');
      $module_instance->truncate('sales');
    }
    //
    if ($restore_initial_status) {
      if (!$initially_enabled)
        $this->disable_module('pscleaner');
      if (!$initially_installed)
        $this->uninstall_module('pscleaner');
      if (!$initially_exists_dir)
        Tools::deleteDirectory($mod_dir);
    }
    // TODO: propose to 'pscleaner' developers some code to clean
    // sample stores and then remove this part
    Db::getInstance()->executeS('TRUNCATE TABLE `' . _DB_PREFIX_ . 'store`');
    Db::getInstance()->executeS('TRUNCATE TABLE `' . _DB_PREFIX_ . 'store_shop`');
    foreach (scandir(_PS_STORE_IMG_DIR_) as $file_or_dir)
      if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $file_or_dir) &&
          is_file(_PS_STORE_IMG_DIR_ . $file_or_dir))
        unlink(_PS_STORE_IMG_DIR_ . $file_or_dir);
    // banners in themeconfigurator hooks
    Db::getInstance()->executeS('TRUNCATE TABLE `' . _DB_PREFIX_ . 'themeconfigurator`');
  }

   /**
  * Review customers, products, categories and stores data to check if it only
  * corresponds to the sample data of catalog and sales loaded by the
  * Prestashop installer, also checking if the Prestashop version installed
  * is compatible with this function
  * @return bool true if only sample data is present, false otherwise
  *
  */
  public function is_only_sample_data_installed() {
    if (!(Tools::version_compare($this->get_running_version(), '1.6.0.0', '>=') &&
          Tools::version_compare($this->get_running_version(), '1.6.0.9', '<=')))
      return false;
    // a) john doe
    $customers = Customer::getCustomers();
    if (count($customers) != 1)
      return false;
    $customer_john = $customers[0]; 
    if (!($customer_john['email'] == 'pub@prestashop.com' &&
          $customer_john['firstname'] == 'John' &&
          $customer_john['lastname'] == 'DOE'))
      return false;
    // b) products
    $id_lang = Context::getContext()->language->id;
    $sample_products = Product::getProducts($id_lang, 0, 0, 'name', 'asc');
    if (count($sample_products) != 7)
      return false;
    //$sample_products_names = array_column($sample_products, 'name'); // requires PHP >= 5.5
    $sample_products_names = array_map(function ($v){ return $v['name']; }, $sample_products);
    asort($sample_products_names);
    $expected_products_names = array('Blouse', 'Faded Short Sleeve T-shirts',
      'Printed Chiffon Dress', 'Printed Dress', 'Printed Dress',
      'Printed Summer Dress', 'Printed Summer Dress'
    );
    asort($expected_products_names);
    if (count(array_diff($sample_products_names, $expected_products_names)) != 0)
      return false;
    // c) categories
    // Category::getCategories($id_lang, false)
    // Category::getNestedCategories(null, $id_lang, false)
    // Category::getSimpleCategories($id_lang)
    // Category::getHomeCategories($id_lang, false)
    // Category::getRootCategory($id_lang)
    $simple_categories = Category::getSimpleCategories($id_lang);
    if (count($simple_categories) != 10)
      return false;
    $root_category = Category::getRootCategory($id_lang);
    $sample_categories = array_filter($simple_categories,
      function($it) use($root_category) {
        return $it['id_category'] != $root_category->id_category;
      }
    );
    $sample_categories_names = array_map(function ($v){ return $v['name']; }, $sample_categories);
    asort($sample_categories_names);
    $expected_categories_names = array('Blouses', 'Casual Dresses',
      'Dresses', 'Evening Dresses', 'Summer Dresses', 'T-shirts',
      'Tops', 'Tops', 'Women'
    );
    asort($expected_categories_names);
    if (count(array_diff($sample_categories_names, $expected_categories_names)) != 0)
      return false;
    // d) stores
    $sample_stores = Db::getInstance()->executeS('SELECT name from ' . _DB_PREFIX_. 'store');
    if (count($sample_stores) != 5)
      return false;
    $sample_stores_names = array_map(function ($v){ return $v['name']; }, $sample_stores);
    asort($sample_stores_names);
    $expected_stores_names = array('Coconut Grove', 'Dade County',
      'E Fort Lauderdale', 'N Miami/Biscayne', 'Pembroke Pines'
    );
    if (count(array_diff($sample_stores_names, $expected_stores_names)) != 0)
      return false;
    // e) banners in themeconfigurator
    $sample_tc_banners = Db::getInstance()->executeS('SELECT image from ' . _DB_PREFIX_. 'themeconfigurator');
    if (count($sample_tc_banners != 7))
      return false;
    $sample_tc_banners_images = array_map(function ($v){ return $v['image']; }, $sample_tc_banners);
    asort($sample_tc_banners_images);
    $expected_tc_banners_images = array('banner-img1.jpg', 'banner-img2.jpg',
      'banner-img3.jpg', 'banner-img4.jpg', 'banner-img5.jpg',
      'banner-img6.jpg', 'banner-img7.jpg'
    );
    if (count(array_diff($sample_tc_banners_images, $expected_tc_banners_images)) != 0)
      return false;
    // if all test passed, then it is only sample data
    return true;
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
