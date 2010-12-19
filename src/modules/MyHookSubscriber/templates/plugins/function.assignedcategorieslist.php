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

/**
 * Retrieve a list of the categories assigned to a specified item.
 *
 * The assigned categories are retrieved from $item['Categories'].
 *
 * Available attributes:
 *  - item  (array) The item from which to retrieve the assigned categories.
 *
 * Example:
 *
 * {assignedcategorieslist item=$myItem}
 *
 * @param array       $params All attributes passed to this function from the template.
 * @param Zikula_View $view   Reference to the {@link Zikula_View} object.
 *
 * @return string The HTML code for the item's assigned categories. 
 *                If no categories are assigned to the item, we return
 *                an appropriate message.
 */
function smarty_function_assignedcategorieslist($params, $view)
{
    if (!isset($params['item'])) {
        $view->trigger_error(__f('Error! in %1$s: the %2$s parameter must be specified.', array('assignedcategorieslist', 'item')));
        return false;
    }

    $lang = ZLanguage::getLanguageCode();

    $result = "";

    if (!empty($params['item']['Categories'])) {
        foreach ($params['item']['Categories'] as $property => $category) {
            $result .= "<div>";
            if (isset($category['Category']['display_name'][$lang])) {
                $result .= $category['Category']['display_name'][$lang];
            } else {
                $result .= $category['Category']['name'];
            }
            $result .= "</div>";
        }
    } else {
        $result .= __('No assigned categories.');
    }

    return $result;
}