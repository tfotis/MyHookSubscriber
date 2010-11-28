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

class MyHookSubscriber_Controller_Admin extends Zikula_Controller
{
    /**
     * Post initialize.
     *
     * @return void
     */
    protected function postInitialize()
    {
        // In this controller we don't want caching to be enabled.
        $this->view->setCaching(false);
    }

    /**
     * the main function
     *
     * @return string HTML output
     */
    public function main()
    {
        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        // Return the output
        return $this->view->fetch('myhooksubscriber_admin_main.tpl');
    }

    /**
     * view items
     *
     * @param $offset int the start item id for the pager
     * @return string HTML output
     */
    public function view($args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        // offset from pager
        $offset = (int)FormUtil::getPassedValue('offset', isset($args['offset']) ? $args['offset'] : 1, 'GET');

        // limit items
        $limit = $this->getVar('itemsperpage');

        // Get all items
        $items = ModUtil::apiFunc('MyHookSubscriber', 'user', 'getall', array('offset' => $offset, 'limit' => $limit));

        $data = array();
        foreach ($items as $key => $item) {
            $options = array();
            $options[] = array('url' => ModUtil::url('MyHookSubscriber', 'user', 'display', array('id' => $item['id'])), 'image' => 'demo.gif', 'title' => $this->__('View'));

            if (SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$item['id'], ACCESS_EDIT)) {
                $options[] = array('url' => ModUtil::url('MyHookSubscriber', 'admin', 'modify', array('id' => $item['id'])), 'image' => 'xedit.gif', 'title' => $this->__('Edit'));

                if (SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$item['id'], ACCESS_DELETE)) {
                    $options[] = array('url' => ModUtil::url('MyHookSubscriber', 'admin', 'delete', array('id' => $item['id'])), 'image' => '14_layer_deletelayer.gif', 'title' => $this->__('Delete'));
                }
            }

            $item['options'] = $options;

