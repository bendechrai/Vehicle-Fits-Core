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
class VF_SearchLevel
{
    protected $block;
    protected $level;
    protected $prevLevel;
    protected $displayBrTag;

    function display($block, $level, $prevLevel = false, $displayBrTag = null, $yearRangeAlias = null)
    {
        $this->displayBrTag = $displayBrTag;
        $this->block = $block;
        $this->level = $level;
        $this->prevLevel = $prevLevel;
        $this->yearRangeAlias = $yearRangeAlias;
        return $this->_display();
    }

    protected function _display()
    {
        ob_start();
        if ($this->helper()->showLabels() === true || 'true' == strtolower($this->helper()->showLabels()) ) {
            echo '<label>';
            echo $this->__(ucfirst($this->level));
            echo ':</label>';
        }

        $prevLevelsIncluding = $this->schema()->getPrevLevelsIncluding($this->level);
        $prevLevelsIncluding = implode(',', $prevLevelsIncluding);
        ?>
        <select name="<?= $this->selectName() ?>"
                class="<?= $this->selectName() ?>Select {prevLevelsIncluding: '<?= $prevLevelsIncluding ?>'}">
            <option value="0"><?= $this->__($this->helper()->getDefaultSearchOptionText($this->level)) ?></option>
            <?php
            foreach ($this->getEntities() as $entity) {
                ?>
                <option
                    value="<?= $entity->getId() ?>" <?= ($this->getSelected($entity) ? ' selected="selected"' : '') ?>><?= $entity->getTitle() ?></option>
            <?php
            }
            ?>
        </select>
        <?php
        if ($this->displayBrTag()) {
            echo '<br />';
        }
        return ob_get_clean();
    }

    function selectName()
    {
        if ($this->yearRangeAlias) {
            return $this->yearRangeAlias;
        }
        return str_replace(' ', '_', $this->level);
    }

    function schema()
    {
        return new VF_Schema();
    }

    /** @return bool */
    function getSelected($entity)
    {
        $selected = false;
        if ($this->level != $this->leafLevel()) {
            return (bool)($entity->getId() == $this->block->getSelected($this->level));
        }

        VF_Singleton::getInstance()->setRequest($this->block->getRequest());
        $fit = VF_Singleton::getInstance()->vehicleSelection();
        if (false === $fit) {
            return false;
        }


        if ('year_start' == $this->yearRangeAlias) {
            return (bool)($entity->getTitle() == $fit->earliestYear());
        } else if ('year_end' == $this->yearRangeAlias) {
            return (bool)($entity->getTitle() == $fit->latestYear());
        }

        $level = $fit->getLevel($this->leafLevel());
        if ($level) {
            return (bool)($entity->getTitle() == $level->getTitle());
        }
    }

    protected function getEntities()
    {
        $search = $this->block;
        if ($this->prevLevel) {
            return $search->listEntities($this->level);
        }
        return $search->listEntities($this->level);
    }

    protected function leafLevel()
    {
        return $this->schema()->getLeafLevel();
    }

    protected function displayBrTag()
    {
        if (is_bool($this->displayBrTag)) {
            return $this->displayBrTag;
        }
        return VF_Singleton::getInstance()->displayBrTag();
    }

    protected function __($text)
    {
        return $this->block->translate($text);
    }

    protected function helper()
    {
        return VF_Singleton::getInstance();
    }
}
