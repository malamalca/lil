<?php
	$config = ['Lil' => [
		'appTitle'            => 'Lil',
		
		'authModel'           => 'User',
		'authFields'          => ['username' => 'username', 'password' => 'passwd'],
		'enablePasswordReset' => true,
		'enableRegistration'  => true,
		
		'userDisplayField'    => 'name',
		'userEmailField'      => 'email',
		'passwordResetField'  => 'reset_key',
		
		'from' => ['email' => 'info@lil.si', 'name' => 'Lil'],
		
		'xml2pdf' => [
            'binary' => 'D:\bin\wkhtmltopdf\bin\wkhtmltopdf.exe',
            'no-outline',         // Make Chrome not complain
            'print-media-type',
            'margin-top'    => 0,
            'margin-right'  => 0,
            'margin-bottom' => 0,
            'margin-left'   => 0,
        
            // Default page options
            'disable-smart-shrinking',
            'user-style-sheet' => dirname(dirname(__FILE__)) . DS . 'webroot' . DS . 'css' . DS . 'lil_pdf.css',
    	]
		
		//'dateFormat'          => 'YMD',
		//'dateSeparator'       => '-',
		//'timeFormat'          => '12',
	]];