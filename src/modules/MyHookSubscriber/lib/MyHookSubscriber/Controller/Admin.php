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

        // assign the data to the template
        $this->view->assign('data', $data);

        // assign the information required to create the pager
        $numitems = ModUtil::apiFunc('MyHookSubscriber', 'user', 'countitems');
        $this->view->assign('pager', array('numitems' => $numitems, 'limit' => $limit));

        // return the output
        return $this->view->fetch('myhooksubscriber_admin_view.tpl');
    }

    /**
     * create/edit an item
     *
     * @param $id int the id of the item to be modified
     * @return string HTML output
     */
    public function edit()
    {
        // get item id from GET
        $id = (int)FormUtil::getPassedValue('id', 0, 'GET');

        // get submitted data from POST
        $data = FormUtil::getPassedValue('data', null, 'POST');

        // if $data is null, the form isn't submitted yet,
        // so we show the form to the user to fill in / update the data
        if (!$data) {
            // if id is 0, then we show the form to create new item
            // else we show the form to edit existing item
            if ($id == 0) {
                // create an empty item from model
                $item = new MyHookSubscriber_Model_Record();

                // Security check
                if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_ADD)) {
                    return LogUtil::registerPermissionError();
                }
            } else {
                // get data of existing item
                $item = ModUtil::apiFunc('MyHookSubscriber', 'user', 'get', array('id' => $id));

                // if item is empty (does not exist or no permission?), return with error
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

            // type cast our id because inside the $data is a string
            $data['id'] = (int)$data['id'];
            
            // validate item
            // do some checking to validate the data for this item
            // eg. $itemValid = $this->validateItem($data);

            // validate any hooks
            $validators = new Zikula_Collection_HookValidationProviders();
            $validators = $this->notifyHooks('myhooksubscriber.hook.mhs.validate.edit', $data, (($data['id'] > 0) ? $data['id'] :  null), array(), $validators)->getData();
            if ($validators->hasErrors()) {
                LogUtil::registerError($this->__('Some errors were found.'));
                // maybe get the errors that the hook registered and show them?
            } else {
                // set a flag to assign our create/update operation status
                $status = false;

                // if $data['id'] is 0, we have an insert operation
                // else we have an update operation
                if ($data['id'] == 0) {
                    // perform insert
                    $insert_id = ModUtil::apiFunc('MyHookSubscriber', 'admin', 'create', $data);
                    if ($insert_id > 0) {
                        // update our $data array with the given id
                        $data['id'] = $insert_id;
                        // set status as true
                        $status = true;
                    }
                } else {
                    // perform update
                    $status = ModUtil::apiFunc('MyHookSubscriber', 'admin', 'update', $data);
                }

                if ($status == true) {
                    // item created/updated, so notify hooks of the event
                    $this->notifyHooks('myhooksubscriber.hook.mhs.process.edit', $data, $data['id']);

                    // An item was created, so we clear all cached templates (list of the items).
                    $this->view->clear_cache('myhooksubscriber_user_view.tpl');

                    // return to main
                    System::redirect(ModUtil::url('MyHookSubscriber', 'admin', 'view'));
                }
            }

            // if execution gets here, it means something went wrong
            // so show template but with the fields already filled in
            // if we have an edit operation, get existing item first to get the extra data (ob_status, cr_uid etc)
            if ($data['id'] > 0) {
                $item = ModUtil::apiFunc('MyHookSubscriber', 'user', 'get', array('id' => $data['id']));
            }
            
            if (isset($item) && !empty($item)) {
                $item = array_merge($item, $data);
            } else {
                $item = $data;
            }
        }

        // assign the item to the template
        $this->view->assign('item', $item);

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
