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
 *
 * See http://www.doctrine-project.org/projects/orm/1.2/docs/manual/defining-models/en for examples.
 */

class MyHookSubscriber_Model_Items extends Doctrine_Record
{
    /**
     * Set table definitions.
     *
     * @return void
     */
    public function setTableDefinition()
    {
        $this->setTableName('myhooksubscribers');

        $this->hasColumn('id', 'integer', 4, array('fixed' => true, 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('title', 'string', 255, array('fixed' => false, 'primary' => false, 'notnull' => true, 'autoincrement' => false));
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