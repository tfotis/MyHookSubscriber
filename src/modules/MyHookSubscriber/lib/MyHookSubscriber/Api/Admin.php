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

class MyHookSubscriber_Api_Admin extends Zikula_Api
{
    /**
     * get available admin panel links
     *
     * @return array array of admin links
     */
    public function getlinks()
    {
        $links = array();

        if (SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_READ)) {
            $links[] = array('url'  => ModUtil::url('MyHookSubscriber', 'admin', 'view'), 'text' => $this->__('View items'));
        }

        if (SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_ADD)) {
            $links[] = array('url'  => ModUtil::url('MyHookSubscriber', 'admin', 'edit'), 'text' => $this->__('Create an item'));
        }
        
        if (SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_ADMIN)) {
            $links[] = array('url'  => ModUtil::url('MyHookSubscriber', 'admin', 'settings'), 'text' => $this->__('Settings'));
        }

        return $links;
    }
}