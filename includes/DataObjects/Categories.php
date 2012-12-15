<?php
/**
 * Table Definition for categories
 */
require_once 'DB/DataObject.php';

class DataObjects_Categories extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'categories';          // table name
    public $_database = 'mysql';        // database name (used with database_{*} config)
    public $id;                              // int(11)  not_null primary_key auto_increment group_by
    public $name;                            // varchar(225)  
    public $description;                     // blob(196605)  blob

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Categories',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function validateId()
    {
        return false;
    }

    function validateName()
    {
        return false;
    }

    function validateDescription()
    {
        return false;
    }
}
