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

class MyHookSubscriber_Model_ItemsTable extends Doctrine_Table
{
    public function construct()
    {
        //
    }

    public function getall($args)
    {
        // Optional arguments.
        if (!isset($args['offset']) || empty($args['offset'])) {
            $args['offset'] = 1;
        }
        if (!isset($args['limit']) || empty($args['limit'])) {
            $args['limit'] = 0;
        }

        // create the query
        $query = Doctrine_Query::create()
                               ->from('MyHookSubscriber_Model_Items')
                               ->offset($args['offset']-1)
                               ->limit($args['limit']);

        if (isset($args['cid'])) {
            $query->where('MyHookSubscriber_Model_Items.Categories.category_id = ?', $args['cid']);
        }

        //echo $query->getSqlQuery();

        // return array of data
        return $query->fetchArray();
    }

    public function countall($args)
    {
        $query = Doctrine_Query::create()
                               ->select('COUNT(*) AS total_items')
                               ->from('MyHookSubscriber_Model_Items');

        if (isset($args['cid'])) {
            $query->where('MyHookSubscriber_Model_Items.Categories.category_id = ?', $args['cid']);
        }

        //echo $query->getSqlQuery();

        // return total items
        return (int)$query->fetchOne()->total_items;
    }

    public function save($data)
    {
        // if our id is empty, unset it
        if (empty($data['id'])) {
            unset($data['id']);
        }

        if (isset($data['id'])) {
            $item = $this->find($data['id']);
        } else {
            $item = $this->create();
        }

        $item->merge($data);

        try {
            $item->save();
        } catch(Doctrine_Validator_Exception $e) {
            var_dump($item->getErrorStack());
            System::shutdown();
        }

        return $item->id;
    }
}