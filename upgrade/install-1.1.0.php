<?php

/**
 * @var genzo_category $module
 * @return bool
 * @throws PrestaShopException
 */
function upgrade_module_1_1_0($module) {

    if (!$module->executeSqlScript('install-1.1.0') OR !insertExistingCategories()) {
        return false;
    }

    return true;
}

function insertExistingCategories() {
    $query = new DbQuery();
    $query->select('id_category');
    $query->from('category');
    $categories =  Db::getInstance()->ExecuteS($query);

    if(!Db::getInstance()->insert('genzo_category', $categories)) {
        return false;
    }

    return true;

}