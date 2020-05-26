<?php

/**
 * Copyright (C) 2018 Emanuel Schiendorfer
 *
 * @author    Emanuel Schiendorfer <https://github.com/eschiendorfer>
 * @copyright 2018 Emanuel Schiendorfer
 */

namespace GenzoCategoryModule;

class GenzoCategory extends \ObjectModel {

    public $id_category;
    public $id_lang;
    public $footer_description;

    public static $definition = array(
        'table'     => 'genzo_category',
        'primary'   => 'id_category',
        'auto_increment' => false,
        'multilang'      => true,
        'fields' => array(
            'footer_description' => array('type' => self::TYPE_HTML, 'lang' => true),
        )
    );
}