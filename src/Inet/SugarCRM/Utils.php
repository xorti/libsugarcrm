<?php
/**
 * InetETL
 *
 * PHP Version 5.3 -> 5.6
 * SugarCRM Versions 6.5 - 7.6
 *
 * @author Emmanuel Dyan
 * @copyright 2005-2015 iNet Process
 * @package iNetETL
 * @license GNU General Public License v2.0
 * @link http://www.inetprocess.com
 */

namespace Inet\SugarCRM;

/**
 * SugarCRM Utils for language and dropdown management
 *
 * @todo Unit Tests
 */
class Utils
{
    /**
     * Prefix that should be set by each class to identify it in logs
     * @var    string
     */
    protected $logPrefix;
    /**
     * Logger, inherits PSR\Log and uses Monolog
     * @var    Inet\Util\Logger
     */
    protected $log;


    /**
     * Set the LogPrefix to be unique and ask for an Entry Point to SugarCRM
     * @param    EntryPoint    $entryPoint    Enters the SugarCRM Folder
     */
    public function __construct(EntryPoint $entryPoint)
    {
        $this->logPrefix = __CLASS__ . ': ';
        $this->log = $entryPoint->getLogger();
    }

    /**
     * Encode a multienum from Sugar
     * @param     string    $multiselect    MultiEnum from DB
     * @return    array                     Multienum as Array
     */
    public function arrayToMultiselect($values)
    {
        if (!is_array($values)) {
            throw new \InvalidArgumentException('The values provided to arrayToMultiselect are not in an Array');
        }
        // First clean the array
        foreach ($values as $k => $v) {
            if (empty($v)) {
                unset($values[$k]);
            }
        }

        // Encode
        $values = (empty($values) ? '^^' : encodeMultienumValue($values));

        // Then return the array
        return $values;
    }

    /**
     * Decode a multienum from Sugar
     * @param     string    $multiselect    MultiEnum from DB
     * @return    array                     Multienum as Array
     */
    public static function multiselectToArray($values)
    {
        // Unencode
        $values = unencodeMultienum($values);

        // First clean the array
        foreach ($values as $k => $v) {
            if (empty($v)) {
                unset($values[$k]);
            }
        }

        // Then return the array
        return $values;
    }


    /**
     * Add a label for a specific module in SugarCRM
     * @param    string    $module
     * @param    string    $language
     * @param    string    $label
     * @param    string    $value
     * @return   bool
     */
    public function addLabel($module, $language, $label, $value)
    {
        require_once('modules/ModuleBuilder/parsers/parser.label.php');
        $addLabels = \ParserLabel::addLabels($language, array($label => $value), $module);

        return $addLabels;
    }


    /**
     * Add a dropdown in SugarCRM
     * @param    string    $name        Dropdown's name
     * @param    array     $values      Values for the dropdown
     * @param    string    $lang        Language
     * @return   void
     */
    public function addDropdown($name, array $values, $lang)
    {
        require_once('modules/ModuleBuilder/MB/ModuleBuilder.php');
        require_once('modules/ModuleBuilder/parsers/parser.dropdown.php');
        $_REQUEST['view_package'] = 'studio'; //need this in parser.dropdown.php

        $parser = new \ParserDropDown();
        $json = \getJSONobj();
        // Change the values to array of arrays
        $dpValues = array();
        foreach ($values as $key => $value) {
            $dpValues[] = array($key, $value);
        }

        $params = array(
            'view_package' => 'studio',
            'dropdown_name' => $name,
            'list_value' => $json->encode($dpValues),
            'dropdown_lang' => $lang,
        );

        $parser->saveDropDown($params);
    }
}
