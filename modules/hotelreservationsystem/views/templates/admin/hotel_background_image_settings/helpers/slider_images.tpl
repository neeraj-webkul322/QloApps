{*
* Slider Images Panel
*}
<div id="wk-bg-slider-panel" class="panel"{if !$wk_bg_slider_panel_visible} style="display:none;"{/if}>
    <div class="panel-heading">
        <i class="icon-picture"></i> {l s='Slider Images' mod='hotelreservationsystem'}
    </div>
    <div class="panel-body">
        <div class="table-responsive-row clearfix">
            <table class="table tableDnD wk-bg-slider-table" id="wkBgImageTable">
                <thead>
                    <tr>
                        <th>{l s='ID' mod='hotelreservationsystem'}</th>
                        <th>{l s='Image' mod='hotelreservationsystem'}</th>
                        <th>{l s='Position' mod='hotelreservationsystem'}</th>
                        <th class="text-right">{l s='Action' mod='hotelreservationsystem'}</th>
                    </tr>
                </thead>
                <tbody id="wkBgImageList">
                    {if $wk_bg_slider_rows|@count}
                        {foreach $wk_bg_slider_rows as $row}
                            <tr id="wk_bg_image_{$row.id|intval}" class="wk-bg-slider-row" data-image-id="{$row.id|intval}">
                                <td>{$row.id|intval}</td>
                                <td>
                                    <img
                                        src="{$row.image_url|escape:'html':'UTF-8'}"
                                        alt=""
                                        style="width:100px;height:50px;object-fit:cover;border:1px solid #d3d8db;"
                                    />
                                </td>
                                <td id="td_wk_bg_image_{$row.id|intval}" class="pointer dragHandle center positionImage">
                                    <div class="dragGroup">
                                        <div class="positions wk-bg-slider-position">{$row.position|intval}</div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <a href="#" class="wk-bg-delete-slider-image text-danger" data-image-id="{$row.id|intval}" title="{l s='Delete' mod='hotelreservationsystem'}">
                                        <i class="icon-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr id="wk-bg-slider-empty-row">
                            <td colspan="4" class="text-center">{l s='No slider images available.' mod='hotelreservationsystem'}</td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>
    </div>
</div>
