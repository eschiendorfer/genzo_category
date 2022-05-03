<?php

/**
 * @var genzo_category $module
 * @return bool
 * @throws PrestaShopException
 */
function upgrade_module_2_0_0($module) {

    if (!$module->executeSqlScript('install-2.0.0') OR !$module->executeSqlScript('install')) {
        return false;
    }

    if (!readdOldCategories()) {
        return false;
    }

    if (!deleteHelperTables()) {
        return false;
    }

    return true;
}

function readdOldCategories() {

    $query = new DbQuery();
    $query->select('*');
    $query->from('genzo_category_old', 'c');
    $query->innerJoin('genzo_category_lang_old', 'cl', 'c.id_category=cl.id_category');
    $categories = Db::getInstance()->ExecuteS($query);

    foreach ($categories as $category) {

        if (($id_category = (int)$category['id_category']) && ($id_lang = (int)$category['id_lang']) && ($id_shop = (int)$category['id_shop']) && ($footer_description = $category['footer_description'])) {
            $genzoCategory = new \GenzoCategoryModule\GenzoCategory($id_category, $id_lang, $id_shop);
            $genzoCategory->id_category = $id_category;
            $genzoCategory->footer_description = $footer_description;
            $genzoCategory->save();
        }
    }

    return true;
}

function deleteHelperTables() {
    $sql = 'DROP TABLE IF EXISTS `' ._DB_PREFIX_. 'genzo_category_old`,`' ._DB_PREFIX_. 'genzo_category_lang_old`;';
    return Db::getInstance()->execute($sql);
}