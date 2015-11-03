<?php
/*
* example run file
* dumps product object with ID givean as an argument
*
* @author     Tomasz Wesołowski <github@ittw.pl>
*/

$id = isset($argv[1]) ? $argv[1] : null;
if($id){
    $p = new Product($id);
    var_dump($p);
}else{
    die("Missing id argument. Example usage. 'presh run example_run_file.php 1'");
}
