import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from './components/Signature';

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'content', function (items) {
    if (app.current.matches(DiscussionPage)) {
      const user = this.attrs.post.user?.();

      if (user && app.session.user) {
        if (user.signature()) {
          const allowInlineEditing = app.forum.attribute<boolean>('allowInlineEditing') || false;

          items.push(
            <div className="Post-signature">
              <Signature user={user} readonly={!allowInlineEditing} />
            </div>
          );
        }
      }
    }
  });
}
