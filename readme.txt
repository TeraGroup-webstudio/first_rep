Опис проекта
$this->config->get('config_language_id');
$this->config->get('theme_default_product_empty_end'); - товари в кінець списку з 0 кількістю

Watermark
theme_default_watermark_status -- статус водяного знаку

------------------------------lazy load --------------------------------
$data['lazy_load_status'] = $this->config->get('theme_default_image_lazy_load_status');

<file path="catalog/view/theme/*/template/product/{category,special,search,manufacturer_info}*twig">
      <operation>
         <search>
            <![CDATA[<img src="{{ product.thumb }}" alt="{{ product.name }}" title="{{ product.name }}" class="img-responsive" />]]>
         </search>
         <add position="replace">
            <![CDATA[<img data-original="{{ product.thumb }}" alt="{{ product.name }}" title="{{ product.name }}" class="img-responsive lazy-load"/>]]>
         </add>
      </operation>
   </file>
   <file path="catalog/view/theme/*/template/extension/module/{latest,featured,bestseller,special}*.twig">
      <operation>
         <search>
            <![CDATA[<img src="{{ product.thumb }}" alt="{{ product.name }}" title="{{ product.name }}" class="img-responsive" />]]>
         </search>
         <add position="replace">
            <![CDATA[<img data-original="{{ product.thumb }}" alt="{{ product.name }}" title="{{ product.name }}" class="img-responsive lazy-load"/>]]>
         </add>
      </operation>
   </file>
----------------------------------------------------------------------------

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-cogs fw"></i> {{ text_microdata_main_setting }}</h4>
    </div>
    <div class="panel-body">

    </div>
</div>


-------------------------------------------------------------------------------
config_currency2 - theme_default_product_refresh_price_currency2
config_autocalc_option_special - theme_default_product_refresh_price_option_special
config_autocalc_option_discount - theme_default_product_refresh_price_option_discount
config_autocalc_not_mul_qty - theme_default_product_refresh_price_not_mul_qty
config_autocalc_select_first - theme_default_product_refresh_price_select_first
config_autocalc_hide_option_price - theme_default_product_refresh_price_hide_option_price

