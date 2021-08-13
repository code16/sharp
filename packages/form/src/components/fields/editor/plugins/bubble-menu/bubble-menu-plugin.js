import { Plugin } from "prosemirror-state";
import { BubbleMenuView as BaseBubbleMenuView, BubbleMenuPluginKey } from "@tiptap/extension-bubble-menu";
import { NotSelectableBubbleMenuView } from "./not-selectable-view";

/**
 * allow to ignore some extension when selecting text
 * todo replace NotSelectableBubbleMenuView with BaseBubbleMenuView
 */
class BubbleMenuView extends NotSelectableBubbleMenuView {
    constructor(props) {
        super(props);
        this.ignoredExtensions = props.ignoredExtensions;
    }
    update(view, oldState) {
        if(this.ignoredExtensions?.some(extension => this.editor.isActive(extension.name))) {
            this.hide();
            return;
        }
        super.update(view, oldState);
    }
}


export const BubbleMenuPlugin = (options) => {
    return new Plugin({
        key: BubbleMenuPluginKey,
        view: view => new BubbleMenuView({ view, ...options }),
    })
}
