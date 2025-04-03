/**
 *
 * Add button to block toolbar to hide block from frontend when enabled.
 */

(function (wp) {
  if (window.betterBlocksInitialized) {
    return;
  }
  if (!wp.data.select('core/interface') && !window.betterBlocksInitialized) {
    window.betterBlocksInitialized = true;
  }

  var __ = wp.i18n.__;
  var addFilter = wp.hooks.addFilter;
  var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
  var Fragment = wp.element.Fragment;
  var InspectorAdvancedControls = wp.blockEditor.InspectorAdvancedControls;
  var BlockControls = wp.blockEditor.BlockControls;
  var ToolbarGroup = wp.components.ToolbarGroup;
  var ToolbarButton = wp.components.ToolbarButton;
  var ToggleControl = wp.components.ToggleControl;

  wp.hooks.removeFilter('blocks.registerBlockType', 'betterblocks/hide-block-attribute');
  wp.hooks.removeFilter('editor.BlockEdit', 'betterblocks/with-inspector-control');

  addFilter('blocks.registerBlockType', 'betterblocks/hide-block-attribute', function (settings, name) {
    settings.attributes = Object.assign(settings.attributes, {
      betterblocks_disable_frontend_block: {
        type: 'boolean',
        default: false,
      },
    });

    return settings;
  });

  var withInspectorControl = createHigherOrderComponent(function (BlockEdit) {
    return function (props) {
      var toggleHideBlock = function () {
        props.setAttributes({ betterblocks_disable_frontend_block: !props.attributes.betterblocks_disable_frontend_block });
      };

      return wp.element.createElement(
        Fragment,
        null,
        wp.element.createElement(
          BlockControls,
          null,
          wp.element.createElement(
            ToolbarGroup,
            { className: 'hide-block__toolbar' },
            wp.element.createElement(ToolbarButton, {
              icon: props.attributes.betterblocks_disable_frontend_block ? 'hidden' : 'visibility',
              label: props.attributes.betterblocks_disable_frontend_block ? __('Show Block', 'betterblocks') : __('Hide Block', 'betterblocks'),
              onClick: toggleHideBlock,
            })
          )
        ),
        wp.element.createElement('div', { className: props.attributes.betterblocks_disable_frontend_block ? 'hide-block--active' : '' }, wp.element.createElement(BlockEdit, props)),
        wp.element.createElement(
          InspectorAdvancedControls,
          null,
          wp.element.createElement(ToggleControl, {
            label: __('Hide Block', 'betterblocks'),
            checked: !!props.attributes.betterblocks_disable_frontend_block,
            onChange: toggleHideBlock,
            help: props.attributes.betterblocks_disable_frontend_block ? __('Block is hidden', 'betterblocks') : __('Block is visible', 'betterblocks'),
          })
        )
      );
    };
  }, 'withInspectorControl');

  addFilter('editor.BlockEdit', 'betterblocks/with-inspector-control', withInspectorControl);
})(window.wp);

/**
 *
 * Allow block editor sidebar to be resized with drag and drop.
 */

(function ($) {
  ('use strict');

  $(function () {
    const sidebar_width_key = 'betterblocks_sidebar_width';
    const sidebar_selector = '.interface-interface-skeleton__sidebar';
    const pinned_item_selector = '.interface-pinned-items button';
    const close_sidebar_selector = '.editor-sidebar__panel-tabs button[aria-label="Close Settings"]';
    const layout_selector = '.edit-post-layout, .edit-site-layout';

    function initResizableSidebar() {
      const $sidebar = $(sidebar_selector);

      if ($sidebar.length === 0) {
        return;
      }

      const savedWidth = localStorage.getItem(sidebar_width_key);

      if (savedWidth) {
        $sidebar.width(savedWidth);
      }

      if ($sidebar.hasClass('ui-resizable')) {
        $sidebar.resizable('destroy');
      }

      $sidebar.resizable({
        handles: 'w',
        minWidth: 280,
        maxWidth: 700,
        resize: function (event, ui) {
          $(this).css({
            left: 'auto',
            right: 0,
          });
          localStorage.setItem(sidebar_width_key, $(this).width());
        },
      });

      $sidebar.find('.ui-resizable-w').css({
        left: '0',
        width: '10px',
        height: '100%',
      });

      if ($sidebar.find('.betterblocks-resize-indicator').length === 0) {
        $sidebar.find('.ui-resizable-w').append('<div class="betterblocks-resize-indicator"></div>');
      }

      $sidebar.width(savedWidth || 280);
    }

    function updateSidebarVisibility() {
      const isSidebarOpen = $(pinned_item_selector)
        .toArray()
        .some((button) => $(button).hasClass('is-pressed'));
      $(layout_selector).toggleClass('is-sidebar-opened', isSidebarOpen);
    }

    function setupEventListeners() {
      $('body').on('click', pinned_item_selector, updateSidebarVisibility);

      $('body').on('click', close_sidebar_selector, function () {
        const $sidebar = $(sidebar_selector);
        if ($sidebar.length) {
          $sidebar.hide();
          $(layout_selector).removeClass('is-sidebar-opened');
        }
      });
    }

    function init() {
      const checkInterval = setInterval(() => {
        if ($(sidebar_selector).length) {
          clearInterval(checkInterval);
          initResizableSidebar();
          updateSidebarVisibility();
          setupEventListeners();
        }
      }, 1000);

      function periodicCheck() {
        const $sidebar = $(sidebar_selector);
        if ($sidebar.length && !$sidebar.hasClass('ui-resizable')) {
          initResizableSidebar();
        }
      }

      setInterval(periodicCheck, 10000);
    }

    init();
  });
})(jQuery);
