<?php
/**
 * Table Definition for pages
 */
require_once 'DB/DataObject.php';

class DataObjects_Pages extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'pages';               // table name
    public $_database = 'mysql';             // database name (used with database_{*} config)
    public $id;                              // int(11)  not_null primary_key auto_increment group_by
    public $title;                           // varchar(225)  not_null
    public $content;                         // blob(196605)  blob
    public $keywords;                        // blob(196605)  blob

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Pages',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function validateId()
    {
        return false;
    }

    function validateTitle()
    {
        return false;
    }

    function validateContent()
    {
        return false;
    }

    function validateKeywords()
    {
        return false;
    }
}
