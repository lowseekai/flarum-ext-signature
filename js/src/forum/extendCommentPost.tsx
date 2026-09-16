import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from './components/Signature';
import type Mithril from 'mithril';

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'view', function (vnode: Mithril.Vnode<any, any>) {
    if (app.current.matches(DiscussionPage)) {
      const user = this.attrs.post.user?.();

      if (user && app.session.user) {
        if (user.signature()) {
          const allowInlineEditing = app.forum.attribute<boolean>('allowInlineEditing') || false;
          const rootChildren = Array.isArray(vnode.children) ? vnode.children : [];
          const postMain = rootChildren.find((child: any) =>
            typeof child?.attrs?.className === 'string' &&
            child.attrs.className.split(/\s+/).includes('Post-main')
          ) as Mithril.Vnode<any, any> | undefined;

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
