import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from './components/Signature';
import type Mithril from 'mithril';

function findVNodeByClass(vnode: any, className: string): Mithril.Vnode<any, any> | undefined {
  if (!vnode || typeof vnode !== 'object') {
    return undefined;
  }

  if (
    typeof vnode.attrs?.className === 'string' &&
    vnode.attrs.className.split(/\s+/).includes(className)
  ) {
    return vnode;
  }

  if (Array.isArray(vnode.children)) {
    for (const child of vnode.children) {
      const match = findVNodeByClass(child, className);

      if (match) {
        return match;
      }
    }
  }

  return undefined;
}

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'view', function (vnode: Mithril.Vnode<any, any>) {
    if (app.current.matches(DiscussionPage)) {
      const user = this.attrs.post.user?.();

      if (user && app.session.user) {
        if (user.signature()) {
          const allowInlineEditing = app.forum.attribute<boolean>('allowInlineEditing') || false;
          const postMain = findVNodeByClass(vnode, 'Post-main');

          if (postMain && Array.isArray(postMain.children)) {
            const alreadyAdded = postMain.children.some((child: any) =>
              typeof child?.attrs?.className === 'string' &&
              child.attrs.className.split(/\s+/).includes('Post-signature')
            );

            if (!alreadyAdded) {
              const signature = (
                <div className="Post-signature">
                  <Signature user={user} readonly={!allowInlineEditing} />
                </div>
              );
              const actionsIndex = postMain.children.findIndex((child: any) =>
                typeof child?.attrs?.className === 'string' &&
                child.attrs.className.split(/\s+/).includes('Post-actions')
              );
              const footerIndex = postMain.children.findIndex((child: any) =>
                typeof child?.attrs?.className === 'string' &&
                child.attrs.className.split(/\s+/).includes('Post-footer')
              );
              const insertAt = actionsIndex >= 0 ? actionsIndex : footerIndex;

              if (insertAt >= 0) {
                postMain.children.splice(insertAt, 0, signature);
              } else {
                postMain.children.push(signature);
              }
            }
          }
        }
      }
    }
  });
}
