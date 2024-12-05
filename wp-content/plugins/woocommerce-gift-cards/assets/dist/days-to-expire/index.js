/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external "React"
var external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wc","productEditor"]
var external_wc_productEditor_namespaceObject = window["wc"]["productEditor"];
;// CONCATENATED MODULE: external ["wc","blockTemplates"]
var external_wc_blockTemplates_namespaceObject = window["wc"]["blockTemplates"];
;// CONCATENATED MODULE: ./resources/js/admin/product-editor/days-to-expire/edit.tsx

/**
 * External dependencies
 */



function DaysToExpire({
  context: {
    postType
  },
  attributes
}) {
  const [expirationDays, setExpirationDays] = (0,external_wc_productEditor_namespaceObject.__experimentalUseProductEntityProp)('meta_data._gift_card_expiration_days', {
    postType
  });
  const blockProps = (0,external_wc_blockTemplates_namespaceObject.useWooBlockProps)(attributes);
  const {
    hasEdit
  } = (0,external_wc_productEditor_namespaceObject.__experimentalUseProductEdits)();
  return (0,external_React_namespaceObject.createElement)("div", {
    ...blockProps
  }, (0,external_React_namespaceObject.createElement)(external_wc_productEditor_namespaceObject.__experimentalNumberControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Days to expire', 'woocommerce-gift-cards'),
    suffix: (0,external_wp_i18n_namespaceObject.__)('days', 'woocommerce-gift-cards'),
    value: expirationDays !== '0' || hasEdit('meta_data._gift_card_expiration_days') ? expirationDays || '' : '',
    onBlur: () => expirationDays === '0' && setExpirationDays(''),
    onChange: setExpirationDays,
    placeholder: (0,external_wp_i18n_namespaceObject.__)('Unlimited', 'woocommerce-gift-cards'),
    help: (0,external_wp_i18n_namespaceObject.__)('Period of time before gift code becomes inactive. Leave it empty to keep this active indefinitely.', 'woocommerce-gift-cards'),
    min: 0
  }));
}
;// CONCATENATED MODULE: ./resources/js/admin/product-editor/days-to-expire/block.json
var block_namespaceObject = JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"woocommerce-gift-cards/days-to-expire-field","version":"0.1.0","title":"Days to Expire Field","category":"widgets","icon":"flag","description":"Days to Expire Field","attributes":{"__editorContent":{"type":"string","__experimentalRole":"content"}},"supports":{"html":false,"inserter":false},"textdomain":"woocommerce-gift-cards","editorScript":"file:./index.js","editorStyle":"file:./index.css","usesContext":["postType"]}');
;// CONCATENATED MODULE: ./resources/js/admin/product-editor/days-to-expire/index.ts
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


(0,external_wp_blocks_namespaceObject.registerBlockType)(block_namespaceObject, {
  edit: DaysToExpire
});
/******/ })()
;