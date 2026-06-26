import app from 'flarum/forum/app';
import addOriginalUrlInput from './components/OriginalUrlInput';

export { default as extend } from './extend';

app.initializers.add('shebaoting-repost', () => {
  addOriginalUrlInput();
});
