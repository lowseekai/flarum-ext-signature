import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from './components/Signature';

function placeSignatureBeforeActions(element: Element | undefined) {
  if (!element) {
    return;
  }

  const signature = element.querySelector('.Post-signature');
  const actions = element.querySelector('.Post-actions');

  const parent = actions?.parentElement;

  if (signature && parent && parent === signature.parentElement) {
    parent.insertBefore(signature, actions);
  }
}

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'content', function (content) {
    const user = this.attrs.post.user?.();

    if (!user || !app.session.user || !user.signature()) {
      return;
    }

    const allowInlineEditing = app.forum.attribute<boolean>('allowInlineEditing') || false;

    content.push(
      <div className="Post-signature">
        <Signature user={user} readonly={!allowInlineEditing} />
      </div>
    );
  });

  extend(CommentPost.prototype, ['oncreate', 'onupdate'], function () {
    placeSignatureBeforeActions(this.element);
  });
}
