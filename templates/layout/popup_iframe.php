<?php

// if redirect happens in popup, set flash template to Lil.Flash/popup
$flashOptions = [];
if (!empty($popupRedirect)) $flashOptions = ['element' => 'Lil.Flash' . DS . 'popup'];
$content = $this->Flash->render('flash', $flashOptions);

// add rendered content
$content .= $this->fetch('content');


// add ready scripts
if ($scripts = $this->Lil->jsReadyOut()) {
	$content .= '<script type="text/javascript">' . $scripts . '</script>';
}

echo '{"data": ';
echo json_encode(rawurlencode($content));
echo '}';