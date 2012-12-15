<?php
/**
 * Table Definition for pictures_categories
 */
require_once 'DB/DataObject.php';

class DataObjects_Pictures_categories extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'pictures_categories';    // table name
    public $_database = 'mysql';    // database name (used with database_{*} config)
    public $id;                              // int(11)  not_null primary_key auto_increment group_by
    public $category_id;                     // int(11)  group_by
    public $picture_id;                      // int(11)  group_by

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Pictures_categories',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function validateId()
    {
        return false;
    }

    function validateCategory_id()
    {
        return false;
    }

    function validatePicture_id()
    {
        return false;
    }
}
