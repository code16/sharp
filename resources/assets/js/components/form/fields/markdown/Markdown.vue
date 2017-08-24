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
            value:{
                type: Object,
                default: ()=>{}
            },

            placeholder:String,
            toolbar:Array,
            height:Number,
            maxImageSize:Number,

            readOnly: Boolean,

            locale:String
        },
        inject: [ 'xsrfToken', 'actionsBus', '$tab' ],

        data() {
            return {
                simplemde:null,
                cursorPos:0,
                lastKeydown:0,
                onNextBackspace:noop,

                uploaderId: 0
            }
        },
        watch: {
            /// On form locale change
            locale() {
                this.simplemde.value(this.value);
            }
        },
        computed: {
            indexedFiles() {
                return this.value.files.map( (file,i) => ({ [this.idSymbol]:i, ...file }) );
            },
            codemirror() {
                return this.simplemde.codemirror;
            },
            idSymbol() {
                return Symbol('fileIdSymbol');
            }
        },
        methods : {
            createUploader() {
                let $uploader = new MarkdownUpload({
                    provide: {
                        actionsBus: this.actionsBus
                    },
                    propsData: {
                        id: this.uploaderId++,
                        xsrfToken: this.xsrfToken
                    },
                });

                $uploader.$on('success', this.updateUploader.bind(this));
                $uploader.$on('added', this.refreshCodemirror.bind(this));
                $uploader.$on('remove', this.removeMarker.bind(this));

                return $uploader;
            },

            refreshCodemirror() {
                this.codemirror.refresh();
                this.codemirror.focus();
            },

            removeMarker({ id, marker }) {
                if(marker.explicitlyCleared)
                    return;
                marker.inclusiveLeft = marker.inclusiveRight = false;
                let find = marker.find(), line = find.from.line;
                this.codemirror.replaceRange('',find.from,{line:line+1, ch:0});
                marker.inclusiveLeft = marker.inclusiveRight = true;
                this.codemirror.focus();

                this.value = this.value.files.filter()
            },

            updateUploader({ id, marker }, data) {
                let find = marker.find();

                let content = this.codemirror.getLine(find.from.line);
                this.codemirror.replaceRange(content.replace(/\(.*?\)/,`(${data.name})`),find.from,find.to);

                this.value.files.push({ [this.idSymbol]:id, ...data });
            },

            insertUploadImage() {
                let selection = this.codemirror.getSelection(' ');
                let curLineContent = this.codemirror.getLine(this.cursorPos.line);

                if(selection) {
                    this.codemirror.replaceSelection('');
                    curLineContent = this.codemirror.getLine(this.cursorPos.line);
                    //console.log(selection);
                }

                if(curLineContent.length) {
                    this.codemirror.replaceRange('\n', {
                        line: this.cursorPos.line,
                        ch: curLineContent.length
                    });
                    this.codemirror.setCursor(this.cursorPos.line+1, 0);
                }

                this.codemirror.getInputField().blur();

                let md = `![${selection||''}]()`;

                this.codemirror.replaceRange(`${md}\n`,this.cursorPos);
                this.codemirror.setCursor(this.cursorPos.line-1,0);
                let from = this.cursorPos, to = { line:this.cursorPos.line, ch:this.cursorPos.ch+md.length };

                this.codemirror.addLineClass(this.cursorPos.line, 'wrap', 'SharpMarkdown__upload-line');

                let $uploader = this.createUploader();
                $uploader.marker = this.codemirror.markText(from, to, {
                    replacedWith: $uploader.$mount().$el,
                    clearWhenEmpty: false,
                    inclusiveRight: true,
                    inclusiveLeft: true,
                });

                $uploader.marker.on('beforeCursorEnter',() => this.uploadBeforeCursorEnter($uploader.marker));
                $uploader.inputClick();

                this.codemirror.setCursor(this.cursorPos.line+1, 0);
            },

            uploadBeforeCursorEnter(marker) {
                //console.log(this.lastKeydown.keyCode, this.cursorPos.line);
                if(this.lastKeydown.keyCode === 8 && this.cursorPos.line === 1) {
                    this.onNextBackspace = this.removeMarker(marker);
                }
            },
            onCursorActivity() {
                this.cursorPos = this.codemirror.getCursor();
            },

            onChange() {
                this.$emit('input', {
                    ...this.value, text: this.simplemde.value()
                });
            },

            onBeforeChange(cm, change) {
               // console.log('beforeChange',arguments, this.cursorEntered);
            },

            onKeydown(cm, e) {
                //console.log('key down');
                this.lastKeydown = e;
            },

            onKeyHandled(cm, name, e) {
                if(CodeMirror.keyMap.default[name] === 'undo')
                    return;

                if(name === 'Backspace') {
                    this.onNextBackspace();
                    this.onNextBackspace = noop;
                }
            },
            codemirrorOn(eventName, callback, immediate) {
                immediate && callback(this.codemirror);
                this.codemirror.on(eventName, callback);
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
                this.codemirror.setOption('readOnly', true);
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
                initialValue: this.value.text,
                placeholder: this.placeholder,
                spellChecker: false,
                toolbar: this.toolbar,
                autoDownloadFontAwesome: false,
                status: false
            });

            this.value.files = this.value.files || [];
            this.$emit('input', {
                ...this.value, file: this.indexedFiles
            });
            this.uploaderId = this.value.files.length;

            this.$tab.$on('active', () => {
                this.codemirror.refresh();
            });

            this.codemirror.setSize('auto',this.height);

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
