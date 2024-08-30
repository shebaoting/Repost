import app from 'flarum/admin/app';

app.initializers.add('shebaoting/repost', () => {
  console.log('[shebaoting/repost] Hello, admin!');
  app.extensionData.for('shebaoting-repost').registerPermission(
    {
      icon: 'fas fa-link',
      label: app.translator.trans('shebaoting-repost.admin.permissions.extract_url_label'),
      permission: 'repost.extractUrl',
    },
    'start',
    95
  );
});
