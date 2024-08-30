import { extend } from 'flarum/common/extend';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import DiscussionListItem from 'flarum/forum/components/DiscussionListItem';
import EditPostComposer from 'flarum/forum/components/EditPostComposer';
import app from 'flarum/forum/app';
export default function () {
  function handleOriginalUrl(data) {
    // 获取帖子内容
    const content = data.content;

    // 检查内容是否以 http:// 或 https:// 开头
    const urlPattern = /^(https?:\/\/[^\s]+)/;
    const match = content.match(urlPattern);
    console.log('Matched URL:', match);
    data.attributes = data.attributes || {};

    if (match) {
      // 提取匹配的 URL 并更新 original_url
      const originalUrl = match[0];
      data.attributes.originalUrl = originalUrl;
      console.log('Original URL set:', originalUrl);
    } else {
      // 如果内容开头不是网址，重置 original_url 为空
      data.attributes.originalUrl = '';
      console.log('Original URL cleared');
    }
  }

  // 扩展新帖子的创建逻辑
  extend(DiscussionComposer.prototype, 'data', function (data) {
    handleOriginalUrl(data);
  });

  // 扩展帖子编辑的逻辑
  extend(EditPostComposer.prototype, 'data', function (data) {
    handleOriginalUrl(data);
  });

  extend(DiscussionListItem.prototype, 'infoItems', function (items) {
    const originalUrl = this.attrs.discussion.attribute('original_url');
    if (originalUrl) {
      try {
        const url = new URL(originalUrl);
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
        console.error('Invalid URL:', originalUrl);
      }
    }
  });

  // 修改帖子标题的点击行为
  extend(DiscussionListItem.prototype, 'view', function (vnode) {
    const originalUrl = this.attrs.discussion.attribute('original_url');

    if (originalUrl) {
      // 递归查找标题元素的函数
      const findTitleElement = (children) => {
        if (!children) return null;
        for (let child of children) {
          if (child.tag === 'h2' && child.attrs && child.attrs.className === 'DiscussionListItem-title') {
            return child;
          }
          if (child.children) {
            const found = findTitleElement(child.children);
            if (found) return found;
          }
        }
        return null;
      };

      // 查找标题元素
      const titleElement = findTitleElement(vnode.children);
      if (titleElement) {
        // 为标题添加点击事件
        titleElement.attrs.onclick = function (event) {
          event.preventDefault(); // 阻止默认的 a 标签跳转行为
          event.stopPropagation(); // 阻止事件传播

          window.open(originalUrl, '_blank', 'noopener'); // 打开 original_url

          return false; // 确保阻止默认行为
        };
      }
    }

    return vnode;
  });
}
