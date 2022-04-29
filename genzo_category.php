<?php

/**
 * Copyright (C) 2022 Emanuel Schiendorfer
 *
 * @author    Emanuel Schiendorfer <https://github.com/eschiendorfer>
 * @copyright 2022 Emanuel Schiendorfer
 */

if (!defined('_PS_VERSION_'))
	exit;

include_once _PS_MODULE_DIR_ . 'genzo_category/classes/GenzoCategory.php';

use GenzoCategoryModule\GenzoCategory;

class Genzo_Category extends Module
{
	function __construct() {
		$this->name = 'genzo_category';
		$this->tab = 'front_office_features';
		$this->version = '2.0.0';
		$this->author = 'Emanuel Schiendorfer';
		$this->need_instance = 0;

		$this->bootstrap = true;

	 	parent::__construct();

		$this->displayName = $this->l('Genzo Category');
		$this->description = $this->l('With this module, you can add footer description to categories!');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	}

	public function install() {
		if (!parent::install() OR
			!$this->executeSqlScript('install') OR
            !$this->registerHook('displayHeader') OR
            !$this->registerHook('displayCategoryFooterDescription') OR
            !$this->registerHook('actionAdminCategoriesFormModifier') OR
            !$this->registerHook('actionAdminCategoriesControllerSaveAfter')
        )
			return false;
		return true;
	}

	public function uninstall() {
		if (!parent::uninstall() OR
			    !$this->executeSqlScript('uninstall')
			)
			return false;
		return true;
	}

    public function executeSqlScript($script) {
        $file = dirname(__FILE__) . '/sql/' . $script . '.sql';
        if (! file_exists($file)) {
            return false;
        }
        $sql = file_get_contents($file);
        if (! $sql) {
            return false;
        }
        $sql = str_replace(['PREFIX_', 'ENGINE_TYPE', 'CHARSET_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_, 'utf8'], $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $statement) {
            $stmt = trim($statement);
            if ($stmt) {
                if (!Db::getInstance()->execute($stmt)) {
                    return false;
                }
            }
        }
        return true;
    }

	// Backoffice
    public function getContent() {

        $id_primary = 776;
        $id_lang = null;
        $id_shop = null;


        $genzoCategory = new \GenzoCategoryModule\GenzoCategory($id_primary, $id_lang, $id_shop);
        // $genzoCategory->delete();

        // return;

        $genzoCategory->footer_description = "id_lang:{$id_lang} id_shop:{$id_shop} ".rand(0,100);
        $genzoCategory->save();

        // sleep(5);

        $genzoCategory = new \GenzoCategoryModule\GenzoCategory(776, 2, 2);
        $genzoCategory->footer_description = 'id_lang:2 id_shop:2';
        // $genzoCategory->save();

        // sleep(5);

        $genzoCategory = new \GenzoCategoryModule\GenzoCategory(776, 2, 3);
        $genzoCategory->footer_description = 'id_lang:2 id_shop:3';
        // $genzoCategory->save();

        return $this->adminDisplayInformation($this->l('To use this module, you have to add a hook to your template. Add the following to category.tpl: {hook h=\'DisplayCategoryFooterDescription\'}'));
    }

    //Hooks
    public function hookDisplayHeader () {
	    // CSS
        $this->context->controller->addCSS($this->_path.'/views/css/genzo_category.css');
    }

    public function hookDisplayCategoryFooterDescription () {

	    if (Tools::getValue('controller')!='category') {
	        return null;
        }
        else {
	        $id_category = Tools::getValue('id_category');
	        $id_shop = $this->context->shop->id_shop;
	        $id_lang = $this->context->language->id_lang;

	        $categoryGenzo = new GenzoCategory($id_category, $id_lang, $id_shop);
	        $footer_description = $this->checkShortcode($categoryGenzo->footer_description);

            $this->context->smarty->assign(array(
                'footer_description' => $footer_description,
            ));

            return $this->display(__FILE__, 'views/templates/hook/displayCategoryFooterDescription.tpl');
        }
    }

    public function hookActionAdminCategoriesFormModifier($params) {
        if ($id_category = Tools::getValue('id_category')) {

            $count = count($params['fields']);

            // New Fields
            $params['fields'][$count]['form']['legend']['title'] = $this->name;
            $params['fields'][$count]['form']['submit']['title'] = $this->l('Save');

            $params['fields'][$count]['form']['input']['footer_description'] = array(
                'type'   => 'textarea',
                'autoload_rte' => true,
                'label'  => $this->l('Footer description'),
                'name'   => 'footer_description',
                'lang' => true,
            );

            // Get Values
            $id_shop = $this->context->shop->id;

            $categoryGenzo = new GenzoCategory($id_category, null, $id_shop);

            $params['fields_value']['footer_description'] = $categoryGenzo->footer_description;
        }
    }

    public function hookActionAdminCategoriesControllerSaveAfter($params) {

        $id_category = (int)Tools::getValue('id_category');

        // This strange way seems to be the only to save a not auto_increment object model with _lang table
        $categoryGenzo = new GenzoCategory($id_category);

        foreach (Language::getIDs() as $id_lang) {
            $categoryGenzo->footer_description[$id_lang] = Tools::getValue('footer_description_' . $id_lang);
        }

        $categoryGenzo->save();
    }

    // Shortcode
    private function checkShortcode ($content) {
       if (file_exists(_PS_MODULE_DIR_ . 'genzo_shortcodes/genzo_shortcodes_include.php')) {
            include_once(_PS_MODULE_DIR_ . 'genzo_shortcodes/genzo_shortcodes_include.php');
            $content = genzoShortcodes::executeShortcodes($content);
        }
        return $content;
    }

}