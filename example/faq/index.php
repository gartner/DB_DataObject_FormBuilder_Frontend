<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mads
 * Date: 13-12-12
 * Time: 18:52
 * To change this template use File | Settings | File Templates.
 */
require_once 'setup.php';
require_once 'top.php';

?>
<h1>Faq</h1>
    <ul id="index"></ul>
<p>&nbsp;</p>
<h4>Why Xml?</h4>
<p>Short answer: I like xml.</p>
    <p>Using xml for this kind of configuration seems to me straight forward and descriptive. There is a DTD for this package, which can make a decent IDE help you writing the configfile. It also enables the package to easily validate the config. This helps catching errors earlier.</p>

        <script type="text/javascript">
        //<![CDATA[

        $(document).ready(
            function (){
                //loop through all your headers
                $.each($('h4'),function(index,value){
                    //append the text of your header to a list item in a div, linking to an anchor we will create on the next line
                    $('#index').append('<li><a href="#anchor-'+index+'">'+$(this).html()+'</a></li>');
                    //add an a tag to the header with a sequential name
                    //$(this).html('<a name="anchor-'+index+'">'+$(this).html()+'</a>');
                    $(this).attr('id', "anchor-"+index);
                });
            }
        );
        //]]>
        </script>
<?php

require_once 'foot.php';
?>