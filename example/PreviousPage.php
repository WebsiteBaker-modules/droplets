//:Create a previous link to your page
//:Display a link to the previous page on the same menu level
$info = show_menu2(0, SM2_CURR, SM2_START, SM2_ALL|SM2_BUFFER, '[if(class==menu-current){[level] [sib] [sibCount] [parent]}]', '', '', '');
list($nLevel, $nSib, $nSibCount, $nParent) = explode(' ', $info);
// show previous
$prv = $nSib > 1 ? $nSib - 1 : 0;
if ($prv > 0) {
return show_menu2(0, SM2_CURR, SM2_START, SM2_ALL|SM2_BUFFER, "[if(sib==$prv){[a][menu_title]</a> <<}]", '', '', '');
}
else
return '(no previous)';