import Stream from 'flarum/common/utils/Stream';
import type EditorDriverInterface from 'flarum/common/utils/EditorDriverInterface';

class SignatureState {
  // The content of the signature
  content: Stream<string>;

  // Indicates whether the signature is being edited
  editing: boolean;
  editor: EditorDriverInterface | null;

  constructor() {
    this.content = Stream('');
    this.editing = false;
    this.editor = null;
  }

  // Sets the content of the signature
  setContent(content: Stream<string>) {
    this.content = content;
  }

  // Toggles the editing state
  toggleEditing() {
    this.editing = !this.editing;
  }
}

export default SignatureState;
