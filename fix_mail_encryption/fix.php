<?php

/**
 * @file
 * Fix Prestashop problems with sending mail via SSL/TLS, which seems to be
 * a very old issue according with some posts in the forum, for example
 * http://www.prestashop.com/forums/topic/233596-problem-with-smtp-using-live-domains-starttls-needed/
 *
 * @requires PHP CLI 5.3.0, or newer.
 */

class FixMail {
  private $PATCHES = array(
    "1.5.4.1" => "fix-1.5.4.1.diff",
    "1.5.6.2" => "fix-1.5.6.2.diff",
    "1.6.0.9" => "fix-1.6.0.9.diff"
  );

  /**
  * Applies a patch, if available, to a Prestashop installation
  * It uses Bash shell commands and needs its patch utility
  * It also assumes SwiftMailer has been installed on /tools directory
  * of Prestashop root directory (/tools/swift5/lib/)
  *
  * @param string $running_version running version of Prestashop
  * @param string $install_dir directory of Prestashop installation
  * @param boolean $reverse false to revert a previous patch
  * @return boolean true if patch applied correctly, false otherwise
  */
  public function apply_patch($running_version, $install_dir, $reverse = false) {
    $reverse = ($reverse === 'true' || $reverse === 1); // forced cast to boolean
    if(isset($this->PATCHES[$running_version])) {
      $patch_file = dirname(__FILE__) . "/patches/" . $this->PATCHES[$running_version];
      $patch_command = "patch -p1 < $patch_file";
      if ($reverse == true)
        $patch_command = "patch --reverse -p1 < $patch_file";
      $output = array();
      $return_value = NULL;
      chdir($install_dir);
      exec($patch_command, $output, $return_value);
      if ($return_value == 0)
        return true;
      else
        print_r($output);
    }
    return false;
  }
}
