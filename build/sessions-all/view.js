/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************!*\
  !*** ./src/sessions-all/view.js ***!
  \**********************************/
/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

document.addEventListener('DOMContentLoaded', function () {
  console.log("DOM fully loaded and parsed");

  // Check if the browser supports the Popover API
  const supportsPopover = HTMLElement.prototype.hasOwnProperty('popover');
  function positionPopover(icon, popover) {
    const iconRect = icon.getBoundingClientRect();
    popover.style.position = 'absolute';
    popover.style.top = `${iconRect.bottom + window.scrollY + 5}px`;
    popover.style.left = `${iconRect.left + window.scrollX}px`;
  }
  document.body.addEventListener('mouseover', function (event) {
    if (event.target.classList.contains('session-icon')) {
      console.log('Mouseover event triggered on icon');
      const popoverId = event.target.getAttribute('popovertarget');
      const popover = document.getElementById(popoverId);
      if (popover) {
        positionPopover(event.target, popover);
        if (supportsPopover) {
          popover.showPopover();
        } else {
          popover.style.display = 'block';
        }
      }
    }
  });
  document.body.addEventListener('mouseout', function (event) {
    if (event.target.classList.contains('session-icon')) {
      console.log('Mouseout event triggered on icon');
      const popoverId = event.target.getAttribute('popovertarget');
      const popover = document.getElementById(popoverId);
      if (popover) {
        if (supportsPopover) {
          popover.hidePopover();
        } else {
          popover.style.display = 'none';
        }
      }
    }
  });

  // Fallback for non-supporting browsers
  if (!supportsPopover) {
    console.warn('Popover API is not supported in this browser. Using fallback.');
    document.querySelectorAll('[popover]').forEach(popover => {
      popover.style.display = 'none';
      popover.style.position = 'absolute';
      popover.style.backgroundColor = '#f0f0f0';
      popover.style.border = '1px solid #ccc';
      popover.style.padding = '5px';
      popover.style.borderRadius = '4px';
      popover.style.zIndex = '1000';
    });
  }
});
/******/ })()
;
//# sourceMappingURL=view.js.map