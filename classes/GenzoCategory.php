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
    public $fakii;

    public static $definition = array(
        'table'     => 'genzo_category',
        'primary'   => 'id_category',
        'multilang'      => true,
        'fields' => array(
            'id_category'        => array('type' => self::TYPE_INT),
            'fakii' => array('type' => self::TYPE_BOOL),
            'footer_description' => array('type' => self::TYPE_HTML, 'lang' => true),
        )
    );
}