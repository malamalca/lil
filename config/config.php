<?php

use Cake\Core\Configure;

$config['Lil'] = array_merge(
    [
        'appTitle' => 'Lil',

        'authModel' => 'User',
        'authFields' => ['username' => 'username', 'password' => 'passwd'],
        'enablePasswordReset' => true,
        'enableRegistration' => true,

        'usersTable' => 'users',
        'userDisplayField' => 'name',
        'userEmailField' => 'email',
        'passwordResetField' => 'reset_key',

        'from' => ['email' => 'info@lil.si', 'name' => 'Lil'],

        'layout' => 'Lil.lil',

        'pdfEngine' => 'TCPDF',
        'pdfOptions' => [
            'pagePre' => '',
            'pagePost' => '',
            'footerHtml' => '',
            'headerHtml' => ''
        ],
        'TCPDF' => [],
        'WKHTML2PDF' => [
            'binary' => 'C:\bin\wkhtmltopdf\bin\wkhtmltopdf.exe',
            'no-outline', // Make Chrome not complain
            'print-media-type',
            'dpi' => 96,
            'margin-top' => 30,
            'margin-right' => 0,
            'margin-bottom' => 20,
            'margin-left' => 0,

            // Default page options
            'disable-smart-shrinking',
            'user-style-sheet' => dirname(dirname(__FILE__)) . DS . 'webroot' . DS . 'css' . DS . 'lil_pdf.css',
        ],

        'legacyDateFields' => false,
        'dateFormat' => 'YMD',
        'dateSeparator' => '-',
        'timeFormat' => '12',

        'ownerIdField' => 'company_id',

        'userLevelField' => 'privileges',
        'userLevelRoot' => 2,
        'userLevelAdmin' => 5,
    ],
    (array)Configure::read('Lil')
);

return $config;
