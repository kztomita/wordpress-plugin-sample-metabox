<?php
/**
 * @package Sample MetaBox
 * @version 1.0
 */
/*
Plugin Name: Sample MetaBox
Plugin URI: 
Description: 記事編集ページへのmeta box追加サンプル。
Version: 1.0
Author URI: 
*/

if (is_admin()) {
	include_once(dirname(__FILE__).'/sample-metabox-admin.php');
}

