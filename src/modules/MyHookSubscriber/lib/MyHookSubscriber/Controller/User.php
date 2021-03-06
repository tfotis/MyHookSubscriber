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

class MyHookSubscriber_Controller_User extends Zikula_AbstractController
{
    /**
     * the main function
     *
     * @return string html string
     */
    public function main()
    {
        if ($this->getVar('enablecategorization')) {
            return $this->categories();
        } else {
            return $this->view(array('offset' => 1));
        }
    }

    /**
     * view categories
     *
     * @param none
     * @return string html string
     */
    public function categories()
    {
        // security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::Categories', '::', ACCESS_OVERVIEW)) {
            return LogUtil::registerPermissionError();
        }

        // get registry
        $catregistry = CategoryRegistryUtil::getRegisteredModuleCategories('MyHookSubscriber', 'myhooksubscribers');

        // get categories under registry
        $all_categories = array();

        foreach($catregistry as $property => $cid) {
            $categories = CategoryUtil::getSubCategories($cid);
            $all_categories = array_merge($all_categories, $categories);
        }

        // assign the item output to the template
        $this->view->assign('categories', $all_categories);

        // Return the output
        return $this->view->fetch('myhooksubscriber_user_categories.tpl');
    }

    /**
     * view items
     *
     * @param $args['cid'] int the category id for the items (optional)
     * @param $args['offset'] int the start item id for the pager
     * @return string html string
     */
    public function view($args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_OVERVIEW)) {
            return LogUtil::registerPermissionError();
        }

        // offset
        $offset = (int)FormUtil::getPassedValue('offset', isset($args['offset']) ? $args['offset'] : 1, 'GET');
        if (!is_numeric($offset) || $offset == 0 || $offset < 0) {
            $offset = 1;
        }

         // limit
        $limit = $this->getVar('itemsperpage');

        // parameters to pass to dao
        $params = array(
            'offset' => $offset,
            'limit' => $limit
        );

        // if categorization is enabled and we have a cid, pass it to our parameters
        if ($this->getVar('enablecategorization')) {
            $cid = (int)FormUtil::getPassedValue('cid', isset($args['cid']) ? $args['cid'] : 0, 'GET');

            if ($cid > 0) {
                $params['cid'] = $cid;

                $category = CategoryUtil::getCategoryByID($cid);
                $this->view->assign('category', $category);
            }
        }

        // get our table
        $itemsTable = Doctrine_Core::getTable('MyHookSubscriber_Model_Items');

        // get all items, but limit them according to our config value $limit
        $items = $itemsTable->getAll($params);

        // loop through each item
        $data = array();
        foreach ($items as $item) {
            $options = array();

            if (SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$item['id'], ACCESS_EDIT)) {
                $options[] = array('url' => ModUtil::url('MyHookSubscriber', 'admin', 'modify', array('id' => $item['id'])), 'image' => 'xedit.gif', 'title' => $this->__('Edit'));

                if (SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$item['id'], ACCESS_DELETE)) {
                    $options[] = array('url' => ModUtil::url('MyHookSubscriber', 'admin', 'delete', array('id' => $item['id'])), 'image' => '14_layer_deletelayer.gif', 'title' => $this->__('Delete'));
                }
            }

            $item['options'] = $options;

            $data[] = $item;
        }
        unset($items);

        // assign the item output to the template
        $this->view->assign('data', $data);

        // assign the information required to create the pager
        $this->view->assign('pager', array('numitems' => $itemsTable->countall($params), 'limit' => $limit));

        // Return the output
        return $this->view->fetch('myhooksubscriber_user_view.tpl');
    }

    /**
     * display item
     *
     * @param $args['id'] int the id of the item to display (optional)
     * @return string html string
     */
    public function display($args)
    {
        // get id
        $id = (int)FormUtil::getPassedValue('id', isset($args['id']) ? $args['id'] : 0, 'REQUEST');
        if ($id == 0) {
            return LogUtil::registerArgsError();
        }

        $template = 'myhooksubscriber_user_display.tpl';

        // check if the contents are cached.
        if ($this->view->is_cached($template)) {
            return $this->view->fetch($template);
        }

        // get our table
        $itemsTable = Doctrine_Core::getTable('MyHookSubscriber_Model_Items');

        // get the item
        $item = $itemsTable->find($id);
        if (empty($item)) {
            return LogUtil::registerError($this->__('No such item found.'));
        }

        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', $item['title'].'::'.$id, ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        // assign item to template
        $this->view->assign('item', $item);

        // return output
        return $this->view->fetch($template);
    }
}