            $data[] = $item;
        }

        // assign the data to the template
        $this->view->assign('data', $data);

        // assign the information required to create the pager
        $numitems = ModUtil::apiFunc('MyHookSubscriber', 'user', 'countitems');
        $this->view->assign('pager', array('numitems' => $numitems, 'limit' => $limit));

        // return the output
        return $this->view->fetch('myhooksubscriber_admin_view.tpl');
    }

    /**
     * add new item
     *
     * @return string HTML output
     */
    public function newitem()
    {
        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_ADD)) {
            return LogUtil::registerPermissionError();
        }

        // Return the output
        return $this->view->fetch('myhooksubscriber_admin_new.tpl');
    }

    /**
     * create an item
     * @param 'title' string the title of the item
     */
    public function create($args)
    {
        // Confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError (ModUtil::url('MyHookSubscriber', 'admin', 'view'));
        }

        $data = FormUtil::getPassedValue('data', isset($args['data']) ? $args['data'] : null, 'POST');
        
        // validate item
        // do some checking to validate the data for this item
        // eg. $itemValid = $this->validateItem($data);

        // validate any hooks
        $validators = new Zikula_Collection_HookValidationProviders();
        $validators = $this->notifyHooks('myhooksubscriber.hook.mhs.validate.edit', $data, null, array(), $validators)->getData();
        if ($validators->hasErrors()) {
            return LogUtil::registerError($this->__('Some errors were found.'));
        }

        // Create the item
        $id = ModUtil::apiFunc('MyHookSubscriber', 'admin', 'create', $data);

        // The return value of the function is checked
        if ($id > 0) {
            // item created, so notify hooks of the event
            $this->notifyHooks('myhooksubscriber.hook.mhs.process.edit', $data, $id);
            
            // An item was created, so we clear all cached templates (list of the items).
            $this->view->clear_cache('myhooksubscriber_user_view.tpl');
        } 
        
        return System::redirect(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
    }

    /**
     * modify an item
     *
     * @param id int the id of the item to be modified
     * @return string HTML output
     */
    public function modify($args)
    {
        $id   = FormUtil::getPassedValue('id', isset($args['id']) ? $args['id'] : null, 'GET');

        // Get the item
        $item = ModUtil::apiFunc('MyHookSubscriber', 'user', 'get', array('id' => $id));

        // if item is empty, return with error
        if (empty($item)) {
            return LogUtil::registerError($this->__('No such item found.'), 404);
        }

        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$id, ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        $item['returnurl'] = System::serverGetVar('HTTP_REFERER');

        // assign the item to the template
        $this->view->assign('item', $item);

        // Return the output
        return $this->view->fetch('myhooksubscriber_admin_modify.tpl');
    }

    /**
     * update item
     *
     * @param 'id' the id of the item
     * @param 'title' the title of the item
     */
    public function update($args)
    {
        // Confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
        }

        $data = FormUtil::getPassedValue('data', isset($args['data']) ? $args['data'] : null, 'POST');
        $url  = FormUtil::getPassedValue('url', isset($args['url']) ? $args['url'] : null, 'POST');

        // validate item
        // do some checking to validate the data for this item
        // eg. $itemValid = $this->validateItem($data);

        // validate any hooks
        $validators = new Zikula_Collection_HookValidationProviders();
        $validators = $this->notifyHooks('myhooksubscriber.hook.mhs.validate.edit', $data, $data['id'], array(), $validators)->getData();
        if ($validators->hasErrors()) {
            return LogUtil::registerError($this->__('Some errors were found.'));
        }

        // Update the item
        $update = ModUtil::apiFunc('MyHookSubscriber', 'admin', 'update', $data);
        
        if ($update) {
            // item updated, so notify hooks of the event
            $this->notifyHooks('myhooksubscriber.hook.mhs.process.edit', $data, $data['id']);

            // An item was updated, so we clear all cached templates
            $this->view->clear_cache(null, $data['id']);
            $this->view->clear_cache('myhooksubscriber_user_view.tpl');
        }

        if (!isset($url)) {
            return System::redirect(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
        }

        return System::redirect($url);
    }

    /**
     * delete item
     *
     * @param 'id' the id of the item
     * @param 'confirmation' confirmation that this item can be deleted
     * @return mixed string HTML output if no confirmation otherwise true
     */
    public function delete($args)
    {
        // get id
        $id = FormUtil::getPassedValue('id', isset($args['id']) ? $args['id'] : null, 'REQUEST');

        // get cofirmation
        $confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');

        // Get the existing item
        $item = ModUtil::apiFunc('MyHookSubscriber', 'user', 'get', array('id' => $id));

        if (empty($item)) {
            return LogUtil::registerError($this->__('No such item found.'), 404);
        }

        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$id, ACCESS_DELETE)) {
            return LogUtil::registerPermissionError();
        }

        // Check for confirmation.
        // If it's empty, we don't have confirmation to delete yet.
        if (empty($confirmation)) {
            // pass item to template
            $this->view->assign('item', $item);

            // Return the output that has been generated by this function
            return $this->view->fetch('myhooksubscriber_admin_delete.tpl');
        }

        // If we get here it means that the user has confirmed the action

        // Confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
        }

        // validate any hooks
        $validators = new Zikula_Collection_HookValidationProviders();
        $validators = $this->notifyHooks('myhooksubscriber.hook.mhs.validate.delete', $item, $item['id'], array(), $validators)->getData();
        if ($validators->hasErrors()) {
            return LogUtil::registerError($this->__('Some errors were found.'));
        }

        // Delete the item
        $delete = ModUtil::apiFunc('MyHookSubscriber', 'admin', 'delete', array('id' => $id));

        if ($delete) {
            // item deleted, so notify hooks of the event
            $this->notifyHooks('myhooksubscriber.hook.mhs.process.delete', $item, $id);

            // An item was deleted, so we clear all cached pages
            $this->view->clear_cache(null, $id);
            $this->view->clear_cache('myhooksubscriber_user_view.tpl');
        }

        return System::redirect(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
    }

    /**
     * modify module configuration
     *
     * @return mixed string HTML output string
     */
    public function modifyconfig()
    {
        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        // Return the output that has been generated by this function
        return $this->view->fetch('myhooksubscriber_admin_modifyconfig.tpl');
    }

    /**
     * update configuration
     *
     * @param 'itemsperpage' int the items to show per page
     * @return mixed string HTML output if no confirmation otherwise true
     */
    public function updateconfig()
    {
        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        // Confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
        }

        // Update module variables
        $itemsperpage = (int)FormUtil::getPassedValue('itemsperpage', 25, 'POST');
        if ($itemsperpage < 1) {
            $itemsperpage = 25;
        }
        $this->setVar('itemsperpage', $itemsperpage);

        // the module configuration has been updated successfuly
        LogUtil::registerStatus($this->__('Done! Module configuration updated.'));

        return System::redirect(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
    }
}
