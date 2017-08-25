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
            codemirror() {
                return this.simplemde.codemirror;
            },
            idSymbol() {
                return Symbol('fileIdSymbol');
            }
        },
        methods : {
            indexedFiles() {
                return (this.value.files||[]).map( (file,i) => ({ [this.idSymbol]:i, ...file }) );
            },
            createUploader(value) {
                let $uploader = new MarkdownUpload({
                    provide: {
                        actionsBus: this.actionsBus
                    },
                    propsData: {
                        id: this.uploaderId++,
                        xsrfToken: this.xsrfToken,
                        value
                    },
                });

                $uploader.$on('success', this.updateUploader.bind(this, $uploader));
                $uploader.$on('added', this.refreshCodemirror.bind(this, $uploader));
                $uploader.$on('remove', this.removeMarker.bind(this, $uploader));

                return $uploader;
            },

            refreshCodemirror() {
                this.codemirror.refresh();
                this.codemirror.focus();
            },

            removeMarker($uploader, { fromBackspace } = {}) {
                let { id, marker } = $uploader;

                if(marker.explicitlyCleared)
                    return;

                if(!fromBackspace) {
                    marker.inclusiveLeft = marker.inclusiveRight = false;
                    let find = marker.find(), line = find.from.line;
                    this.codemirror.replaceRange('',find.from,{line:line+1, ch:0});
                    marker.inclusiveLeft = marker.inclusiveRight = true;
                    marker.clear();
                    this.codemirror.focus();
                }

                $uploader.$destroy();
                this.value.files = this.value.files.filter(f => f[this.idSymbol] !== id);
            },

            updateUploader({ id, marker }, data) {
                let find = marker.find();

                let content = this.codemirror.getLine(find.from.line);
                this.codemirror.replaceRange(content.replace(/\(.*?\)/,`(${data.name})`),find.from,find.to);

                this.value.files.push({ [this.idSymbol]:id, ...data });
            },

            insertUploadImage({ replaceBySelection, data, isInsertion } = {}) {
                let selection = this.codemirror.getSelection(' ');
                let curLineContent = this.codemirror.getLine(this.cursorPos.line);

                if(selection) {
                    this.codemirror.replaceSelection('');
                    curLineContent = this.codemirror.getLine(this.cursorPos.line);
                }

                if(curLineContent.length) {
                    this.codemirror.replaceRange('\n', {
                        line: this.cursorPos.line,
                        ch: this.cursorPos.ch
                    });
                    //this.codemirror.setCursor(this.cursorPos.line+1, 0);
                }

                this.codemirror.getInputField().blur();

                let md = replaceBySelection ? selection : `![${selection||''}]()`;

                if(isInsertion) {
                    md += '\n';
                }

                this.codemirror.replaceRange(md,this.cursorPos);
                this.codemirror.setCursor(this.cursorPos.line+(isInsertion?-1:0),0);
                let from = this.cursorPos, to = { line:this.cursorPos.line, ch:this.cursorPos.ch+md.length };

                this.codemirror.addLineClass(this.cursorPos.line, 'wrap', 'SharpMarkdown__upload-line');


                let $uploader = this.createUploader(data);
                //console.log($uploader);
                $uploader.marker = this.codemirror.markText(from, to, {
                    replacedWith: $uploader.$mount().$el,
                    clearWhenEmpty: false,
                    inclusiveRight: true,
                    inclusiveLeft: true,
                });

                $uploader.marker.on('beforeCursorEnter',() => this.uploadBeforeCursorEnter($uploader));

                if(!data)
                    $uploader.inputClick();

                this.codemirror.setCursor(this.cursorPos.line+1, 0);
            },

            uploadBeforeCursorEnter($uploader) {
                //debugger
                console.log(this.lastKeydown.keyCode, this.cursorPos.line);
                if(this.lastKeydown.keyCode === 8) {
                    this.removeMarker($uploader, { fromBackspace:true });
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
              // debugger
            },

            onKeydown(cm, e) {
                //console.log('key down');
                this.lastKeydown = e;
            },

            onKeyHandled(cm, name, e) {

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
                (imageBtn||{}).action = () => this.insertUploadImage({ isInsertion:true });
            },

            parse() {
                let images = [];
                this.codemirror.eachLine(lineHandler => {
                    let { text } = lineHandler;
                    let line = this.codemirror.getLineNumber(lineHandler);
                    let regex = /!\[(.*?)\]\((.*?)\)/g;
                    let match = regex.exec(text);

                    if(match) {
                        let { index, 0: { length }, 1:title, 2:name  } = match;
                        //console.log(match);
                        images.push({
                            range: { start:{ ch:index ,line }, end:{ch:index+length, line} },
                            data: {
                                name,
                                title
                            }
                        })
                    }
                });

                images.reverse().forEach(({ range, data }) => {
                    this.codemirror.setSelection(range.start, range.end);
                    this.insertUploadImage({ replaceBySelection:true, data });
                });
            },

            refreshOnExternalChange() {
                this.codemirror.refresh();
                this.parse();
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

            this.value.files = this.indexedFiles();
            this.uploaderId = this.value.files.length;


            this.$tab.$once('active', () => this.refreshOnExternalChange());

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
            console.log(this);
        }
    }
</script>
