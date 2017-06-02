<template>
    <div class="SharpMarkdown">
        <textarea ref="textarea"></textarea>
    </div>
</template>

<script>
    import SimpleMDE from 'simplemde';
    import MarkdownUpload from './MarkdownUpload';
    import CodeMirror from 'codemirror';

    const noop = ()=>{};

    export default {
        name: 'SharpMarkdown',
        props: {
            value:String,

            placeholder:String,
            toolbar:Array,
            height:Number,
            maxImageSize:Number,

            locale:String
        },
        data() {
            return {
                simplemde:null,
                cursorPos:0,
                lastKeydown:0,
                onNextBackspace:noop
            }
        },
        watch: {
            /// On form locale change
            locale() {
                this.simplemde.value(this.value);
            }
        },
        methods : {
            createUploader(cm) {
                return new MarkdownUpload({
                    propsData: {
                        onSuccess(file) {
                            let find = this.marker.find();
                            //console.log(this.marker);
                            let content = cm.getLine(find.from.line);
                            cm.replaceRange(content.replace(/\(.*?\)/,`(${file.name})`),find.from,find.to);
                        },
                        onAdded() {
                            cm.refresh();
                            cm.focus();
                        },
                        onRemoved() {
                            if(this.marker.explicitlyCleared)
                                return;
                            this.remove();
                        }
                    },
                    methods: {
                        remove() {
                            this.marker.inclusiveLeft = this.marker.inclusiveRight = false;
                            let find = this.marker.find(), line = find.from.line;
                            cm.replaceRange('',find.from,{line:line+1, ch:0});
                            this.marker.inclusiveLeft = this.marker.inclusiveRight = true;
                            cm.focus();
                        }
                    }
                });
            },
            insertUploadImage({codemirror}) {
                let cm = codemirror;
                let selection = cm.getSelection(' ');
                let curLineContent = cm.getLine(this.cursorPos.line);

                if(selection) {
                    cm.replaceSelection('');
                    curLineContent = cm.getLine(this.cursorPos.line);
                    //console.log(selection);
                }

                if(curLineContent.length) {
                    cm.replaceRange('\n', {
                        line: this.cursorPos.line,
                        ch: curLineContent.length
                    });
                    cm.setCursor(this.cursorPos.line+1, 0);
                }

                cm.getInputField().blur();

                let md = `![${selection||''}]()`;

                cm.replaceRange(`${md}\n`,this.cursorPos);
                cm.setCursor(this.cursorPos.line-1,0);
                let from = this.cursorPos, to = { line:this.cursorPos.line, ch:this.cursorPos.ch+md.length };

                let uploader = this.createUploader(cm);
                uploader.marker = cm.markText(from, to, {
                    replacedWith: uploader.$mount().$el ,
                    clearWhenEmpty: false,
                    inclusiveRight: true,
                    inclusiveLeft: true,
                });

                uploader.marker.on('beforeCursorEnter',this.uploadBeforeCursorEnter(uploader));
                uploader.inputClick();

                cm.setCursor(this.cursorPos.line+1, 0);
            },
            uploadBeforeCursorEnter(uploader) {
                return _ => {
                    console.log(this.lastKeydown.keyCode, this.cursorPos.line);
                    if(this.lastKeydown.keyCode === 8 && this.cursorPos.line === 1) {
                        this.onNextBackspace = uploader.remove.bind(uploader);
                    }
                    this.cursorEntered=true;
                }
            },
            onCursorActivity(cm) {
                this.cursorPos = cm.getCursor();
            },
            onChange(cm) {
                this.$emit('input', this.simplemde.value());
            },
            onBeforeChange(cm, change) {
                console.log('beforeChange',arguments, this.cursorEntered);
            },
            onKeydown(cm, e) {
                console.log('key down');
                this.lastKeydown = e;
            },
            onKeyHandled(cm, name, e) {
                console.log('key handled',arguments);
                if(CodeMirror.keyMap.default[name] === 'undo') {

                }
                else if(name === 'Backspace') {
                    this.onNextBackspace();
                    this.onNextBackspace = noop;
                }
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
                autoDownloadFontAwesome: false
            });

            let imageBtn = mde.options.toolbar.find(btn => btn.name === 'image');
            (imageBtn||{}).action = this.insertUploadImage;

            mde.codemirror.setSize('auto',this.height);

            this.simplemde = mde;

            this.codemirrorOn('cursorActivity', this.onCursorActivity, true);
            this.codemirrorOn('change', this.onChange, true);
            this.codemirrorOn('beforeChange',this.onBeforeChange);

            this.codemirrorOn('keydown', this.onKeydown);
            this.codemirrorOn('keyHandled', this.onKeyHandled);

            console.log(this);
        }
    }
</script>
