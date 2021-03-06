<?php
/*
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * SimpleCommandDispatcher.inc
 *
 * @category     Addons
 * @package      Addons_Dispatcher
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      3.0.1
 * @lastmodified $Date: 2017-03-03 22:38:16 +0100 (Fr, 03. Mrz 2017) $
 * @since        File available since 17.12.2015
 * @description  xyz
 */

//    require (dirname(__DIR__).'/SimpleRegister.php');

    // detect if system running backend or frontend is already set by SimpleRegister
//    $oApp = (isset($GLOBALS['admin']) ? $GLOBALS['admin'] : $GLOBALS['wb']);
    $bIsBackend = ($oApp instanceof admin);
    // set addon depending path / url
    $sAddonPath = $oReg->AppPath.'modules/'.$sAddonName;
    $sAddonUrl  = $oReg->AppUrl.'modules/'.$sAddonName;
    $sAddonRel = '/modules/'.basename(__DIR__);
    $sAddonThemeRel ='/themes/default';
    // define the theme to use -----------------------------------------------------------
    if (is_readable($sAddonPath.'/themes/default')) {
    // first set fallback to system default theme
        $sAddonThemePath = $sAddonPath.'/themes/default';
        $sAddonThemeUrl  = $sAddonUrl.'/themes/default';
    }
    if (is_readable($sAddonPath.'/themes/'.$oReg->DefaultTheme)) {
    // overload with the selected theme if accessible
        $sAddonThemePath = $sAddonPath.'/themes/'.$oReg->DefaultTheme;
        $sAddonThemeUrl  = $sAddonUrl.'/themes/'.$oReg->DefaultTheme;
    }
    // define the template to use --------------------------------------------------------
    if (is_readable($sAddonPath.'/templates/default')) {
        // first set fallback to system default template
        $sAddonTemplatePath = $sAddonPath.'/templates/default';
        $sAddonTemplateUrl  = $sAddonUrl.'/templates/default';
    }
    if (is_readable($sAddonPath.'/templates/'.$oReg->DefaultTemplate)) {
        // try setting to the template of global settings
        $sAddonTemplatePath = $sAddonPath.'/templates/'.$oReg->DefaultTemplate;
        $sAddonTemplateUrl  = $sAddonUrl.'/templates/'.$oReg->DefaultTemplate;
    }
    if (!$bIsBackend && is_readable($sAddonPath.'/templates/'.$oReg->Template)) {
        // try setting to the template of page depending settings
        $sAddonTemplatePath = $sAddonPath.'/templates/'.$oReg->Template;
        $sAddonTemplateUrl  = $sAddonUrl.'/templates/'.$oReg->Template;
    }
        // load core depending language file ------------------------------------------------
        if(is_readable($oReg->AppPath.'/languages/EN.php') ){
            include($oReg->AppPath.'languages/EN.php');
        }
        if(is_readable($oReg->AppPath.'/languages/'.$oReg->Language.'.php') ){
            include($oReg->AppPath.'/languages/'.$oReg->Language.'.php');
        }
    // load addon depending language file ------------------------------------------------
    if (is_readable($sAddonPath.'/languages/EN.php')) {
        // first load fallback to system default language (EN)
        include $sAddonPath.'/languages/EN.php';
    }
    if (is_readable($sAddonPath.'/languages/'.$oReg->DefaultLanguage.'.php')) {
        // try loading language of global settings
        include $sAddonPath.'/languages/'.$oReg->DefaultLanguage.'.php';
    }
    if (is_readable($sAddonPath.'/languages/'.$oReg->Language.'.php')) {
        // try loading language of user (backend) or page (frontend) defined settings
        include $sAddonPath.'/languages/'.$oReg->Language.'.php';
    }
    // load addon Theme/Template depending language file ---------------------------------
    $sTmp = ($bIsBackend ? $sAddonThemePath : $sAddonTemplatePath).'/languages/';
    if (is_readable($sTmp.'EN.php')) {
        // first load fallback to system default language (EN)
        include $sTmp.'EN.php';
    }
    if (is_readable($sTmp.$oReg->DefaultLanguage.'.php')) {
        // try loading language of global settings
        include $sTmp.$oReg->DefaultLanguage.'.php';
    }
    if (is_readable($sTmp.$oReg->Language.'.php')) {
        // try loading language of user (backend) or page (frontend) defined settings
        include $sTmp.$oReg->Language.'.php';
    }

    if( !function_exists( 'make_dir' ) ) { require($oReg->AppPath.'/framework/functions.php');  }
    // Simple Command Dispatcher ---------------------------------------------------------
     // Include the ordering class
    if (!class_exists('order')) {
    include $oReg->AppPath.'framework/class.order.php';
    }
    if (!class_exists('admin')) {
        include $oReg->AppPath.'framework/class.admin.php';
    }
    if (!class_exists('Translate')) {
        include $oReg->AppPath.'framework/Translate.php';
    }
    Translate::getInstance ()->enableAddon ('modules\\'.$sAddonName);

    // sanitize command from compatibility file
    $sCommand = (isset($sCommand) ? strtolower($sCommand) : 'overview');
    // sanitize/validate request var 'cmd'
    $sCmd = preg_replace(
        '/[^a-z\/0-1]/siu',
        '',
        (isset($_REQUEST['cmd']) ? strtolower($_REQUEST['cmd']) : '')
    );
    // build valid sCommand string
    if (($sCommand && $sCmd)) {
        if (!preg_match('/^'.$sCommand.'/si', $sCmd)) {
            // concate both arguments if needed
            $sCommand .= '/'.$sCmd;
        } else {
            $sCommand = $sCmd;
        }
$sCmd = '';
    }

/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $sCommand ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
    $sCommand = 'cmd'.str_replace( // remove spaces and add prefix 'cmd'
        ' ', '',
        ucfirst( // make first char of every word to uppercase
            str_replace( // change '/' to space
                '/', ' ',
                preg_replace( // change leading 'add/' to 'modify/'
                    '/^add\//s',
                    'modify/',
                    trim(($sCommand ?: $sCmd), '/') // remove leading and trailing slashes
                )
            )
        )
    );

    // execute command -------------------------------------------------------------------
    if (is_readable($sAddonPath.'/cmd/'.$sCommand.'.inc') ) {
        include($sAddonPath.'/cmd/'.$sCommand.'.inc');
    } else {
        throw new Exception('call of invalid command ['.$sCommand.'] for [modules/'.$sAddonName.'] failed!');
    }
*/

// end of file