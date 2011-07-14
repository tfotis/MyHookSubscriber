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

class MyHookSubscriber_Controller_Admin extends Zikula_AbstractController
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
        // We don't actually have a main function, just return view with an offset of 1
        return $this->view(array('offset' => 1));
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

        // parameters to pass to dao
        $params = array(
            'offset' => $offset,
            'limit' => $limit
        );

        // get our table
        $itemsTable = Doctrine_Core::getTable('MyHookSubscriber_Model_Items');

        // get all items, but limit them according to our config value $limit
        $items = $itemsTable->getAll($params);

        // iterate through the data and add options for view, edit and delete - according to permissions
        $data = array();
        foreach ($items as $item) {
            $options = array();

            $options[] = array('url' => ModUtil::url('MyHookSubscriber', 'user', 'display', array('id' => $item['id'])), 'image' => 'demo.gif', 'title' => $this->__('View'));

            if (SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$item['id'], ACCESS_EDIT)) {
                $options[] = array('url' => ModUtil::url('MyHookSubscriber', 'admin', 'edit', array('id' => $item['id'])), 'image' => 'xedit.gif', 'title' => $this->__('Edit'));

                if (SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$item['id'], ACCESS_DELETE)) {
                    $options[] = array('url' => ModUtil::url('MyHookSubscriber', 'admin', 'delete', array('id' => $item['id'])), 'image' => '14_layer_deletelayer.gif', 'title' => $this->__('Delete'));
                }
            }

            $item['options'] = $options;

            $data[] = $item;
        }
        unset($items);

        // assign the data to the template
        $this->view->assign('data', $data);

        // assign the information required to create the pager
        $this->view->assign('pager', array('numitems' => $itemsTable->countall($params), 'limit' => $limit));

        // return the template
        return $this->view->fetch('myhooksubscriber_admin_view.tpl');
    }

    /**
     * create/edit an item
     *
     * @param $id int     the id of the item to be modified.
     * @param $data array the data of the item to be modified.
     * @return string HTML output
     */
    public function edit()
    {
        // get our table, we will need it in all our operations
        $itemsTable = Doctrine_Core::getTable('MyHookSubscriber_Model_Items');

        // get submitted data from POST
        $data = FormUtil::getPassedValue('data', null, 'POST');

        // if $data is null, the form isn't submitted yet,
        // so we show the form to the user to fill in / update the data
        if (!$data) {
            // get item id from GET
            $id = (int)FormUtil::getPassedValue('id', 0, 'GET');

            // if id is 0, then we show the form to create new item
            // else we show the form to edit existing item
            if ($id == 0) {
                // create an empty item from model
                $item = $itemsTable->create();

                // Security check
                if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_ADD)) {
                    return LogUtil::registerPermissionError();
                }
            } else {
                // get data of existing item
                $item = $itemsTable->find($id);

                // if item is empty, return with error
                if (empty($item)) {
                    return LogUtil::registerError($this->__('No such item found.'), 404);
                }

                // Security check
                if (!SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$id, ACCESS_EDIT)) {
                    return LogUtil::registerPermissionError();
                }
            }
        }

        // if $data is populated, the form is submitted so take action
        if ($data) {
            // Confirm authorisation code
            if (!SecurityUtil::confirmAuthKey()) {
                return LogUtil::registerAuthidError(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
            }

            // validate item
            // do some checking to validate the data for this item
            // eg. $itemValid = $this->validateItem($data);
            // for this example we don't have any validation, so just set it to true
            $itemValid = true;

            // validate any hooks
            $hook = new Zikula_ValidationHook('myhooksubscriber.ui_hooks.mhs.validate_edit', new Zikula_Hook_ValidationProviders());
            $validators = $this->notifyHooks($hook)->getValidators();
            if ($validators->hasErrors() || !$itemValid) {
                LogUtil::registerError($this->__('Some errors were found.'));
            } else {

                // save item
                $id = $itemsTable->save($data);

                // item created/updated, so notify hooks of the event
                $url = new Zikula_ModUrl('MyHookSubscriber', 'user', 'display', ZLanguage::getLanguageCode(), array('id' => $id));
                $hook = new Zikula_ProcessHook('myhooksubscriber.ui_hooks.mhs.process_edit', $id, $url);
                $this->notifyHooks($hook);

                // An item was created, so we clear all cached templates (list of the items).
                $this->view->clear_cache('myhooksubscriber_user_view.tpl');

                // set status message
                LogUtil::registerStatus(empty($data['id']) ? $this->__('Item inserted.') : $this->__('Item updated.'));

                // return to main
                System::redirect(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
            }

            // if execution gets here, it means something went wrong
            // so show template but with the fields already filled in
            // if we have an edit operation, get existing item first to get the extra data (ob_status, cr_uid etc)
            if ($data['id']) {
                $item = $itemsTable->find($data['id']);
                $item = array_merge($item, $data);
            } else {
                $item = $data;
            }
        }

        // assign the item to the template
        $this->view->assign('item', $item);

        // categories
        if ($this->getVar('enablecategorization')) {
            $catregistry = CategoryRegistryUtil::getRegisteredModuleCategories('MyHookSubscriber', 'myhooksubscribers');
            $this->view->assign('catregistry', $catregistry);
        }

        // return the output
        return $this->view->fetch('myhooksubscriber_admin_edit.tpl');
    }

    /**
     * delete item
     *
     * @param 'id' the id of the item
     * @param 'confirmation' confirmation that this item can be deleted
     * @return mixed string HTML output if no confirmation otherwise true
     */
    public function delete()
    {
        // get our table, we will need it in all our operations
        $itemsTable = Doctrine_Core::getTable('MyHookSubscriber_Model_Items');

        // get id
        $id = (int)FormUtil::getPassedValue('id', 0, 'REQUEST');

        // get confirmation
        $confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');

        // get the existing item
        $item = $itemsTable->find($id);

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

            // return the output that has been generated by this function
            return $this->view->fetch('myhooksubscriber_admin_delete.tpl');
        }

        // If we get here it means that the user has confirmed the action

        // Confirm authorisation code
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
        }

        // validate any hooks
        $hook = new Zikula_ValidationHook('myhooksubscriber.ui_hooks.mhs.validate_delete', new Zikula_Hook_ValidationProviders());
        $validators = $this->notifyHooks($hook)->getValidators();
        if ($validators->hasErrors()) {
            return LogUtil::registerError($this->__('Some errors were found.'));
        }

        // delete the item
        $item->delete();

        // item deleted, so notify hooks of the event
        $hook = new Zikula_ProcessHook('myhooksubscriber.ui_hooks.mhs.process_delete', $id);
        $this->notifyHooks($hook);

        // An item was deleted, so we clear all cached pages
        $this->view->clear_cache(null, $id);
        $this->view->clear_cache('myhooksubscriber_user_view.tpl');

        // set status message
        LogUtil::registerStatus($this->__('Item deleted.'));

        return System::redirect(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
    }

    /**
     * modify module configuration
     *
     * @return mixed string HTML output string
     */
    public function settings()
    {
        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        // get submitted data from POST
        $settings = FormUtil::getPassedValue('settings', null, 'POST');

        // if $data is populated, the form is submitted so take action
        if ($settings) {
            // Confirm authorisation code
            if (!SecurityUtil::confirmAuthKey()) {
                return LogUtil::registerAuthidError(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
            }

            // Update module variables
            // itemsperpage
            $itemsperpage = (int)$settings['itemsperpage'];
            if ($itemsperpage < 1) {
                $itemsperpage = 25;
            }
            $this->setVar('itemsperpage', $itemsperpage);

            // enablecategorization
            if (isset($settings['enablecategorization'])) {
                $enablecategorization = true;
            } else {
                $enablecategorization = false;
            }
            $this->setVar('enablecategorization', $enablecategorization);

            // the module configuration has been updated successfuly
            LogUtil::registerStatus($this->__('Settings updated.'));

            return System::redirect(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
        } else {
            // return the template
            return $this->view->fetch('myhooksubscriber_admin_settings.tpl');
        }
    }
}
