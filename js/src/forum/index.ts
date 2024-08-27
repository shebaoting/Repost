import app from 'flarum/forum/app';
import addOriginalUrlInput from './components/OriginalUrlInput';

app.initializers.add('shebaoting-repost', () => {
  console.log('[shebaoting/repost] Hello, forum!');
  addOriginalUrlInput();
});
