<?php
defined("_VALID_ACCESS") || die('Direct access forbidden');

Utils_RecordBrowserCommon::new_record_field(
    'Sales_plan',
    [
        'name' => _M('asf_zone'),
        'type' => 'commondata',
        'extra' => false,
        'visible' => true,
        'required' => true,
        'position' => 28,
        'param' => "Agrohandel/purchase_asf_zone",
    ]
);
