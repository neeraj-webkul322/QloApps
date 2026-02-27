<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class HeaderSliderImage extends ObjectModel
{
    public $id;
    public $image_path;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'header_slider_images',
        'primary' => 'id_header_slider_image',
        'fields' => array(
            'image_path' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function getAllSliderImages()
    {
        self::ensureTableExists();
        return Db::getInstance()->executeS(
            'SELECT `id_header_slider_image`, `image_path`, `position`
            FROM `'._DB_PREFIX_.'header_slider_images`
            ORDER BY `position` ASC'
        );
    }

    public static function getMaxPosition()
    {
        self::ensureTableExists();
        return (int) Db::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'header_slider_images`'
        );
    }

    public static function ensureTableExists()
    {
        static $ensured = false;
        if ($ensured) {
            return true;
        }

        $result = Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'header_slider_images` (
                `id_header_slider_image` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `image_path` varchar(255) NOT NULL,
                `position` int(11) unsigned NOT NULL DEFAULT 0,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_header_slider_image`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1'
        );
        $ensured = (bool) $result;

        return $ensured;
    }
}
