import { BubbleMenuView as BaseBubbleMenuView } from "@tiptap/extension-bubble-menu";

/**
 * todo remove this and keep own custom class when https://github.com/ueberdosis/tiptap/pull/1556 is merged
 * @see BubbleMenuView
 */
export class NotSelectableBubbleMenuView extends BaseBubbleMenuView {
    constructor(props) {
        super(props);
        this.viewSelecting = false
        this.view.dom.addEventListener('mousedown', this.viewMousedownHandler);
        this.view.dom.addEventListener('mouseup', this.viewMouseupHandler);
    }
    viewMousedownHandler = () => {
        this.viewSelecting = true
    }
    viewMouseupHandler = () => {
        this.viewSelecting = false
        // we use `setTimeout` to make sure `selection` is already updated
        setTimeout(() => this.update(this.editor.view));
    }
    update(view, oldState) {
        if ((!view.hasFocus() || this.viewSelecting) && !this.preventHide) {
            this.hide();
            return;
        }
        super.update(view, oldState);
    }
    destroy() {
        this.view.dom.removeEventListener('mousedown', this.viewMousedownHandler);
        this.view.dom.removeEventListener('mouseup', this.viewMouseupHandler);
        super.destroy();
    }
}
