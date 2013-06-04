<?php
/**
 * Vehicle Fits
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@vehiclefits.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Vehicle Fits to newer
 * versions in the future. If you wish to customize Vehicle Fits for your
 * needs please refer to http://www.vehiclefits.com for more information.
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Select extends Zend_Db_Select
{
    /** @var VF_Schema */
    protected $schema;
    const DEFINITIONS = 'definitions';
    const MAPPINGS = 'mappings';

    function joinAndSelectLevels($fromTable = null, $levels = array(), $schema = null)
    {
        $this->schema = $schema ? $schema : new VF_Schema;
        switch ($fromTable) {
            case self::DEFINITIONS:
                $fromTable = $this->getSchema()->definitionTable();
                break;
            case null:
            case self::MAPPINGS;
                $fromTable = $this->getSchema()->mappingsTable();
                break;
            default:
                // assume they passed in a literal string
                break;
        }

        if (array() == $levels) {
            $levels = $this->getSchema()->getLevels();
        }
        foreach ($levels as $level) {
            $level = str_replace(' ', '_', $level);
            $table = 'elite_level_' . $this->getSchema()->id() . '_' . $level;
            $condition = "{$table}.id = {$fromTable}.{$level}_id";
            $this->joinLeft($table, $condition, array($level => 'title', $level . '_id' => 'id'));
            $this->order("$table.title");
        }
        return $this;
    }

    function whereLevelIdsEqual($levelIds)
    {
        foreach ($levelIds as $level => $id) {
            if ($id == false) {
                continue;
            }
            $this->where($this->inflect($level) . '_id = ?', $id);
        }
        return $this;
    }

    function getSchema()
    {
        return $this->schema;
    }

    function inflect($identifier)
    {
        return str_replace(' ', '_', $identifier);
    }
}
