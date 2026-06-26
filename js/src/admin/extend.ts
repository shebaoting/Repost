import Extend from 'flarum/common/extenders';
import app from 'flarum/admin/app';

export default [
  new Extend.Admin().permission(
    () => ({
      icon: 'fas fa-link',
      label: app.translator.trans('shebaoting-repost.admin.permissions.extract_url_label'),
      permission: 'repost.extractUrl',
    }),
    'start',
    95
  ),
];
