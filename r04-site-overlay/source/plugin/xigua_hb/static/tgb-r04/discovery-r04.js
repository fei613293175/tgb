(function () {
  'use strict';

  var list = document.getElementById('list');
  var loading = document.getElementById('loading-show');
  var noMore = document.getElementById('loading-none');
  var itemSelector = [
    '.post-card',
    '.mod-post-list-item',
    '.tb-item-new',
    '.marticle',
    '.need_list',
    '.job_li2',
    '.shifu_li',
    '.hf_item',
    '.dh_item'
  ].join(',');

  if (!list || !noMore) {
    return;
  }

  function isLoading() {
    return loading && !loading.classList.contains('hidden');
  }

  function syncEmptyState() {
    var hasItems = Boolean(list.querySelector(itemSelector));
    if (!hasItems && !isLoading()) {
      noMore.classList.remove('hidden');
    } else if (hasItems) {
      noMore.classList.add('hidden');
    }
  }

  if (window.MutationObserver) {
    new MutationObserver(syncEmptyState).observe(list, {
      childList: true,
      subtree: true
    });
  }

  window.setTimeout(syncEmptyState, 450);
  window.setTimeout(syncEmptyState, 1200);
}());
