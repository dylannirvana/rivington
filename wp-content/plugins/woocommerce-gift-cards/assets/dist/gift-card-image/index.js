/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external "React"
var external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wc","blockTemplates"]
var external_wc_blockTemplates_namespaceObject = window["wc"]["blockTemplates"];
;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */

/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps} props icon is the SVG component to render
 *                          size is a number specifiying the icon size in pixels
 *                          Other props will be passed to wrapped SVG component
 *
 * @return {JSX.Element}  Icon component
 */

function Icon(_ref) {
  let {
    icon,
    size = 24,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ var icon = (Icon);
//# sourceMappingURL=index.js.map
;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/trash.js


/**
 * WordPress dependencies
 */

const trash = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20 5h-5.7c0-1.3-1-2.3-2.3-2.3S9.7 3.7 9.7 5H4v2h1.5v.3l1.7 11.1c.1 1 1 1.7 2 1.7h5.7c1 0 1.8-.7 2-1.7l1.7-11.1V7H20V5zm-3.2 2l-1.7 11.1c0 .1-.1.2-.3.2H9.1c-.1 0-.3-.1-.3-.2L7.2 7h9.6z"
}));
/* harmony default export */ var library_trash = (trash);
//# sourceMappingURL=trash.js.map
;// CONCATENATED MODULE: external ["wc","productEditor"]
var external_wc_productEditor_namespaceObject = window["wc"]["productEditor"];
;// CONCATENATED MODULE: external ["wc","components"]
var external_wc_components_namespaceObject = window["wc"]["components"];
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: ./resources/js/admin/product-editor/gift-card-image/upload-icon.tsx

/**
 * External dependencies
 */

function UploadIcon() {
  return (0,external_React_namespaceObject.createElement)("svg", {
    width: "90",
    height: "56",
    viewBox: "0 0 90 56",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,external_React_namespaceObject.createElement)("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M64.9344 56L64.9344 55.9999H76.0317V55.9846C76.2054 55.9939 76.3799 55.9991 76.5553 56H76.6708C83.9842 55.9613 89.9033 48.5817 89.9033 39.484C89.9033 30.3624 83.9531 22.9679 76.613 22.9679C73.876 22.9679 71.3322 23.9961 69.2181 25.7586C66.4592 10.9272 57.0086 0 45.7743 0C36.1227 0 27.7875 8.06524 23.8812 19.7425C21.5217 17.9834 18.7596 16.9719 15.8074 16.9719C7.1306 16.9719 0.0966797 25.7087 0.0966797 36.4861C0.0966797 47.0214 6.81828 55.6068 15.2255 55.9871V55.9999H15.7243L15.7358 56H15.879L15.8905 55.9999H26.6142L26.6142 56H36.3478L40.9342 42.8593L30.8063 45.3404L44.9998 31.1454L59.1934 45.3404L49.0654 42.8593L53.6519 56H64.9344Z",
    fill: "#F0F0F0"
  }));
}
;// CONCATENATED MODULE: ./resources/js/admin/product-editor/gift-card-image/edit.tsx

/**
 * External dependencies
 */






// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore No types for this exist yet.
// eslint-disable-next-line @woocommerce/dependency-group


/**
 * Internal dependencies
 */

function GiftCardImage({
  attributes,
  context: {
    postType
  }
}) {
  const blockProps = (0,external_wc_blockTemplates_namespaceObject.useWooBlockProps)(attributes);
  const [recipientEmailImage] = (0,external_wc_productEditor_namespaceObject.__experimentalUseProductEntityProp)('meta_data._gift_card_template_default_use_image', {
    postType
  });
  const [customImageId, setCustomImageId] = (0,external_wc_productEditor_namespaceObject.__experimentalUseProductEntityProp)('meta_data._gift_card_template_default_custom_image', {
    postType
  });

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const image = (0,external_wp_data_namespaceObject.useSelect)(select => select('core').getEntityRecord('postType', 'attachment', customImageId));
  const assignCustomImageId = selection => {
    if (Array.isArray(selection)) {
      setCustomImageId(selection[0].id);
    } else {
      setCustomImageId(selection.id);
    }
  };
  return recipientEmailImage === 'custom' && (0,external_React_namespaceObject.createElement)("div", {
    ...blockProps
  }, !customImageId ? (0,external_React_namespaceObject.createElement)("div", {
    className: "wc-gift-cards-custom-image-placeholder"
  }, (0,external_React_namespaceObject.createElement)(icon, {
    icon: (0,external_React_namespaceObject.createElement)(UploadIcon, null)
  }), (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.MediaUploader, {
    label: (0,external_wp_i18n_namespaceObject.__)('Drop files to upload, or', 'woocommerce-gift-cards'),
    buttonText: (0,external_wp_i18n_namespaceObject.__)('Select image', 'woocommerce-gift-cards'),
    onUpload: assignCustomImageId,
    onFileUploadChange: assignCustomImageId,
    onSelect: assignCustomImageId
  })) : image && (0,external_React_namespaceObject.createElement)(external_React_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)("img", {
    className: "wc-gift-cards-custom-image",
    alt: image.alt_text,
    src: image.source_url
  }), (0,external_React_namespaceObject.createElement)("div", {
    className: "wc-gift-cards-remove-image-button"
  }, (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    icon: library_trash,
    onClick: () => setCustomImageId(null)
  }, (0,external_wp_i18n_namespaceObject.__)('Remove image', 'woocommerce-gift-cards')))));
}
;// CONCATENATED MODULE: ./resources/js/admin/product-editor/gift-card-image/block.json
var block_namespaceObject = JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"woocommerce-gift-cards/gift-card-image-field","version":"0.1.0","title":"Gift Card Image Field","category":"widgets","icon":"flag","description":"Gift Card Image Field","attributes":{"__editorContent":{"type":"string","__experimentalRole":"content"}},"supports":{"html":false,"inserter":false},"textdomain":"woocommerce-gift-cards","editorScript":"file:./index.js","editorStyle":"file:./index.css","usesContext":["postType"]}');
;// CONCATENATED MODULE: ./resources/js/admin/product-editor/gift-card-image/index.ts
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Internal dependencies
 */
 // see https://www.npmjs.com/package/@wordpress/scripts#using-css

(0,external_wp_blocks_namespaceObject.registerBlockType)(block_namespaceObject, {
  edit: GiftCardImage
});
/******/ })()
;