DB_DataObject_FormBuilder_Frontend
==================================

A highly configurable frontend for databases, using PEAR::DB_DataObject

How to use this can be found on the webpage http://pear.quercus.palustris.dk/

This entire webpage is included in the repository, for you to install and run on your own server if you want to try the examples.

### How to install ###
To install this on your own webserver, checkout the repository, point the DocumentRoot to the examples/-folder in the root of the checkout.

Then run [Composer](http://getcomposer.org) from the base of the checkout, to install required packages.

In the includes/-folder, rename the file dbpw.php-dist to dbpw.php and edit it to include your database credentials.

If this database is an empty mysql-database, insert the contents of the file /development/mysql.sql into it. This could be done by running the command 'updateMysql.php mysql.sql'

It will be wise to run the 'createDataObjects.php', also located in the developer/-directory, every time you change the structure of your database.

