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

class MyHookSubscriber_Model_Record extends Doctrine_Record
{
    /**
     * Set table definitions.
     *
     * @return void
     */
    public function setTableDefinition()
    {
        $this->setTableName('myhooksubscriber');

        $this->hasColumn(
            'id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true)
        );

        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
    {   
        $this->actAs('Zikula_Doctrine_Template_StandardFields');
    }
}