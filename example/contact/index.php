<?php
require_once 'setup.php';
require_once 'top.php';

?>
<h1>The author</h1>
<p>My name is Mads Lie Jensen, lives in the northern part of denmark. To get in touch with me, see my <a href="https://github.com/gartner">github page</a></p>

<h1>The package</h1>
<p>After some time of php, I found that it was a lot of the same thing I was doing - building forms to edit tables from a database. Mostly the same, over and over again. It must be possible to make that easier? So I started my own attemptof something that made me write less off the boring code.</p>

<p>Some time later I came around <a href="https://pear.php.net/package/DB_DataObject_FormBuilder">DB_DataObject_FormBuilder</a> from <a href="http://pear.php.net/">PEAR</a>. Automatic build forms, sounded just like what I needed. But then I had to use <a href="https://pear.php.net/package/DB_DataObject">DB_DataObject</a> also. Put some time into learning that one. And I liked working with DataObjects. But after some time with that, again I felt like doing the same semi-boring stuff over and over again. Wouldn't it be nice if I could just write a few values in a config-file to get that result?</p>

<p>That was the start of DB_DataObject_FormBuilder_Frontend.</p>

<p>This package can now be found on <a href="https://github.com/gartner/DB_DataObject_FormBuilder_Frontend">Github</a>. Feel free to fork it, use it, send patches or contact me.</p>


<?php
require_once 'foot.php';
