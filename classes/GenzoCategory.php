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
    public $id_shop;
    public $id_lang;
    public $footer_description;

    public static $definition = array(
        'table'     => 'genzo_category_lang',
        'primary'   => 'id_genzo_category',
        'fields' => array(
            'id_category'        => array('type' => self::TYPE_INT),
            'id_shop'            => array('type' => self::TYPE_INT),
            'id_lang'            => array('type' => self::TYPE_INT),
            'footer_description' => array('type' => self::TYPE_HTML),
        )
    );

    public static function getIdGenzoCategory($id_category, $id_shop, $id_lang) {
        $query = new \DbQuery();
        $query->select('id_genzo_category');
        $query->from(self::$definition['table']);
        $query->where('id_category = ' . (int)$id_category);
        $query->where('id_shop = ' . (int)$id_shop);
        $query->where('id_lang = ' . (int)$id_lang);
        return \Db::getInstance()->getValue($query);
    }

    public static function getBackofficeData($id_category, $id_shop) {

        $languages = \Language::getIDs();

        foreach ($languages as $id_lang) {
            $query = new \DbQuery();
            $query->select('footer_description');
            $query->from(self::$definition['table']);
            $query->where('id_category = ' . (int)$id_category);
            $query->where('id_shop = ' . (int)$id_shop);
            $query->where('id_lang = ' . (int)$id_lang);
            $values[$id_lang] = \Db::getInstance()->getValue($query);
        }

        return $values;
    }
    
}