$(document).ready(function () {
    var ajaxUrl = (typeof wkBgDeleteAjaxUrl !== 'undefined' && wkBgDeleteAjaxUrl) ? wkBgDeleteAjaxUrl : window.location.href;
    var emptyRowHtml = '<tr id="wk-bg-slider-empty-row"><td colspan="4" class="text-center">No slider images available.</td></tr>';

    function toggleBackgroundTypeFields() {
        var bgType = $('#WK_BG_MEDIA_TYPE').val();

        $('.wk-bg-type-image, .wk-bg-type-slider, .wk-bg-type-video').hide();
        $('#wk-bg-slider-panel').hide();

        if (bgType === 'image') {
            $('.wk-bg-type-image').show();
            return;
        }

        if (bgType === 'slider') {
            $('.wk-bg-type-slider').show();
            $('#wk-bg-slider-panel').show();
            return;
        }

        if (bgType === 'video') {
            $('.wk-bg-type-video').show();
        }
    }

    function updateSliderEmptyState() {
        var $rows = $('#wkBgImageList .wk-bg-slider-row');
        if ($rows.length) {
            $('#wk-bg-slider-empty-row').hide();
            $rows.each(function (index) {
                $(this).find('.wk-bg-slider-position').text(index + 1);
            });
            return;
        }

        if (!$('#wk-bg-slider-empty-row').length) {
            $('#wkBgImageList').append(emptyRowHtml);
        } else {
            $('#wk-bg-slider-empty-row').show();
        }
    }

    function getOrderedSliderIds() {
        var orderedIds = [];
        $('#wkBgImageList .wk-bg-slider-row').each(function () {
            var imageId = parseInt($(this).data('image-id'), 10);
            if (!isNaN(imageId) && imageId > 0) {
                orderedIds.push(imageId);
            }
        });
        return orderedIds;
    }

    function handleAjaxError(response) {
        if (response && response.message) {
            showErrorMessage(response.message);
        }
    }

    function postAjax(action, payload, onSuccess) {
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: $.extend(
                {
                    ajax: 1,
                    action: action
                },
                payload || {}
            ),
            success: function (response) {
                if (!response || !response.success) {
                    handleAjaxError(response);
                    return;
                }

                if (typeof onSuccess === 'function') {
                    onSuccess(response);
                }
            }
        });
    }

    function saveSliderPositions() {
        var orderedIds = getOrderedSliderIds();
        if (!orderedIds.length) {
            return;
        }

        postAjax(
            'updateSliderImagePositions',
            {ordered_ids: orderedIds},
            function (response) {
                updateSliderEmptyState();
                showSuccessMessage(response.message);
            }
        );
    }

    function initSliderTableDnD() {
        var $table = $('#wkBgImageTable');
        if (!$table.length || typeof $table.tableDnD !== 'function') {
            return;
        }

        var originalOrder = false;
        $table.tableDnD({
            dragHandle: 'dragHandle',
            onDragClass: 'myDragClass',
            onDragStart: function () {
                originalOrder = $.tableDnD.serialize();
            },
            onDrop: function () {
                if (originalOrder === $.tableDnD.serialize()) {
                    return;
                }
                updateSliderEmptyState();
                saveSliderPositions();
            }
        });
    }

    toggleBackgroundTypeFields();
    initSliderTableDnD();
    updateSliderEmptyState();

    $(document).on('change', '#WK_BG_MEDIA_TYPE', toggleBackgroundTypeFields);

    $(document).on('click', '.wk-bg-delete-media', function (event) {
        event.preventDefault();

        var $deleteBtn = $(this);
        var deleteKey = $deleteBtn.data('delete-key');
        var targetInput = $deleteBtn.data('target-input');
        var removeRow = parseInt($deleteBtn.data('remove-row'), 10) === 1;

        if (!deleteKey || !confirm(deleteConfirm)) {
            return;
        }

        postAjax(
            'deleteBackgroundMedia',
            {delete_key: deleteKey},
            function (response) {
                showSuccessMessage(response.message);

                if (targetInput) {
                    $(targetInput).val('');
                }

                if (removeRow) {
                    $deleteBtn.closest('.form-group').remove();
                } else {
                    $deleteBtn.remove();
                }
            }
        );
    });

    $(document).on('click', '.wk-bg-delete-slider-image', function (event) {
        event.preventDefault();

        if (!confirm(deleteConfirm)) {
            return;
        }

        var $deleteBtn = $(this);
        var imageId = parseInt($deleteBtn.data('image-id'), 10);
        if (isNaN(imageId) || imageId <= 0) {
            return;
        }

        postAjax(
            'deleteSliderImage',
            {id_image: imageId},
            function (response) {
                $deleteBtn.closest('.wk-bg-slider-row').remove();
                updateSliderEmptyState();
                showSuccessMessage(response.message);
            }
        );
    });
});
