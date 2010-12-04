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

class MyHookSubscriber_Version extends Zikula_Version
{
    public function getMetaData()
    {
        $meta = array();
        $meta['displayname']    = $this->__('MyHookSubscriber');
        $meta['url'] = 'myhooksubscriber';
        $meta['version'] = '1.0.0';
        $meta['description'] = $this->__('Module that subscribes to hooks');
        $meta['securityschema'] = array('MyHookSubscriber::' => 'MyHookSubscriber name::MyHookSubscriber ID');
        $meta['capabilities'] = array(HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true));
        return $meta;
    }

    protected function setupHookBundles()
    {
         $bundle = new Zikula_Version_HookSubscriberBundle('modulehook_area.myhooksubscriber.mhs', __('MyHookSubscriber Display Hooks'));
         $bundle->addType('ui.view', 'myhooksubscriber.hook.mhs.ui.view');
         $bundle->addType('ui.edit', 'myhooksubscriber.hook.mhs.ui.edit');
         $bundle->addType('ui.delete', 'myhooksubscriber.hook.mhs.ui.delete');
         $bundle->addType('validate.edit', 'myhooksubscriber.hook.mhs.validate.edit');
         $bundle->addType('validate.delete', 'myhooksubscriber.hook.mhs.validate.delete');
         $bundle->addType('process.edit', 'myhooksubscriber.hook.mhs.process.edit');
         $bundle->addType('process.delete', 'myhooksubscriber.hook.mhs.process.delete');
         $this->registerHookSubscriberBundle($bundle);

         $bundle = new Zikula_Version_HookSubscriberBundle('modulehook_area.myhooksubscriber.mhsfilter', __('MyHookSubscriber Filter Hooks'));
         $bundle->addType('ui.filter', 'myhooksubscriber.hook.mhsfilter.ui.filter');
         $this->registerHookSubscriberBundle($bundle);
    }
}
