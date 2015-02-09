<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty from_charset modifier plugin
 * Type:     modifier<br>
 * Name:     from_charset<br>
 * Purpose:  convert character encoding from $charset to internal encoding
 *
 * @author Rodney Rehm
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_from_charset($params)
{
    if (!Smarty::$_MBSTRING) {
        
    }

    if (!isset($params[1])) {
        $params[1] = '"ISO-8859-1"';
    }

    return 'mb_convert_encoding(' . $params[0] . ', "' . addslashes(Smarty::$_CHARSET) . '", ' . $params[1] . ')';
}
