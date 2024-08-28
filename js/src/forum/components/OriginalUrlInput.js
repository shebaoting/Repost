import { extend } from 'flarum/common/extend';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';

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
}
