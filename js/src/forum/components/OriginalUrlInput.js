import { extend, override } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Icon from 'flarum/common/components/Icon';
import Link from 'flarum/common/components/Link';
import abbreviateNumber from 'flarum/common/utils/abbreviateNumber';
import DiscussionListItem from 'flarum/forum/components/DiscussionListItem';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';

export default function () {
  function extractOriginalUrl(content) {
    const urlPattern = /^(https?:\/\/[^\s]+)/;
    const match = String(content || '').match(urlPattern);

    return match ? match[0] : '';
  }

  function originalUrl(discussion) {
    return discussion.originalUrl ? discussion.originalUrl() : discussion.attribute('originalUrl');
  }

  function discussionRoute(component) {
    const discussion = component.attrs.discussion;
    const jumpTo = typeof component.getJumpTo === 'function' ? component.getJumpTo() : undefined;

    return app.route.discussion(discussion, jumpTo);
  }

  // 扩展新帖子的创建逻辑
  extend('flarum/forum/components/DiscussionComposer', 'data', function (data) {
    if (app.forum.attribute('canExtractOriginalUrl')) {
      data.originalUrl = extractOriginalUrl(data.content);
    }
  });

  extend(DiscussionListItem.prototype, 'infoItems', function (items) {
    const urlValue = originalUrl(this.attrs.discussion);

    if (urlValue) {
      try {
        const url = new URL(urlValue);
        const domain = url.hostname; // 获取域名信息，比如 chat.chatgpt.com

        items.add(
          'originalUrl',
          m(
            'span',
            {
              className: 'item-terminalPost',
            },
            domain
          ), // 仅显示域名部分
          -10
        );
      } catch (e) {
        // Ignore malformed legacy data.
      }
    }
  });

  // 修改帖子标题的点击行为
  extend(DiscussionListItem.prototype, 'mainView', function (vnode) {
    const urlValue = originalUrl(this.attrs.discussion);

    if (urlValue && vnode && vnode.attrs) {
      vnode.attrs.href = urlValue;
      vnode.attrs.external = true;
      vnode.attrs.target = '_blank';
      vnode.attrs.rel = 'noopener noreferrer';
    }
  });

  override(DiscussionListItem.prototype, 'replyCountItem', function (original) {
    const discussion = this.attrs.discussion;

    if (!originalUrl(discussion)) {
      return original();
    }

    const count = discussion.replyCount();
    const countLabel = app.translator.trans('core.forum.discussion_list.total_replies_a11y_label', { count });

    return m(
      Link,
      {
        className: 'DiscussionListItem-stats-item DiscussionListItem-count',
        href: discussionRoute(this),
        'aria-label': countLabel,
      },
      [
        m('span', { className: 'DiscussionListItem-stats-item-icon' }, m(Icon, { name: 'far fa-comment' })),
        m('span', { className: 'DiscussionListItem-stats-item-label' }, [
          m('span', { 'aria-hidden': true }, abbreviateNumber(count)),
          m('span', { className: 'visually-hidden' }, countLabel),
        ]),
      ]
    );
  });

  extend(DiscussionPage.prototype, 'onupdate', function () {
    const discussion = this.discussion;
    const urlValue = discussion && originalUrl(discussion);

    // 确保讨论已经加载并且有 originalUrl
    if (urlValue) {
      const noticeElement = document.createElement('div');
      const noticeText = document.createElement('p');

      noticeElement.className = 'OriginalUrlNotice';
      noticeText.textContent = app.translator.trans('shebaoting-repost.forum.notice_message');
      noticeElement.appendChild(noticeText);

      // 找到第一个帖子元素
      const firstPost = this.$('.PostStream-item:first-child .Post-body')[0];

      // 检查是否已经插入提示，避免重复插入
      if (firstPost && !this.$('.OriginalUrlNotice').length) {
        firstPost.parentNode.insertBefore(noticeElement, firstPost.nextSibling);
      }
    }
  });
}
