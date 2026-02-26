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

class AdminHotelBackgroundImageSettingsController extends ModuleAdminController
{
    const BG_TYPE_IMAGE = 'image';
    const BG_TYPE_SLIDER = 'slider';
    const BG_TYPE_VIDEO = 'video';

    public function __construct()
    {
        $this->table = 'configuration';
        $this->className = 'Configuration';
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_title = $this->l('Background Image Settings');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_JS_DIR_.'jquery/plugins/jquery.tablednd.js');
        $this->addJS($this->module->getPathUri().'views/js/admin/background_image_settings.js');
        Media::addJsDef(
            array(
                'wkBgDeleteAjaxUrl' => $this->context->link->getAdminLink('AdminHotelBackgroundImageSettings', true),
                'deleteConfirm' => $this->l('Are you sure want to delete?'),
            )
        );
    }

    public function renderForm()
    {
        $selectedType = $this->normalizeBackgroundType(Configuration::get('WK_BG_MEDIA_TYPE'));
        $storedBackgroundImage = trim((string) Configuration::get('WK_BG_IMAGE'));
        $storedVideoPath = trim((string) Configuration::get('WK_BG_VIDEO'));
        $youtubeUrl = trim((string) Configuration::get('WK_BG_VIDEO_YOUTUBE'));
        $vimeoUrl = trim((string) Configuration::get('WK_BG_VIDEO_VIMEO'));
        $imagePreviewHtml = '';
        $videoPreviewHtml = '';

        if ($storedBackgroundImage) {
            $backgroundImageUrl = $this->buildSliderImageUrl($storedBackgroundImage);
            $escapedImageUrl = htmlspecialchars($backgroundImageUrl, ENT_QUOTES, 'UTF-8');
            $imageDeleteIcon = $this->getDeleteIconHtml('WK_BG_IMAGE', '', true);
            $imagePreviewHtml = '
                <div id="wk-bg-image-preview-wrapper" class="wk-bg-image-preview-wrapper" style="display:flex;align-items:flex-end;">
                    <img src="'.$escapedImageUrl.'" alt="" style="width: 200px; height: 100px; object-fit: cover; border: 1px solid #d3d8db;" />
                    <span style="margin-left:8px;">'.$imageDeleteIcon.'</span>
                </div>';
        }

        if ($storedVideoPath) {
            $videoUrl = $this->buildSliderImageUrl($storedVideoPath);
            $escapedVideoUrl = htmlspecialchars($videoUrl, ENT_QUOTES, 'UTF-8');
            $videoDeleteIcon = $this->getDeleteIconHtml('WK_BG_VIDEO', '', true);
            $videoPreviewHtml = '
                <div id="wk-bg-video-preview-wrapper" class="wk-bg-video-preview-wrapper">
                    <video controls style="width: 200px; height: 100px; object-fit: cover; border: 1px solid #d3d8db;">
                        <source src="'.$escapedVideoUrl.'" type="video/mp4" />
                    </video>
                    <span style="margin-left:8px;">'.$videoDeleteIcon.'</span>
                </div>';
        }

        $formInputs = array(
            array(
                'type' => 'select',
                'label' => $this->l('Background type'),
                'name' => 'WK_BG_MEDIA_TYPE',
                'required' => true,
                'options' => array(
                    'query' => array(
                        array('id' => 'image', 'name' => $this->l('Image')),
                        array('id' => 'slider', 'name' => $this->l('Slider')),
                        array('id' => 'video', 'name' => $this->l('Video')),
                    ),
                    'id' => 'id',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'file',
                'label' => $this->l('Background image'),
                'name' => 'WK_BG_IMAGE_FILE',
                'form_group_class' => 'wk-bg-type-image collapse',
            ),
            array(
                'type' => 'file',
                'label' => $this->l('Slider images'),
                'name' => 'WK_BG_SLIDER_FILES',
                'multiple' => true,
                'form_group_class' => 'wk-bg-type-slider collapse',
            ),
            array(
                'type' => 'file',
                'label' => $this->l('Upload video file'),
                'name' => 'WK_BG_VIDEO_FILE',
                'form_group_class' => 'wk-bg-type-video collapse',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('YouTube URL'),
                'name' => 'WK_BG_VIDEO_YOUTUBE',
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'wk-bg-type-video collapse',
                'suffix' => $youtubeUrl ? $this->getDeleteIconHtml('WK_BG_VIDEO_YOUTUBE', '#WK_BG_VIDEO_YOUTUBE') : '',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Vimeo URL'),
                'name' => 'WK_BG_VIDEO_VIMEO',
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'wk-bg-type-video collapse',
                'suffix' => $vimeoUrl ? $this->getDeleteIconHtml('WK_BG_VIDEO_VIMEO', '#WK_BG_VIDEO_VIMEO') : '',
            ),
        );

        if ($imagePreviewHtml) {
            $formInputs[] = array(
                'type' => 'html',
                'label' => $this->l('Current background image'),
                'name' => 'WK_BG_IMAGE_PREVIEW',
                'html_content' => $imagePreviewHtml,
                'form_group_class' => 'wk-bg-type-image collapse',
            );
        }

        if ($videoPreviewHtml) {
            $formInputs[] = array(
                'type' => 'html',
                'label' => $this->l('Current uploaded video'),
                'name' => 'WK_BG_VIDEO_PREVIEW',
                'html_content' => $videoPreviewHtml,
                'form_group_class' => 'wk-bg-type-video collapse',
            );
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Background Image Settings'),
                'icon' => 'icon-picture-o',
            ),
            'input' => $formInputs,
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submitBackgroundImageSettings',
            ),
        );

        $this->fields_value = array(
            'WK_BG_MEDIA_TYPE' => $selectedType,
            'WK_BG_VIDEO_YOUTUBE' => $youtubeUrl,
            'WK_BG_VIDEO_VIMEO' => $vimeoUrl,
        );

        $this->show_toolbar = false;

        return parent::renderForm();
    }

    public function initContent()
    {
        parent::initContent();
        $this->content = $this->renderForm().$this->renderSliderImagesPanel();
        $this->context->smarty->assign('content', $this->content);
    }

    protected function getSortedSliderImages()
    {
        $images = $this->getCurrentSliderImages();
        usort($images, function ($a, $b) {
            $posA = isset($a['position']) ? (int) $a['position'] : 0;
            $posB = isset($b['position']) ? (int) $b['position'] : 0;
            if ($posA === $posB) {
                return 0;
            }
            return ($posA < $posB) ? -1 : 1;
        });

        return $images;
    }

    protected function normalizeSliderImagesForSave(array $sliderImages)
    {
        $normalized = array();
        $position = 1;
        foreach ($sliderImages as $row) {
            if (!is_array($row) || !isset($row['path']) || !$row['path']) {
                continue;
            }
            $normalized[] = array(
                'id' => isset($row['id']) ? (int) $row['id'] : $position,
                'path' => (string) $row['path'],
                'position' => $position,
            );
            $position++;
        }

        return $normalized;
    }

    protected function saveSliderImages(array $sliderImages)
    {
        $normalized = $this->normalizeSliderImagesForSave($sliderImages);
        return Configuration::updateValue('WK_BG_SLIDER_IMAGES', json_encode($normalized));
    }

    protected function renderSliderImagesPanel()
    {
        $selectedType = $this->normalizeBackgroundType(Configuration::get('WK_BG_MEDIA_TYPE'));
        $sliderImages = $this->getSortedSliderImages();
        $sliderRows = array();
        foreach ($sliderImages as $row) {
            $sliderRows[] = array(
                'id' => (int) $row['id'],
                'position' => (int) $row['position'],
                'image_url' => $this->buildSliderImageUrl($row['path']),
            );
        }

        $tpl = $this->context->smarty->createTemplate(
            $this->module->getLocalPath().'views/templates/admin/hotel_background_image_settings/helpers/slider_images.tpl'
        );
        $tpl->assign(
            array(
                'wk_bg_slider_rows' => $sliderRows,
                'wk_bg_slider_panel_visible' => ($selectedType === self::BG_TYPE_SLIDER),
            )
        );

        return $tpl->fetch();
    }

    protected function getValidTypes()
    {
        return array(
            self::BG_TYPE_IMAGE,
            self::BG_TYPE_SLIDER,
            self::BG_TYPE_VIDEO,
        );
    }

    protected function normalizeBackgroundType($type)
    {
        $type = Tools::strtolower(trim((string) $type));
        if (!in_array($type, $this->getValidTypes())) {
            return self::BG_TYPE_IMAGE;
        }
        return $type;
    }

    protected function getSliderImageDirectoryAbs()
    {
        return _PS_MODULE_DIR_.$this->module->name.'/views/img/imgSlider/';
    }

    protected function getVideoDirectoryAbs()
    {
        return _PS_MODULE_DIR_.$this->module->name.'/views/video/';
    }

    protected function ensureSliderImageDirectory()
    {
        $directory = $this->getSliderImageDirectoryAbs();
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }
        return is_dir($directory) && is_writable($directory);
    }

    protected function ensureVideoDirectory()
    {
        $directory = $this->getVideoDirectoryAbs();
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }
        return is_dir($directory) && is_writable($directory);
    }

    protected function getCurrentSliderImages()
    {
        $stored = Configuration::get('WK_BG_SLIDER_IMAGES');
        $decoded = json_decode((string) $stored, true);
        if (!is_array($decoded)) {
            return array();
        }

        $images = array();
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (!isset($row['path']) || !Validate::isCleanHtml($row['path'])) {
                continue;
            }
            $images[] = array(
                'id' => isset($row['id']) ? (int) $row['id'] : 0,
                'path' => (string) $row['path'],
                'position' => isset($row['position']) ? (int) $row['position'] : 0,
            );
        }

        return $images;
    }

    protected function buildSliderImageUrl($relativePath)
    {
        $relativePath = ltrim((string) $relativePath, '/');
        return $this->context->link->getMediaLink(_MODULE_DIR_.$this->module->name.'/'.$relativePath);
    }

    protected function getDeleteIconHtml($deleteKey, $targetInput = '', $removeRow = false)
    {
        $deleteKey = htmlspecialchars((string) $deleteKey, ENT_QUOTES, 'UTF-8');
        $targetInput = htmlspecialchars((string) $targetInput, ENT_QUOTES, 'UTF-8');

        return ' <a href="#" class="wk-bg-delete-media text-danger" data-delete-key="'.$deleteKey.'" data-target-input="'.$targetInput.'" data-remove-row="'.((int) $removeRow).'" title="'.$this->l('Delete').'" ><i class="icon-trash"></i></a>';
    }

    public function ajaxProcessDeleteBackgroundMedia()
    {
        $response = array(
            'success' => false,
            'message' => $this->l('Unable to delete configuration value.'),
        );

        $deleteKey = trim((string) Tools::getValue('delete_key'));
        $allowedKeys = array(
            'WK_BG_IMAGE',
            'WK_BG_VIDEO',
            'WK_BG_VIDEO_YOUTUBE',
            'WK_BG_VIDEO_VIMEO',
        );

        if (!$deleteKey || !in_array($deleteKey, $allowedKeys, true)) {
            $response['message'] = $this->l('Invalid configuration key.');
            $this->ajaxDie(json_encode($response));
        }

        if (Configuration::deleteByName($deleteKey)) {
            $response['success'] = true;
            $response['message'] = $this->l('Configuration deleted successfully.');
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessDeleteSliderImage()
    {
        $response = array(
            'success' => false,
            'message' => $this->l('Unable to delete slider image.'),
        );

        $imageId = (int) Tools::getValue('id_image');
        if (!$imageId) {
            $response['message'] = $this->l('Invalid image id.');
            $this->ajaxDie(json_encode($response));
        }

        $sliderImages = $this->getSortedSliderImages();
        $updatedImages = array();
        $isDeleted = false;

        foreach ($sliderImages as $row) {
            if ((int) $row['id'] === $imageId) {
                $isDeleted = true;
                $filePath = _PS_MODULE_DIR_.$this->module->name.'/'.ltrim($row['path'], '/');
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                continue;
            }
            $updatedImages[] = $row;
        }

        if (!$isDeleted) {
            $response['message'] = $this->l('Slider image not found.');
            $this->ajaxDie(json_encode($response));
        }

        $this->saveSliderImages($updatedImages);
        $response['success'] = true;
        $response['message'] = $this->l('Slider image deleted successfully.');
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessUpdateSliderImagePositions()
    {
        $response = array(
            'success' => false,
            'message' => $this->l('Unable to update slider positions.'),
        );

        $orderedIds = Tools::getValue('ordered_ids');
        if (is_string($orderedIds)) {
            $decoded = json_decode($orderedIds, true);
            if (is_array($decoded)) {
                $orderedIds = $decoded;
            } else {
                $orderedIds = array_filter(array_map('intval', explode(',', $orderedIds)));
            }
        }

        if (!is_array($orderedIds) || !$orderedIds) {
            $response['message'] = $this->l('Invalid slider ordering data.');
            $this->ajaxDie(json_encode($response));
        }

        $orderedIds = array_values(array_unique(array_map('intval', $orderedIds)));
        $sliderImages = $this->getCurrentSliderImages();
        $imagesById = array();
        foreach ($sliderImages as $row) {
            $imagesById[(int) $row['id']] = $row;
        }

        $updatedImages = array();
        foreach ($orderedIds as $imageId) {
            if (isset($imagesById[$imageId])) {
                $updatedImages[] = $imagesById[$imageId];
                unset($imagesById[$imageId]);
            }
        }

        foreach ($imagesById as $row) {
            $updatedImages[] = $row;
        }

        $this->saveSliderImages($updatedImages);
        $response['success'] = true;
        $response['message'] = $this->l('Slider positions updated successfully.');
        $this->ajaxDie(json_encode($response));
    }

    protected function getMaxSliderPosition(array $sliderImages)
    {
        $maxPosition = 0;
        foreach ($sliderImages as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (!isset($row['position'])) {
                continue;
            }
            $rowPosition = (int) $row['position'];
            if ($rowPosition > $maxPosition) {
                $maxPosition = $rowPosition;
            }
        }

        return $maxPosition;
    }

    protected function isValidYoutubeUrl($url)
    {
        if (!$url || !Validate::isUrl($url)) {
            return false;
        }
        $parts = parse_url($url);
        if (!isset($parts['host'])) {
            return false;
        }
        $host = Tools::strtolower($parts['host']);
        return (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false);
    }

    protected function isValidVimeoUrl($url)
    {
        if (!$url || !Validate::isUrl($url)) {
            return false;
        }
        $parts = parse_url($url);
        if (!isset($parts['host'])) {
            return false;
        }
        $host = Tools::strtolower($parts['host']);
        return (strpos($host, 'vimeo.com') !== false);
    }

    protected function uploadImageFile(array $file, $position)
    {
        if (!isset($file['tmp_name']) || !isset($file['name'])) {
            return false;
        }

        if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize())) {
            $this->errors[] = $error;
            return false;
        }

        if (!$this->ensureSliderImageDirectory()) {
            $this->errors[] = $this->l('Unable to access slider image directory.');
            return false;
        }

        $extension = Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!$extension) {
            $extension = 'jpg';
        }
        $fileName = 'bg_'.date('YmdHis').'_'.mt_rand(1000, 9999).'.'.$extension;
        $absPath = $this->getSliderImageDirectoryAbs().$fileName;

        if (!ImageManager::resize($file['tmp_name'], $absPath)) {
            $this->errors[] = $this->l('An error occurred while uploading image.');
            return false;
        }

        return array(
            'id' => (int) $position,
            'path' => 'views/img/imgSlider/'.$fileName,
            'position' => (int) $position,
            'url' => $this->buildSliderImageUrl('views/img/imgSlider/'.$fileName),
        );
    }

    protected function uploadVideoFile(array $file)
    {
        if (!isset($file['tmp_name']) || !isset($file['name'])) {
            return false;
        }

        if ((int) $file['error'] !== 0) {
            $this->errors[] = sprintf($this->l('File upload failed for "%s".'), $file['name']);
            return false;
        }

        if ((int) $file['size'] <= 0 || (int) $file['size'] > (int) Tools::getMaxUploadSize()) {
            $this->errors[] = $this->l('Uploaded video exceeds allowed upload size.');
            return false;
        }

        if (!$this->ensureVideoDirectory()) {
            $this->errors[] = $this->l('Unable to access video directory.');
            return false;
        }

        $extension = Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = array('mp4', 'webm', 'ogg');
        if (!in_array($extension, $allowedExtensions)) {
            $this->errors[] = $this->l('Invalid video format. Allowed formats: mp4, webm, ogg.');
            return false;
        }

        $fileName = 'bg_video_'.date('YmdHis').'_'.mt_rand(1000, 9999).'.'.$extension;
        $absPath = $this->getVideoDirectoryAbs().$fileName;
        if (!move_uploaded_file($file['tmp_name'], $absPath)) {
            $this->errors[] = $this->l('An error occurred while uploading video.');
            return false;
        }

        return 'views/video/'.$fileName;
    }

    protected function hasMultipleImageUpload($fieldName)
    {
        return (
            isset($_FILES[$fieldName]['name'])
            && is_array($_FILES[$fieldName]['name'])
            && count(array_filter($_FILES[$fieldName]['name'])) > 0
        );
    }

    protected function uploadMultipleSliderImages($fieldName, $startPosition = 1)
    {
        $uploaded = array();
        if (!$this->hasMultipleImageUpload($fieldName)) {
            return $uploaded;
        }

        $names = $_FILES[$fieldName]['name'];
        $tmpNames = $_FILES[$fieldName]['tmp_name'];
        $sizes = $_FILES[$fieldName]['size'];
        $errors = $_FILES[$fieldName]['error'];
        $types = $_FILES[$fieldName]['type'];

        $position = (int) $startPosition;
        foreach ($names as $index => $name) {
            if (!$name) {
                continue;
            }
            if ((int) $errors[$index] !== 0) {
                $this->errors[] = sprintf($this->l('File upload failed for "%s".'), $name);
                continue;
            }
            $file = array(
                'name' => $name,
                'tmp_name' => $tmpNames[$index],
                'size' => $sizes[$index],
                'error' => $errors[$index],
                'type' => $types[$index],
            );
            $row = $this->uploadImageFile($file, $position);
            if ($row) {
                $uploaded[] = $row;
                $position++;
            }
        }

        return $uploaded;
    }

    public function initToolbar()
    {
        $this->show_toolbar = false;
        $this->toolbar_btn = array();
        parent::initToolbar();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitBackgroundImageSettings')) {
            $mediaType = $this->normalizeBackgroundType(Tools::getValue('WK_BG_MEDIA_TYPE'));
            $youtubeUrl = trim((string) Tools::getValue('WK_BG_VIDEO_YOUTUBE'));
            $vimeoUrl = trim((string) Tools::getValue('WK_BG_VIDEO_VIMEO'));
            $sliderImages = $this->getCurrentSliderImages();
            $backgroundImage = (string) Configuration::get('WK_BG_IMAGE');
            $uploadedVideo = (string) Configuration::get('WK_BG_VIDEO');

            if ($mediaType === self::BG_TYPE_VIDEO) {
                if ($youtubeUrl) {
                    if(!$this->isValidYoutubeUrl($youtubeUrl)){
                        $this->errors[] = $this->l('Invalid YouTube URL.');
                    }else{
                        Configuration::updateValue('WK_BG_VIDEO_YOUTUBE', $youtubeUrl);
                    }
                }
                if ($vimeoUrl) {
                    if(!$this->isValidVimeoUrl($vimeoUrl)){
                        $this->errors[] = $this->l('Invalid Vimeo URL.');
                    }else{
                        Configuration::updateValue('WK_BG_VIDEO_VIMEO', $vimeoUrl);
                    }
                }
                if (isset($_FILES['WK_BG_VIDEO_FILE']) && !empty($_FILES['WK_BG_VIDEO_FILE']['name'])) {
                    $uploadedVideoPath = $this->uploadVideoFile($_FILES['WK_BG_VIDEO_FILE']);
                    if ($uploadedVideoPath) {
                        $uploadedVideo = $uploadedVideoPath;
                        Configuration::updateValue('WK_BG_VIDEO', $uploadedVideo);
                    }
                }
                if (!$youtubeUrl && !$vimeoUrl && !$uploadedVideo) {
                    $this->errors[] = $this->l('Please upload a video file or provide a YouTube/Vimeo URL for video background.');
                }
            } elseif ($mediaType === self::BG_TYPE_IMAGE) {
                if (isset($_FILES['WK_BG_IMAGE_FILE']) && !empty($_FILES['WK_BG_IMAGE_FILE']['name'])) {
                    $row = $this->uploadImageFile($_FILES['WK_BG_IMAGE_FILE'], 1);
                    if ($row) {
                        $backgroundImage = $row['path'];
                        Configuration::updateValue('WK_BG_IMAGE', $backgroundImage);
                    }
                } elseif (!$backgroundImage) {
                    $this->errors[] = $this->l('Please upload a background image.');
                }
            } elseif ($mediaType === self::BG_TYPE_SLIDER) {
                if ($this->hasMultipleImageUpload('WK_BG_SLIDER_FILES')) {
                    $startPosition = $this->getMaxSliderPosition($sliderImages) + 1;
                    $uploadedRows = $this->uploadMultipleSliderImages('WK_BG_SLIDER_FILES', $startPosition);
                    if (!empty($uploadedRows)) {
                        $sliderImages = array_merge($sliderImages, $uploadedRows);
                    }
                } elseif (empty($sliderImages)) {
                    $this->errors[] = $this->l('Please upload slider images.');
                }
            }

            if (!count($this->errors)) {
                if ($mediaType === self::BG_TYPE_SLIDER) {
                    $this->saveSliderImages($sliderImages);
                }
                Configuration::updateValue('WK_BG_MEDIA_TYPE', $mediaType);

                Tools::redirectAdmin(self::$currentIndex.'&conf=6&token='.$this->token);
            }
        }else{
            parent::postProcess();
        }
    }
}
