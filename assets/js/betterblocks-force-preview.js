/**
 *
 * Force preview mode for ACF blocks when editor loads.
 */

wp.domReady(function () {
  const unsubscribe = wp.data.subscribe(function () {
    const blocks = wp.data.select('core/block-editor').getBlocks();

    blocks.forEach(function (block) {
      if (block.name.startsWith('acf/')) {
        if (block.attributes.mode !== 'preview') {
          wp.data.dispatch('core/block-editor').updateBlockAttributes(block.clientId, { mode: 'preview' });
        }
      }
    });
  });

  setTimeout(() => {
    unsubscribe();
  }, 1000);
});
