<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');


$fields = array(
    array('name' => _M('zmp'), 'type'=>'float', 'required'=>true, 'extra'=>false, 'visible'=>true, 'filter'=>true),
    array('name' => _M('euro'), 'type'=>'float', 'required'=>true, 'extra'=>false, 'visible'=>true, 'filter'=>true),
    array('name' => _M('date'), 'type'=>'date', 'required'=>true, 'extra'=>false, 'visible'=>true, 'filter'=>true),
);

Utils_RecordBrowserCommon::install_new_recordset('currency_history', $fields);
Utils_RecordBrowserCommon::add_access('currency_history', 'view', 'ACCESS:all');