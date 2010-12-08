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
     * create a new item
     * @param $args['title'] the name of the item
     * @return int item id on success, 0 on failure
     */
    public function create($args)
    {
        // Argument check
        if (!isset($args['title'])) {
            LogUtil::registerArgsError();
            return 0;
        }

        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', $args['title'].'::', ACCESS_ADD)) {
            LogUtil::registerPermissionError();
            return 0;
        }

        // create item
        try {
            $item = new MyHookSubscriber_Model_Items();
            $item->merge($args);
            $item->save();
         } catch (Exception $e) {
            LogUtil::registerError($this->__('Error! Creation attempt failed.'));
            return 0;
        }

        // Return the id of the newly created item to the calling process
        LogUtil::registerStatus($this->__('Done! Item created.'));
        return $item->id;
    }

    /**
     * update an item
     * @param $args['id'] the ID of the item
     * @param $args['title'] the new name of the item
     * @return bool true on success, false on failure
     */
    public function update($args)
    {
        // Argument check
        if ((!isset($args['id']) || empty($args['id']) || !is_numeric($args['id'])) || !isset($args['title'])) {
            LogUtil::registerArgsError();
            return false;
        }

        // Check if item exists, and get information for security check
        $item = ModUtil::apiFunc('MyHookSubscriber', 'user', 'get', array('id' => $args['id']));
        if (empty($item)) {
            LogUtil::registerError($this->__('No such item found.'));
            return false;
        }

        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$item['id'], ACCESS_EDIT)) {
            LogUtil::registerPermissionError();
            return false;
        }

        // update item
        try {
            $item = Doctrine_Core::getTable('MyHookSubscriber_Model_Items')->find($args['id']);
            $item->merge($args);
            $item->save();
         } catch (Exception $e) {
            LogUtil::registerError($this->__('Error! Update attempt failed.'));
            return false;
        }

        LogUtil::registerStatus($this->__('Done! Item updated.'));
        return true;
    }

    /**
     * delete an item
     * @param $args['id'] id of the item
     * @return bool true on success, false on failure
     */
    public function delete($args)
    {
        // Argument check
        if (!isset($args['id']) || empty($args['id']) || !is_numeric($args['id'])) {
            LogUtil::registerArgsError();
            return false;
        }

        // Check item exists before attempting deletion
        $item = ModUtil::apiFunc('MyHookSubscriber', 'user', 'get', array('id' => $args['id']));
        if (empty($item)) {
            LogUtil::registerError($this->__('No such item found.'));
            return false;
        }

        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', "$item[title]::$item[id]", ACCESS_DELETE)) {
            LogUtil::registerPermissionError();
            return false;
        }

        // delete item
        try {
            $item = Doctrine_Core::getTable('MyHookSubscriber_Model_Items')->find($args['id']);
            $item->delete();
         } catch (Exception $e) {
            LogUtil::registerError($this->__('Error! Delete attempt failed.'));
            return false;
        }

        LogUtil::registerStatus($this->__('Done! Item deleted.'));
        return true;
    }

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
            $links[] = array('url'  => ModUtil::url('MyHookSubscriber', 'admin', 'modifyconfig'), 'text' => $this->__('Settings'));
        }

        return $links;
    }
}