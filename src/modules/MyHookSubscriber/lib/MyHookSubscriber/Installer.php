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

class MyHookSubscriber_Installer extends Zikula_Installer
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

        // register hook bundles
        HookUtil::registerHookSubscriberBundles($this->version);

        // set up config variables
        $modvars = array(
            'itemsperpage' => 25
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
                //HookUtil::registerHookSubscriberBundles($this->version);
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
            DoctrineUtil::dropTable('myhooksubscriber');
        } catch (Exception $e) {
            return false;
        }

        // unregister hook bundles
        HookUtil::unRegisterHookSubscriberBundles($this->version);
        
        // delete any module variables
        $this->delVars();

        return true;
    }
}
