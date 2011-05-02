<?php
/**
 * Copyright 2009 Zikula Foundation.
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license MIT
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class MyHookSubscriber_Installer extends Zikula_AbstractInstaller
{
    /**
     * install module
     */
    public function install()
    {
        // create table
        try {
            DoctrineUtil::createTablesFromModels('MyHookSubscriber');
        } catch (Exception $e) {
            return false;
        }

        // create default category
        if (!self::createdefaultcategory()) {
            LogUtil::registerStatus($this->__('Warning! Could not create the default category tree. If you want to use categorisation with MyHookSubscriber, register at least one property for the module using the Category Registry.'));
        }

        // register hook bundles
        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());

        // set up config variables
        $modvars = array(
            'itemsperpage' => 25,
            'enablecategorization' => true
        );
        $this->setVars($modvars);

        return true;
    }

    /**
     * upgrade module
     */
    public function upgrade($oldversion)
    {
        switch ($oldversion)
        {
            case '1.0.0':
                //HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());
        }

        return true;
    }

    /**
     * uninstall module
     */
    public function uninstall()
    {
        // drop table
        try {
            DoctrineUtil::dropTable('myhooksubscribers');
        } catch (Exception $e) {
            return false;
        }

        // delete default category
        if (!self::deletedefaultcategory()) {
            LogUtil::registerStatus($this->__('Warning! Could not delete the default category tree. You will have to manually delete the default category using Categories manager and then delete the registry using the Category Registry.'));
        }

        // unregister hook bundles
        HookUtil::unregisterHookSubscriberBundles($this->version);

        // delete any module variables
        $this->delVars();

        return true;
    }

    private function createdefaultcategory()
    {
        // some variables we are going to need
        $root_category_path = '/__SYSTEM__/Modules';
        $module_category = 'MyHookSubscriber';

        $modname = 'MyHookSubscriber';
        $tablename = 'myhooksubscribers';
        $property = 'Main';

        // first...
        // we want to create a category with the name 'MyHookSubscriber' under /__SYSTEM__/Modules
        // so we first check out if category already exists
        $category = CategoryUtil::getCategoryByPath($root_category_path.'/'.$module_category);

        // if it doesn't exist, create it
        if (!$category) {
            $category_id = CategoryUtil::createCategory($root_category_path, $module_category);
            $category = CategoryUtil::getCategoryByID($category_id);

            if (!$category) {
                return false;
            }
        }

        // second...
        // we want to create a category registry entry
        // so we first check out if it exists already
        $registry = CategoryRegistryUtil::getRegisteredModuleCategory($modname, $tablename, $property);

        // if it doesn't exist, create it
        if (!$registry) {
            $registry = CategoryRegistryUtil::insertEntry($modname, $tablename, $property, $category['id']);

            if (!$registry) {
                return false;
            }
        }

        return true;
    }

    private function deletedefaultcategory()
    {
        /* TODO delete default category */
        return true;
    }
}
