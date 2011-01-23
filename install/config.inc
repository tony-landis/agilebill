<?php

    # Main path settings, include trailing slashes!
    define ('PATH_AGILE',           '%%PATH_AGILE%%');
    
    # Base site for cookies, example: '.domain.com'
    define ('AGILE_COOKIE',			'');
    
    # Enable multiple simultaneous sessions for a single user
    define ('ALLOW_MULTI_LOGIN', 	false);

    # Database settings
    define ('AGILE_DB_LIMIT',       '25' );
    define ('AGILE_DB_TYPE',        '%%AGILE_DB_TYPE%%' );
    define ('AGILE_DB_HOST',        '%%AGILE_DB_HOST%%' );
    define ('AGILE_DB_DATABASE',    '%%AGILE_DB_DATABASE%%' );
    define ('AGILE_DB_USERNAME',    '%%AGILE_DB_USERNAME%%' );
    define ('AGILE_DB_PASSWORD',    '%%AGILE_DB_PASSWORD%%' );
    define ('AGILE_DB_PREFIX',      '%%AGILE_DB_PREFIX%%' );
    define ('AGILE_DB_PORT',        '' );
    define ('AGILE_DB_PCONNECT',    false); 

    # No need to change anything after this line!
    define ('DEFAULT_SITE',         '%%AGILE_DEFAULT_SITE%%');
    define ('DEF_THEME_N',          'default');
    define ('PATH_CORE',            PATH_AGILE . 'modules/core/');
    define ('PATH_MODULES',         PATH_AGILE . 'modules/');
    define ('PATH_LANGUAGE',        PATH_AGILE . 'language/');
    define ('PATH_THEMES',          PATH_AGILE . 'themes/');
    define ('PATH_PLUGINS',         PATH_AGILE . 'plugins/');
    define ('PATH_INCLUDES',        PATH_AGILE . 'includes/');
    define ('AGILE_PDF_INVOICE_PATH',PATH_AGILE . 'includes/files/');
    define ('PATH_FILES',           PATH_AGILE . 'includes/files/');
    define ('PATH_IMAGES',          PATH_AGILE . 'includes/images/');
    define ('URL_IMAGES',           'includes/images/');
    define ('PATH_ADODB',           PATH_AGILE . 'includes/adodb/');
    $ADODB_CACHE_DIR    =           PATH_ADODB . 'cache';
    define ('PATH_FPDF',            PATH_AGILE . 'includes/pdf/');
    define ('PATH_FPDF_TMP',        PATH_AGILE . 'includes/pdf/tmp/');
    define ('FPDF_FONTPATH',        PATH_AGILE . 'includes/pdf/font/');
    define ('PATH_SMARTY',          PATH_AGILE . 'includes/smarty/');
    define ('PATH_GRAPH',           PATH_AGILE . 'includes/jpgraph/');
    //define('SMARTY_DIR', PATH_SMARTY);

    if (isset($REMOTE_ADDR))         define ('USER_IP', $REMOTE_ADDR);
    elseif (isset($REMOTE_HOST))    define ('USER_IP', $REMOTE_HOST);
    elseif (getenv('HTTP_X_FORWARDED_FOR')) define ('USER_IP', getenv('HTTP_X_FORWARD_FOR'));
    else                             define ('USER_IP', getenv('REMOTE_ADDR'));

    if (!extension_loaded('gd'))     define('GD', false);
    else                             define('GD', true);

    $_PAGE_SHORTCUTS = Array (      Array ( 's' => '_news',       'p' => 'newsletter:newsletter' ),
                                    Array ( 's' => '_affiliate',  'p' => 'affiliate:affiliate' ),
                                    Array ( 's' => '_contact',    'p' => 'staff:staff' ),
                                    Array ( 's' => '_ticket',     'p' => 'ticket:ticket' ),
                                    Array ( 's' => '_account',    'p' => 'account:account' ),
                                    Array ( 's' => '_products',   'p' => 'product:cat' ),
                                    Array ( 's' => '_product',    'p' => 'product:details' ),
                                    Array ( 's' => '_cart',       'p' => 'cart:cart' ),
                                    Array ( 's' => '_checkout',   'p' => 'checkout:checkout' ),
                                    Array ( 's' => '_static',     'p' => 'static_page:show' )
                               );
?>