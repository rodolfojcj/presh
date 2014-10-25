<?php

/**
 * @file
 * Utility functions for Presh
 *
 * @requires PHP CLI 5.3.0, or newer.
 */

class Utils {

  /**
  * Gets the internal type of a file given its path.
  *
  * @param string $file_path path of the file to get the type
  * @return string type of the given file
  */
  public static function get_file_magic_type($file_path) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $file_path);
    return $file_type;
  }

  /**
  * Writes the given content to a temporary file.
  *
  * @param string $content content to be written on the file
  * @return string handle of the written file
  */
  public static function write_to_temp_file($content) {
    $file_handle = tmpfile();
    //$file_path = stream_get_meta_data($file_handle)['uri']; // requires PHP >= 5.4
    $sgmd = stream_get_meta_data($file_handle);
    $file_path = $sgmd['uri'];
    file_put_contents($file_path, $content);
    return $file_handle;
  }

  /**
  * Gets the path of a file handle
  *
  * @param string $handle handle the file
  * @return string path of the file
  */
  public static function get_path_of_file_handle($handle) {
    //return stream_get_meta_data($handle)['uri']; // requires PHP >= 5.4
    $sgmd = stream_get_meta_data($handle);
    return $sgmd['uri'];
  }

  /**
  * Creates a temporary directory inside the system's temporary directory
  *
  * @param string $dirname name of the directory to create. It's empty by
  * default and in this case a unique name will be used (kind of random name)
  * @param int $umask permission umask in octal mode (e.g 0777 and not 777)
  * @return string path of temporary directory created
  */
  public static function create_temp_dir($dirname = '', $umask = 0755) {
    $tmp_base = sys_get_temp_dir();
    if ($dirname == null || $dirname == '')
      $dirname = uniqid();
    $full_path = $tmp_base . DIRECTORY_SEPARATOR . $dirname;
    if (mkdir($full_path, $umask))
      return $full_path;
    else
      return null;
  }
}
?>
