<?php
global $oDb, $wb, $TEXT, $DGTEXT,$section_id,$page_id;
$content = '';
$sid = isset($sid) ? intval($sid) : 0;
if( $sid ) {
$oldSid = $section_id; // save old sectionID
$sql  = 'SELECT `module` FROM `'.TABLE_PREFIX.'sections` ';
$sql .= 'WHERE `section_id`='.$sid;
if (($module = $oDb->get_one($sql))) {
if (is_readable($oReg->AppPath.'/modules/'.$module.'/view.php')) {
$_sFrontendCss = '/modules/'.$module.'/frontend.css';
if(is_readable($oReg->AppPath.$_sFrontendCss)) {
$_sSearch = preg_quote(WB_URL.'/modules/'.$module.'/frontend.css', '/');
if(preg_match('/<link[^>]*?href\s*=\s*\"'.$_sSearch.'\".*?\/>/si', $wb_page_data)) {
$_sFrontendCss = '';
} else {
$_sFrontendCss = '<link href="'.WB_URL.$_sFrontendCss.'" rel="stylesheet" type="text/css" media="screen" />';
}
} else { $_sFrontendCss = ''; }
$section_id = $sid;
ob_start();
require($oReg->AppPath.'/modules/'.$module.'/view.php');
$content = $_sFrontendCss.ob_get_clean();
$section_id = $oldSid; // restore old sectionID
}
}
}
return $content;
