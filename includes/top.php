<?php
/**
 * top of all pages
 */
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="/css/page.css" />
    <?php
    if (!empty($_css)) {
        echo implode("\n\t", (array) $_css) . "\n";
    }
?>
    <!-- Include required JS files -->
    <script type="text/javascript" src="/js/syntaxHighlighter/shCore.js"></script>

    <!--
    At least one brush, here we choose JS. You need to include a brush for every
    language you want to highlight
    -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="/js/syntaxHighlighter/shAutoloader.js"></script>

    <!-- Include *at least* the core style and default theme -->
    <link href="/css/shCore.css" rel="stylesheet" type="text/css" />
    <link href="/css/shThemeDefault.css" rel="stylesheet" type="text/css" />

    <?php
    if (!empty($_style)) { ?>
    <style>
            <?php echo $_style; ?>
    </style>
    <?php
    }
?>
    <title><?php echo (isset($title) ? $title : 'DB_DataObject_FormBuilder_Frontend'); ?></title>
</head>
<body>

<div id="topbg"></div>

<div id="main">
    <a href="https://github.com/gartner/DB_DataObject_FormBuilder_Frontend">
        <img style="position: absolute; top: 0; z-index: 100; left: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_left_red_aa0000.png" alt="Fork me on GitHub">
    </a>
    <div id="header">
        <div id="hdr-overlay"></div>
        <div id="hdr-box1" class="box primary-2"></div>
        <div id="hdr-box2" class="box primary-3"></div>
        <div id="hdr-box3" class="box primary-4"></div>
        <div id="hdr-box4" class="box primary-5"></div>
        <div id="hdr-box5"></div>

        <h1>DB_DataObject_FormBuilder_Frontend</h1>
        <h2></h2>
    </div>

    <?php
    $menu = array(
        'home' => array(
            'url' => '/',
            'regexp' => '@^/$|^/index.php$@',
            'label' => 'Home',
        ),
        'examples' => array(
            'url' => '/examples/',
            'regexp' => '@^/examples/@',
            'label' => 'Examples',
        ),
        'docs' => array(
            'url' => '/docs/',
            'regexp' => '@^/docs/@',
            'label' => 'Docs',
        ),
        'tests' => array(
            'url' => '/tests/',
            'regexp' => '@^/tests/@',
            'label' => 'Tests',
        ),
        'faq' => array(
            'url' => '/faq/',
            'regexp' => '@^/faq/@',
            'label' => 'F.A.Q.',
        ),
        'contact' => array(
            'url' => '/contact/',
            'regexp' => '@^/contact/@',
            'label' => 'Contact',
        ),
    );
    ?>
    <ul id="menu">
        <?php
        foreach ($menu as $item) {
            printf('<li><a href="%s"%s><span></span>%s</a></li>',
                $item['url'],
                (preg_match($item['regexp'], $_SERVER['REQUEST_URI']) ? ' class="sel"' : ''),
                $item['label']
            );
        }
        ?>
    </ul>

    <div id="content">

        <div id="left">

            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-4836130749942390";
            /* DB_DataObject_FormBuilder_Frontend */
            google_ad_slot = "3375136262";
            google_ad_width = 120;
            google_ad_height = 600;
            //-->
</script>
<script type="text/javascript"
        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
        </div>

        <div id="right">
     <!-- end of top.php -->
