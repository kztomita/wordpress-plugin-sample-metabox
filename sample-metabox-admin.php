<?php
/*
 * Sample Metabox 管理画面用処理
 */

/* フォーム要素の名前 */
define('SAMPLE_METABOX_NAME_CHECKBOX', 'sample_metabox_checkbox');
define('SAMPLE_METABOX_NAME_FILE',     'sample_metabox_file');
define('SAMPLE_METABOX_NAME_MEDIA',    'sample_metabox_media_url');
define('SAMPLE_METABOX_NAME_EDITOR',   'sample_metabox_editor');

/* meta情報の名前 */
define('SAMPLE_METABOX_METAKEY_CHECKBOX', 'sample_metabox_checkbox');
define('SAMPLE_METABOX_METAKEY_FILE',     'sample_metabox_file');
define('SAMPLE_METABOX_METAKEY_MEDIA',    'sample_metabox_media_url');
define('SAMPLE_METABOX_METAKEY_EDITOR',   'sample_metabox_editor');

include_once(dirname(__FILE__).'/sample-metabox-admin-checkbox.php');
include_once(dirname(__FILE__).'/sample-metabox-admin-file.php');
include_once(dirname(__FILE__).'/sample-metabox-admin-media.php');
include_once(dirname(__FILE__).'/sample-metabox-admin-editor.php');
