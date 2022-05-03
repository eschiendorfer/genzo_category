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

        // Make sure, that core knows all the shop table relations
        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation('genzo_category_lang', array('type' => 'fk_shop'));
        }

	 	parent::__construct();

		$this->displayName = $this->l('Genzo Category');
		$this->description = $this->l('With this module, you can add footer description to categories!');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	}

	public function install() {
		if (!parent::install() OR
			!$this->executeSqlScript('install') OR
            !$this->registerHook('displayHeader') OR
            !$this->registerHook('displayFooter') OR
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

        $confirmation = '';

        if (Tools::isSubmit('saveGenzoCategory')) {
            $this->processForm();
            $confirmation = $this->displayConfirmation($this->l('Configuration was saved!'));
        }

        return $confirmation.$this->renderForm();

    }

    private function renderForm() {

        $inputs[] = array(
            'type' => 'switch',
            'label' => $this->l('Custom Hook'),
            'name' => 'custom_hook',
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('No')
                )
            ),
            'desc' => 'If yes, you have to add a {hook h=\'DisplayCategoryFooterDescription\'} to category.tpl in your template. Otherwise the default displayFooter hook is used.'
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' =>$this->l('Configuration'),
                    'icon' => 'icon-cogs',
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save Setting'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'saveGenzoCategory',
                ),
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->tpl_vars = [
            'fields_value' => ['custom_hook' => (int)Configuration::get('GENZO_CATEGORY_HOOK_CUSTOM')],
        ];

        return $helper->generateForm([$fields_form]);
    }

    private function processForm() {

        $custom_hook = (bool)Tools::getValue('custom_hook');
        Configuration::updateGlobalValue('GENZO_CATEGORY_HOOK_CUSTOM', $custom_hook);

        if ($custom_hook) {
            $this->unregisterHook('displayFooter');
            $this->registerHook('displayCategoryFooterDescription');
        }
        else {
            $this->registerHook('displayFooter');
            $this->unregisterHook('displayCategoryFooterDescription');
        }

    }

    //Hooks
    public function hookDisplayHeader () {
	    // CSS
        $this->context->controller->addCSS($this->_path.'/views/css/genzo_category.css');
    }

    public function hookDisplayCategoryFooterDescription() {
        return $this->renderHookContent();
    }

    public function hookDisplayFooter() {
        return $this->renderHookContent();
    }

    public function renderHookContent() {

	    if (Tools::getValue('controller')!='category') {
	        return null;
        }
        else {
	        $id_category = Tools::getValue('id_category');
	        $id_shop = $this->context->shop->id_shop;
	        $id_lang = $this->context->language->id_lang;

	        $genzoCategory = new GenzoCategory($id_category, $id_lang, $id_shop);
	        $footer_description = $this->checkShortcode($genzoCategory->footer_description);

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
            $footer_description = [];

            if (is_array($categoryGenzo->footer_description) && !empty($categoryGenzo->footer_description)) {
                $footer_description = $categoryGenzo->footer_description;
            }
            else {
                foreach (Language::getIDs() as $id_lang) {
                    $footer_description[$id_lang] = '';
                }
            }

            $params['fields_value']['footer_description'] = $footer_description;
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