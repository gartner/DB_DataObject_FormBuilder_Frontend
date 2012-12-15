<?php
/**
 * Table Definition for pictures
 */
require_once 'DB/DataObject.php';

class DataObjects_Pictures extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'pictures';            // table name
    public $_database = 'mysql';          // database name (used with database_{*} config)
    public $id;                              // int(11)  not_null primary_key auto_increment group_by
    public $mimetype;                        // varchar(75)  
    public $title;                           // varchar(225)  not_null
    public $date_taken;                      // date(10)  
    public $description;                     // blob(196605)  blob
    public $width;                           // int(11)  not_null group_by
    public $height;                          // int(11)  not_null group_by
    public $added;                           // timestamp(19)  not_null unsigned zerofill timestamp
    public $lastupdate;                      // timestamp(19)  not_null unsigned zerofill

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Pictures',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function validateId()
    {
        return false;
    }

    function validateMimetype()
    {
        return false;
    }

    function validateTitle()
    {
        return false;
    }

    function validateDate_taken()
    {
        return false;
    }

    function validateDescription()
    {
        return false;
    }

    function validateWidth()
    {
        return false;
    }

    function validateHeight()
    {
        return false;
    }

    function validateAdded()
    {
        return false;
    }

    function validateLastupdate()
    {
        return false;
    }
}
