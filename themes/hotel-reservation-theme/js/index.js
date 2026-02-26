/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function(){
	$('#home-page-tabs li:first, #index .tab-content ul:first').addClass('active');

    if (typeof wkHomeBackgroundMedia === 'undefined' || !wkHomeBackgroundMedia) {
        return;
    }

    if (wkHomeBackgroundMedia.type !== 'slider') {
        return;
    }

    var $sliderLayer = $('#wk-home-bg-slider-layer');
    var $slides = $sliderLayer.find('.wk-home-bg-slide');
    if (!$slides.length) {
        return;
    }

    if ($slides.length === 1) {
        $slides.eq(0).addClass('active');
        return;
    }

    var currentIndex = parseInt(wkHomeBackgroundMedia.activeIndex, 10);
    var interval = parseInt(wkHomeBackgroundMedia.interval, 10);

    if (isNaN(currentIndex) || currentIndex < 0 || currentIndex >= $slides.length) {
        currentIndex = 0;
    }

    if (isNaN(interval) || interval < 1000) {
        interval = 5000;
    }

    $slides.removeClass('active').eq(currentIndex).addClass('active');
    window.setInterval(function () {
        var nextIndex = (currentIndex + 1) % $slides.length;
        $slides.eq(currentIndex).removeClass('active');
        $slides.eq(nextIndex).addClass('active');
        currentIndex = nextIndex;
    }, interval);
});
