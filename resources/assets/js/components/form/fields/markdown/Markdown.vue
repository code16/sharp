<template>
    <div class="SharpMarkdown" :class="{'SharpMarkdown--read-only':readOnly}">
        <div class="SharpModule__inner">
            <textarea ref="textarea"></textarea>
        </div>
    </div>
</template>

<script>
    import SimpleMDE from 'simplemde';
    import MarkdownUpload from './MarkdownUpload';
    import CodeMirror from 'codemirror';

    import { lang } from '../../../../mixins/Localization';

    const noop = ()=>{};

    export default {
        name: 'SharpMarkdown',
        props: {
            value:String,

            placeholder:String,
            toolbar:Array,
            height:Number,
            maxImageSize:Number,

            readOnly: Boolean,

            locale:String
        },
        inject: [ 'xsrfToken' ],

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
                        },
                        xsrfToken: this.xsrfToken
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
               // console.log('beforeChange',arguments, this.cursorEntered);
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
            },

            localizeToolbar() {
                this.simplemde.toolbar.forEach(icon => {
                    if(typeof icon === 'object') {
                        let lName = icon.name.replace(/-/g,'_');
                        icon.title = lang(`form.markdown.icons.${lName}.title`);
                    }
                });
                this.$el.querySelector('.editor-toolbar').remove();
                this.simplemde.createToolbar();
            },
            setReadOnly() {
                this.simplemde.codemirror.setOption('readOnly', true);
                this.simplemde.toolbar.forEach(icon => typeof icon === 'object' && (icon.action = noop));
            },
            bindImageAction() {
                let imageBtn = this.simplemde.toolbar.find(btn => btn.name === 'image');
                (imageBtn||{}).action = this.insertUploadImage;
            }
        },
        mounted() {
            this.simplemde = new SimpleMDE({
                element: this.$refs.textarea,
                initialValue: this.value,
                placeholder: this.placeholder,
                spellChecker: false,
                toolbar: this.toolbar,
                autoDownloadFontAwesome: false
            });

            this.simplemde.codemirror.setSize('auto',this.height);

            if(this.readOnly) {
                this.setReadOnly();
            }
            /// Custom mde setup
            this.localizeToolbar();
            this.bindImageAction();

            //// CM events bindings
            this.codemirrorOn('cursorActivity', this.onCursorActivity, true);
            this.codemirrorOn('change', this.onChange, true);
            this.codemirrorOn('beforeChange',this.onBeforeChange);

            this.codemirrorOn('keydown', this.onKeydown);
            this.codemirrorOn('keyHandled', this.onKeyHandled);
            //console.log(this);
        }
    }
</script>
