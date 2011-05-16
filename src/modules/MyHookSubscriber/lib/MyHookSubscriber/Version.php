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

class MyHookSubscriber_Version extends Zikula_AbstractVersion
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
         $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.myhooksubscriber.ui_hooks.mhs', 'ui_hooks', __('MyHookSubscriber Display Hooks'));
         $bundle->addEvent('display_view', 'myhooksubscriber.ui_hooks.mhs.display_view');
         $bundle->addEvent('form_edit', 'myhooksubscriber.ui_hooks.mhs.form_edit');
         $bundle->addEvent('form_delete', 'myhooksubscriber.ui_hooks.mhs.form_delete');
         $bundle->addEvent('validate_edit', 'myhooksubscriber.ui_hooks.mhs.validate_edit');
         $bundle->addEvent('validate_delete', 'myhooksubscriber.ui_hooks.mhs.validate_delete');
         $bundle->addEvent('process_edit', 'myhooksubscriber.ui_hooks.mhs.process_edit');
         $bundle->addEvent('process_delete', 'myhooksubscriber.ui_hooks.mhs.process_delete');
         $this->registerHookSubscriberBundle($bundle);

         $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.myhooksubscriber.filter_hooks.mhs', 'filter_hooks', __('MyHookSubscriber Filter Hooks'));
         $bundle->addEvent('filter', 'myhooksubscriber.filter_hooks.mhs.filter');
         $this->registerHookSubscriberBundle($bundle);
    }
}
