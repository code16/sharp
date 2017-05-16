<template>
    <div class="SharpMarkdown">
        <textarea ref="textarea"></textarea>
    </div>
</template>

<script>
    import SimpleMDE from 'simplemde';
    import MarkdownUpload from './MarkdownUpload';
    import CodeMirror from 'codemirror';

    export default {
        name: 'SharpMarkdown',
        props: {
            value:String,

            placeholder:String,
            toolbar:Array,
            height:Number,
            maxImageSize:Number
        },
        data() {
            return {
                simplemde:null,
                cursorPos:0,
            }
        },
        methods : {
            createUploader(cm, selection) {
                return new MarkdownUpload({
                    propsData: {
                        onSuccess(marker, file) {
                            let find = marker.find();
                            console.log(marker);
                            cm.replaceRange(`[${selection||''}](${file.name})`,find.from,find.to);
                        },
                        onAdded() {
                            cm.refresh();
                            cm.focus();
                        },
                        onRemoved(marker) {
                            let find = marker.find(), line=find.from.line;
                            marker.clear();
                            cm.replaceRange('',{line,ch:0},{line:line+1,ch:0});
                            cm.focus();
                        }
                    }
                });
            },
            insertUploadImage({codemirror}) {
                let cm = codemirror;
                let selection = cm.getSelection(' ');
                let curLineContent = cm.getLine(this.cursorPos.line);

                if(curLineContent.length) {
                    cm.replaceRange('\n', {
                        line: this.cursorPos.line,
                        ch: curLineContent.length
                    });
                    cm.setCursor(this.cursorPos.line+1, 0);
                }

                cm.getInputField().blur();

                cm.replaceRange('![]()\n',this.cursorPos);
                cm.setCursor(this.cursorPos.line-1,0);
                let from = this.cursorPos, to = { line:this.cursorPos.line, ch:this.cursorPos.ch+5 };

                let uploader = this.createUploader(cm);
                uploader.marker = cm.markText(from, to, {
                    replacedWith: uploader.$mount().$el ,
                    clearWhenEmpty: false,
                    inclusiveRight: true,
                    inclusiveLeft: true,
                });

                uploader.marker.on('beforeCursorEnter',function() {
                    console.log('cursor enter', JSON.stringify(cm.getCursor()));
                });
                uploader.inputClick();

                cm.setCursor(this.cursorPos.line+1, 0);
            },
            onCursorActivity(cm) {
                this.cursorPos = cm.getCursor();
            },
            onChange(cm) {
                this.$emit('input', this.simplemde.value());
            },
            codemirrorOn(eventName, callback, immediate) {
                immediate && callback(this.simplemde.codemirror);
                this.simplemde.codemirror.on(eventName, callback);
            }
        },
        mounted() {
            let mde = new SimpleMDE({
                element: this.$refs.textarea,
                initialValue: this.value,
                placeholder: this.placeholder,
                spellChecker: false,
                toolbar: this.toolbar,
            });

            let imageBtn = mde.options.toolbar.find(btn => btn.name === 'image');
            (imageBtn||{}).action = this.insertUploadImage;

            mde.codemirror.setSize('auto',this.height);

            this.simplemde = mde;

            this.codemirrorOn('cursorActivity', this.onCursorActivity, true);
            this.codemirrorOn('change', this.onChange, true);
        }
    }
</script>
