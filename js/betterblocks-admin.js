/**
 * Add controls to hide a block from the frontend.
 */
(function (wp) {
  'use strict';

  if (window.betterBlocksInitialized) {
    return;
  }

  window.betterBlocksInitialized = true;

  var __ = wp.i18n.__;
  var addFilter = wp.hooks.addFilter;
  var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
  var Fragment = wp.element.Fragment;
  var InspectorAdvancedControls = wp.blockEditor.InspectorAdvancedControls;
  var BlockControls = wp.blockEditor.BlockControls;
  var ToolbarGroup = wp.components.ToolbarGroup;
  var ToolbarButton = wp.components.ToolbarButton;
  var ToggleControl = wp.components.ToggleControl;

  addFilter('blocks.registerBlockType', 'betterblocks/hide-block-attribute', function (settings) {
    settings.attributes = Object.assign({}, settings.attributes, {
      betterblocks_disable_frontend_block: {
        type: 'boolean',
        default: false,
      },
    });

    return settings;
  });

  /**
   * Only render BetterBlocks controls for the currently selected block.
   */
  var withHideBlockControls = createHigherOrderComponent(function (BlockEdit) {
    return function (props) {
      if (!props.isSelected) {
        return wp.element.createElement(BlockEdit, props);
      }

      var isHidden = !!props.attributes.betterblocks_disable_frontend_block;

      var toggleHideBlock = function () {
        props.setAttributes({ betterblocks_disable_frontend_block: !isHidden });
      };

      return wp.element.createElement(
        Fragment,
        null,
        wp.element.createElement(BlockEdit, props),
        wp.element.createElement(
          BlockControls,
          null,
          wp.element.createElement(
            ToolbarGroup,
            { className: 'hide-block__toolbar' },
            wp.element.createElement(ToolbarButton, {
              icon: isHidden ? 'hidden' : 'visibility',
              label: isHidden ? __('Show Block', 'betterblocks') : __('Hide Block', 'betterblocks'),
              onClick: toggleHideBlock,
            })
          )
        ),
        wp.element.createElement(
          InspectorAdvancedControls,
          null,
          wp.element.createElement(ToggleControl, {
            label: __('Hide Block', 'betterblocks'),
            checked: isHidden,
            onChange: toggleHideBlock,
            help: isHidden ? __('Block is hidden', 'betterblocks') : __('Block is visible', 'betterblocks'),
          })
        )
      );
    };
  }, 'withHideBlockControls');

  /**
   * Apply the hidden appearance to WordPress's existing block wrapper instead
   * of adding an extra wrapper around every block's editor output.
   */
  var withHideBlockClass = createHigherOrderComponent(function (BlockListBlock) {
    return function (props) {
      var isHidden = !!props.attributes.betterblocks_disable_frontend_block;
      var className = [props.className, isHidden ? 'hide-block--active' : '']
        .filter(Boolean)
        .join(' ');

      return wp.element.createElement(
        BlockListBlock,
        Object.assign({}, props, { className: className })
      );
    };
  }, 'withHideBlockClass');

  addFilter('editor.BlockEdit', 'betterblocks/with-hide-block-controls', withHideBlockControls);
  addFilter('editor.BlockListBlock', 'betterblocks/with-hide-block-class', withHideBlockClass);
})(window.wp);

/**
 * Allow the block editor settings sidebar to be resized by dragging its left edge.
 * WordPress itself controls whether the sidebar is open or closed.
 */
(function ($) {
  'use strict';

  $(function () {
    var sidebarWidthKey = 'betterblocks_sidebar_width';
    var sidebarSelector = '.interface-interface-skeleton__sidebar';
    var observer = null;
    var initScheduled = false;

    function getStoredSidebarWidth() {
      try {
        return window.localStorage.getItem(sidebarWidthKey);
      } catch (error) {
        return null;
      }
    }

    function saveSidebarWidth(width) {
      try {
        window.localStorage.setItem(sidebarWidthKey, String(width));
      } catch (error) {
        // Storage can be unavailable under restrictive browser/privacy settings.
      }
    }

    function getSavedWidth() {
      var savedWidth = parseInt(getStoredSidebarWidth(), 10);

      if (Number.isNaN(savedWidth)) {
        return 280;
      }

      return Math.min(Math.max(savedWidth, 280), 700);
    }

    function initResizableSidebar() {
      $(sidebarSelector).each(function () {
        var $sidebar = $(this);

        if ($sidebar.hasClass('ui-resizable')) {
          return;
        }

        $sidebar.width(getSavedWidth());

        $sidebar.resizable({
          handles: 'w',
          minWidth: 280,
          maxWidth: 700,
          resize: function () {
            $(this).css({
              left: 'auto',
              right: 0,
            });
          },
          stop: function () {
            saveSidebarWidth(Math.round($(this).width()));
          },
        });

        var $handle = $sidebar.find('.ui-resizable-w');

        if (!$handle.find('.betterblocks-resize-indicator').length) {
          $handle.append('<div class="betterblocks-resize-indicator"></div>');
        }
      });
    }

    function scheduleResizableSidebarInit() {
      if (initScheduled) {
        return;
      }

      initScheduled = true;

      window.requestAnimationFrame(function () {
        initScheduled = false;
        initResizableSidebar();
      });
    }

    initResizableSidebar();

    observer = new MutationObserver(function () {
      scheduleResizableSidebarInit();
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });
  });
})(jQuery);
