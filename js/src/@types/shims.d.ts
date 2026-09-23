import 'flarum/common/models/User';

declare module 'flarum/common/models/User' {
  export default interface User {
    signature(): string | null | undefined;
    signatureHtml(): string | null | undefined;
    canEditSignature(): boolean | undefined;
    canHaveSignature(): boolean | undefined;
  }
}
