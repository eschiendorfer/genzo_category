<?php

/**
 * Copyright (C) 2022 Emanuel Schiendorfer
 *
 * @author    Emanuel Schiendorfer <https://github.com/eschiendorfer>
 * @copyright 2022 Emanuel Schiendorfer
 */

namespace GenzoCategoryModule;

class GenzoCategory extends \ObjectModel {

    public $id_category_helper;
    public $id_category;
    public $footer_description;

    public static $definition = array(
        'table'     => 'genzo_category',
        'primary'   => 'id_category_helper',
        'multilang'      => true,
        'multilang_shop' => true,
        'fields' => array(
            'id_category'        => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'footer_description' => array('type' => self::TYPE_HTML, 'lang' => true),
        ),
        'images' => [
            'genzocategoryfooter' => [
                'inputName' => 'genzocategoryfooter',
                'path' => _PS_IMG_DIR_.'genzo_category/footer/',
                'imageTypes' => [
                    ['name' => 'genzo_category_footer', 'width' => 600, 'height' => 600],
                ]
            ]
        ]
    );

    public function __construct($id_category = null, $idLang = null, $id_shop = null) {
        $id_category_helper = $id_category ? self::getIdCategoryHelper($id_category) : null;
        parent::__construct($id_category_helper, $idLang, $id_shop);
        $this->id_category = $id_category;
    }

    private static function getIdCategoryHelper($id_category) {
        $id_category = (int)$id_category;
        $query = new \DbQuery();
        $query->select('id_category_helper');
        $query->from(self::$definition['table']);
        $query->where("id_category={$id_category}");
        return (int)\Db::getInstance()->getValue($query);
    }
}