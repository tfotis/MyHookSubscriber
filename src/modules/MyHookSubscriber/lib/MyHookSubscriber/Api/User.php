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

class MyHookSubscriber_Api_User extends Zikula_Api
{
    /**
     * get a specific item
     * @param $args['id'] int id of item to get
     * @return item array
     */
    public function get($args)
    {
        // Argument check
        if ((!isset($args['id']) || empty($args['id']) || !is_numeric($args['id']))) {
            LogUtil::registerArgsError();
            return array();
        }

        $item = Doctrine_Query::create()
                              ->select()
                              ->from('MyHookSubscriber_Model_Table')
                              ->where('id = ?', $args['id'])
                              ->fetchOne();
        
        if ($item) {
            return $item->toArray();
        } else {
            return array();
        }
    }

    /**
     * get all items
     * @param $args['offset'] int   offset of records
     * @param $args['limit'] int    limit number of items
     * @return array of items
     */
    public function getall($args)
    {
        // Optional arguments.
        if (!isset($args['offset']) || empty($args['offset'])) {
            $args['offset'] = 1;
        }
        if (!isset($args['limit']) || empty($args['limit'])) {
            $args['limit'] = 0;
        }

        if (!is_numeric($args['offset']) || !is_numeric($args['limit'])) {
            LogUtil::registerArgsError();
            return array();
        }

        // Security check
        if (!SecurityUtil::checkPermission('MyHookSubscriber::', '::', ACCESS_READ)) {
            LogUtil::registerPermissionError();
            return array();
        }

        $objArray = Doctrine_Query::create()
                               ->select()
                               ->from('MyHookSubscriber_Model_Table')
                               ->offset($args['offset']-1)
                               ->limit($args['limit'])
                               ->execute()
                               ->toArray();

        // return the items
        return $objArray;
    }

    /**
     * utility function to count the number of items held by this module
     * @param none
     * @return integer number of items
     */
    public function countitems($args)
    {   
        $count = Doctrine_Query::create()
                               ->select()
                               ->from('MyHookSubscriber_Model_Table')
                               ->count();

        return $count;
    }
}