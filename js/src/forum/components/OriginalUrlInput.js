import { extend } from 'flarum/common/extend';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import DiscussionListItem from 'flarum/forum/components/DiscussionListItem';
export default function () {
  extend(DiscussionComposer.prototype, 'data', function (data) {
    // 获取帖子内容
    const content = data.content;

    // 检查内容是否以 http:// 或 https:// 开头
    const urlPattern = /^(https?:\/\/[^\s]+)/;
    const match = content.match(urlPattern);

    if (match) {
      // 提取匹配的 URL
      const originalUrl = match[0];

      // 将提取的 URL 存储到 original_url 字段
      data.attributes = data.attributes || {};
      data.attributes.originalUrl = originalUrl;

      console.log('Detected URL:', originalUrl); // 输出检测到的 URL
    }
  });

  extend(DiscussionListItem.prototype, 'infoItems', function (items) {
    const originalUrl = this.attrs.discussion.attribute('original_url');
    console.log('Original URL:', originalUrl);

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
      vnode.attrs.onclick = function (event) {
        event.preventDefault(); // 阻止默认的a标签跳转行为
        event.stopPropagation(); // 阻止事件传播

        // 移除a标签的href属性
        event.currentTarget.removeAttribute('href');

        window.open(originalUrl, '_blank', 'noopener'); // 打开 original_url

        return false; // 确保阻止默认行为
      };
    }

    return vnode;
  });
}
