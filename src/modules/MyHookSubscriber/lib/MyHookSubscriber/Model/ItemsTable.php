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

        //echo $query->getSqlQuery();

        // return array of data
        return $query->fetchArray();
    }

    public function save($data)
    {
        // type cast our id
        $data['id'] = (int)$data['id'];

        if ($data['id'] > 0) {
            $item = $this->find($data['id']);
        } else {
            $item = $this->create();
        }
        
        $item->merge($data);

        try {
            $item->save();
        } catch(Doctrine_Validator_Exception $e) {
            var_dump($item->getErrorStack());
        }

       return $item->id;
    }
}