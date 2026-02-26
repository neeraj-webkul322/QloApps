<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class IndexControllerCore extends FrontController
{
    public $php_self = 'index';

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'home-background-media.css');
    }

    protected function normalizeBackgroundMediaType($type)
    {
        $type = Tools::strtolower(trim((string) $type));
        if (!in_array($type, array('image', 'slider', 'video'))) {
            return 'image';
        }

        return $type;
    }

    protected function buildHotelReservationModuleMediaUrl($relativePath)
    {
        $relativePath = ltrim((string) $relativePath, '/');
        return $this->context->link->getMediaLink(_MODULE_DIR_.'hotelreservationsystem/'.$relativePath);
    }

    protected function resolveBackgroundImageUrl($imagePath)
    {
        $imagePath = trim((string) $imagePath);
        if (!$imagePath) {
            return '';
        }

        if (Validate::isAbsoluteUrl($imagePath)) {
            return $imagePath;
        }

        if (strpos($imagePath, 'views/') === 0) {
            return $this->buildHotelReservationModuleMediaUrl($imagePath);
        }

        return $this->context->link->getMediaLink(_PS_IMG_.$imagePath);
    }

    protected function getSliderBackgroundImages()
    {
        $raw = Configuration::get('WK_BG_SLIDER_IMAGES');
        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return array();
        }

        $images = array();
        foreach ($decoded as $row) {
            if (!is_array($row) || !isset($row['path']) || !$row['path']) {
                continue;
            }

            $path = trim((string) $row['path']);
            if (!$path) {
                continue;
            }

            $images[] = array(
                'id' => isset($row['id']) ? (int) $row['id'] : 0,
                'position' => isset($row['position']) ? (int) $row['position'] : 0,
                'url' => $this->buildHotelReservationModuleMediaUrl($path),
            );
        }

        usort($images, function ($a, $b) {
            if ($a['position'] === $b['position']) {
                return 0;
            }
            return ($a['position'] < $b['position']) ? -1 : 1;
        });

        return $images;
    }

    protected function getYoutubeEmbedUrl($url)
    {
        if (!$url || !Validate::isUrl($url)) {
            return '';
        }

        $parts = parse_url($url);
        if (!isset($parts['host'])) {
            return '';
        }

        $host = Tools::strtolower($parts['host']);
        $videoId = '';

        if (strpos($host, 'youtu.be') !== false && !empty($parts['path'])) {
            $videoId = trim($parts['path'], '/');
        } elseif (strpos($host, 'youtube.com') !== false) {
            if (!empty($parts['path']) && strpos($parts['path'], '/embed/') === 0) {
                $videoId = str_replace('/embed/', '', $parts['path']);
            } elseif (!empty($parts['query'])) {
                parse_str($parts['query'], $queryVars);
                if (!empty($queryVars['v'])) {
                    $videoId = $queryVars['v'];
                }
            }
        }

        $videoId = trim((string) $videoId);
        if (!$videoId) {
            return '';
        }

        return 'https://www.youtube.com/embed/'.urlencode($videoId).'?autoplay=1&mute=1&loop=1&playlist='.urlencode($videoId).'&controls=0&rel=0';
    }

    protected function getVimeoEmbedUrl($url)
    {
        if (!$url || !Validate::isUrl($url)) {
            return '';
        }

        $parts = parse_url($url);
        if (!isset($parts['host']) || strpos(Tools::strtolower($parts['host']), 'vimeo.com') === false) {
            return '';
        }

        $path = isset($parts['path']) ? trim($parts['path'], '/') : '';
        if (!$path) {
            return '';
        }

        if (preg_match('/([0-9]+)/', $path, $matches)) {
            return 'https://player.vimeo.com/video/'.(int) $matches[1].'?autoplay=1&muted=1&loop=1&background=1';
        }

        return '';
    }

    protected function getVideoBackgroundData()
    {
        $videoPath = trim((string) Configuration::get('WK_BG_VIDEO'));
        $youtubeUrl = trim((string) Configuration::get('WK_BG_VIDEO_YOUTUBE'));
        $vimeoUrl = trim((string) Configuration::get('WK_BG_VIDEO_VIMEO'));

        $videoData = array(
            'provider' => '',
            'source_url' => '',
            'embed_url' => '',
            'youtube_url' => $youtubeUrl,
            'vimeo_url' => $vimeoUrl,
        );

        if ($videoPath) {
            if (Validate::isAbsoluteUrl($videoPath)) {
                $videoData['provider'] = 'file';
                $videoData['source_url'] = $videoPath;
            } elseif (strpos($videoPath, 'views/') === 0) {
                $videoData['provider'] = 'file';
                $videoData['source_url'] = $this->buildHotelReservationModuleMediaUrl($videoPath);
            }
        }

        if (!$videoData['provider'] && $youtubeUrl) {
            $youtubeEmbedUrl = $this->getYoutubeEmbedUrl($youtubeUrl);
            if ($youtubeEmbedUrl) {
                $videoData['provider'] = 'youtube';
                $videoData['embed_url'] = $youtubeEmbedUrl;
            }
        }

        if (!$videoData['provider'] && $vimeoUrl) {
            $vimeoEmbedUrl = $this->getVimeoEmbedUrl($vimeoUrl);
            if ($vimeoEmbedUrl) {
                $videoData['provider'] = 'vimeo';
                $videoData['embed_url'] = $vimeoEmbedUrl;
            }
        }

        return $videoData;
    }

    protected function assignHomeBackgroundMedia()
    {
        $configuredType = $this->normalizeBackgroundMediaType(Configuration::get('WK_BG_MEDIA_TYPE'));
        $configuredImage = trim((string) Configuration::get('WK_BG_IMAGE'));
        if (!$configuredImage) {
            $configuredImage = trim((string) Configuration::get('WK_HOTEL_HEADER_IMAGE'));
        }

        $imageUrl = $this->resolveBackgroundImageUrl($configuredImage);
        $sliderImages = $this->getSliderBackgroundImages();
        $videoData = $this->getVideoBackgroundData();

        $resolvedType = 'image';
        if ($configuredType === 'slider' && !empty($sliderImages)) {
            $resolvedType = 'slider';
        } elseif ($configuredType === 'video' && !empty($videoData['provider'])) {
            $resolvedType = 'video';
        } elseif ($configuredType === 'image' && $imageUrl) {
            $resolvedType = 'image';
        } elseif ($imageUrl) {
            $resolvedType = 'image';
        } elseif (!empty($sliderImages)) {
            $resolvedType = 'slider';
        } elseif (!empty($videoData['provider'])) {
            $resolvedType = 'video';
        }

        $sliderUrls = array();
        foreach ($sliderImages as $sliderImage) {
            $sliderUrls[] = $sliderImage['url'];
        }

        $headerStyle = 'background-color:#252525;';
        if ($resolvedType === 'image' && $imageUrl) {
            $headerStyle = 'background-image:url("'.$imageUrl.'"); height:100%;';
        } elseif ($resolvedType === 'slider' && !empty($sliderUrls)) {
            $headerStyle = 'background-color:#000; height:100%;';
        } elseif ($resolvedType === 'video') {
            $headerStyle = 'background-color:#000; height:100%;';
        }

        $backgroundData = array(
            'configured_type' => $configuredType,
            'resolved_type' => $resolvedType,
            'header_style' => $headerStyle,
            'image_url' => $imageUrl,
            'slider_images' => $sliderImages,
            'video' => $videoData,
            'slider_interval' => 5000,
        );

        Media::addJsDef(
            array(
                'wkHomeBackgroundMedia' => array(
                    'type' => $resolvedType,
                    'slides' => $sliderUrls,
                    'interval' => 5000,
                    'activeIndex' => 0,
                ),
            )
        );

        $this->context->smarty->assign('wk_home_background', $backgroundData);
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $this->assignHomeBackgroundMedia();
        $this->addJS(_THEME_JS_DIR_.'index.js');

        $this->context->smarty->assign(array('HOOK_HOME' => Hook::exec('displayHome'),
            'HOOK_HOME_TAB' => Hook::exec('displayHomeTab'),
            'HOOK_HOME_TAB_CONTENT' => Hook::exec('displayHomeTabContent')
        ));
        $this->setTemplate(_PS_THEME_DIR_.'index.tpl');
    }
}
