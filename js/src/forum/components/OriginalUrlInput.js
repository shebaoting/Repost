import { extend } from 'flarum/common/extend';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import Stream from 'flarum/common/utils/Stream';

export default function () {
  extend(DiscussionComposer.prototype, 'oninit', function () {
    this.originalUrl = Stream('');
  });

  extend(DiscussionComposer.prototype, 'headerItems', function (items) {
    console.log('headerItems method called');
    items.add(
      'originalUrl',
      m('div', { className: 'Form-group' }, [
        m('label', {}, app.translator.trans('shebaoting-repost.forum.discussion_composer.original_url_label')),
        m('input', {
          className: 'FormControl',
          value: this.originalUrl(),
          oninput: (e) => {
            this.originalUrl(e.target.value);
          },
        }),
      ])
    );
  });

  extend(DiscussionComposer.prototype, 'data', function (data) {
    data.originalUrl = this.originalUrl();
  });
}
