<template>
    <div class="SharpMarkdown" :class="{'SharpMarkdown--read-only':readOnly}">
        <div class="SharpModule__inner">
            <template v-if="isLocalized">
                <div v-for="loc in locales" v-show="locale === loc">
                    <textarea :id="localizedTextareaRef(loc)" :ref="localizedTextareaRef(loc)"></textarea>
                </div>
            </template>
            <template v-else>
                <textarea ref="textarea"></textarea>
            </template>
        </div>
    </div>
</template>

<script>
    import SimpleMDE from 'simplemde';
    import MarkdownUpload from './MarkdownUpload';

    import Widget from './MarkdownWidget';

    import { lang } from '../../../../mixins/Localization';
    import localize from '../../../../mixins/localize/editor';

    const noop = ()=>{};

    const fileIdSymbol = Symbol('fileIdSymbol');

    export default {
        name: 'SharpMarkdown',

        mixins: [ localize({ textProp:'text' }) ],

        props: {
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
            value:{
                type: Object,
                default: ()=>({})
            },

            placeholder:String,
            toolbar:Array,
            height:{
                type: Number,
                default: 300
            },

            innerComponents:Object,

            readOnly: Boolean,
        },

        inject: ['$tab'],

        data() {
            return {
                simplemdeInstances: {},
                cursorPos:{ line:0, ch:0 },

                uploaderId: (this.value.files||[]).length,
            }
        },
        watch: {
            /// On form locale change
            async locale() {
                if(this.isLocalized) {
                    await this.$nextTick();
                    this.refreshOnExternalChange();
                }
            }
        },
        computed: {
            simplemde() {
                return this.isLocalized ? this.simplemdeInstances[this.locale] : this.simplemdeInstances;
            },
            codemirror() {
                return (this.simplemde||{}).codemirror;
            },
            idSymbol() {
                return fileIdSymbol;
            },
            filesByName() {
                return this.value.files.reduce((res, file) => {
                    res[file.name] = file;
                    return res;
                }, {});
            },
            indexByFileId() {
                return this.value.files.reduce((res, file, index) => {
                    res[file[this.idSymbol]] = index;
                    return res;
                }, {});
            },
            text() {
                return this.localizedText;
            }
        },
        methods : {
            localizedTextareaRef(locale) {
                return `textarea_${locale}`;
            },
            indexedFiles() {
                return (this.value.files||[]).map( (file,i) => ({ [this.idSymbol]:i, ...file }) );
            },
            createUploader({ id, value, removeOptions }) {
                let $uploader = new MarkdownUpload({
                    mixins: [ Widget(this) ],
                    propsData: {
                        id, value,
                        ...this.innerComponents.upload,
                        downloadId: this.fieldConfigIdentifier,
                        pendingKey: `${this.uniqueIdentifier}.upload.${id}`
                    }
                });

                $uploader.$on('success', file => this.updateUploaderData($uploader, file));
                $uploader.$on('refresh', () => this.refreshCodemirror());
                $uploader.$on('remove', () => this.removeMarker($uploader, removeOptions));
                $uploader.$on('update', data => this.updateFileData($uploader, data));
                $uploader.$on('active', () => this.setMarkerActive($uploader));
                $uploader.$on('inactive', () => this.setMarkerInactive($uploader));
                $uploader.$on('escape', () => this.escapeMarker());
                //console.log('create uploader', id, $uploader);

                $uploader.$mount();

                return $uploader;
            },
            createUserUploader(options) {
                let uploader = null;
                if(this.lastUploader) {
                    this.lastUploader.$destroy();
                }
                uploader = this.lastUploader = this.createUploader(options);
                uploader.inputClick();
                return new Promise(resolve => {
                    uploader.$on('added', () => {
                        this.lastUploader = null;
                        resolve(uploader)
                    });
                });
            },

            refreshCodemirror() {
                console.log('refresh codemirror');
                this.codemirror.refresh();
                this.codemirror.focus();
            },

            removeMarker($uploader, { isCMEvent, relativeFallbackLine } = {}) {
                let { id, marker } = $uploader;

                if(marker.explicitlyCleared)
                    return;

                if(!isCMEvent) {
                    marker.inclusiveLeft = marker.inclusiveRight = false;
                    let find = marker.find(), line = find.from.line;
                    let fallbackLine = line-relativeFallbackLine;

                    this.codemirror.replaceRange('',{ line: fallbackLine },{line:line+1, ch:0});
                    marker.inclusiveLeft = marker.inclusiveRight = true;
                    marker.clear();
                    this.codemirror.focus();
                }

                $uploader.$destroy();
                this.value.files = this.value.files.filter(f => f[this.idSymbol] !== id);
            },

            escapeMarker() {
                this.codemirror.focus();
            },

            updateUploaderData({ id, marker }, data) {
                let find = marker.find();

                let content = this.codemirror.getLine(find.from.line);
                this.codemirror.replaceRange(content.replace(/\(.*?\)/,`(${data.name})`),find.from,find.to);

                this.value.files.push({ [this.idSymbol]:id, ...data });
            },

            setMarkerActive({ marker }) {
                this.codemirror.addLineClass(marker.lines[0], 'wrap', 'SharpMarkdown__line--active');
            },

            setMarkerInactive({ marker }) {
                this.codemirror.removeLineClass(marker.lines[0], 'wrap', 'SharpMarkdown__line--active');
            },

            updateFileData({ id }, data) {
                let fileIndex = this.indexByFileId[id];
                let file = this.value.files[fileIndex];
                this.$set(this.value.files, fileIndex, { ...file, ...data });
            },

            // replaceBySelection : put the selected text inside the marker (existing tag from parsing)
            // data : contains de title and name from the image tag
            // isInsertion : if the user click on 'insert image' button
            async insertUploadImage({ replaceBySelection, data, isInsertion } = {}) {
                let selection = this.codemirror.getSelection(' ');
                let curLineContent = this.codemirror.getLine(this.cursorPos.line) || '';

                let options = {
                    id: data ? this.filesByName[data.name][this.idSymbol] : this.uploaderId++,
                    value: data && this.filesByName[data.name],
                    removeOptions: {
                        relativeFallbackLine: 1
                    }
                };
                let $uploader = isInsertion
                    ? await this.createUserUploader(options)
                    : this.createUploader(options);

                if(selection) {
                    this.codemirror.replaceSelection('');
                    curLineContent = this.codemirror.getLine(this.cursorPos.line);
                }

                if(curLineContent.length
                    || this.cursorPos.line === 0 && this.cursorPos.ch === 0
                    || this.codemirror.findMarksAt({ line: this.cursorPos.line-1, ch:0 }).length
                ) {
                    this.codemirror.replaceRange('\n', this.cursorPos);
                }

                this.codemirror.getInputField().blur();

                let md = replaceBySelection
                    ? selection
                    : '![]()';// `![${selection||''}]()`;   take selection as title


                let afterNewLinesCount = isInsertion || this.cursorPos.line === this.codemirror.lineCount()-1 ? 1 : 0;

                md += '\n'.repeat(afterNewLinesCount);

                this.codemirror.replaceRange(md,this.cursorPos);
                this.codemirror.setCursor(this.cursorPos.line-afterNewLinesCount,0, { scroll:!!isInsertion });
                let from = this.cursorPos, to = { line:this.cursorPos.line, ch:this.cursorPos.ch+md.length };

                $uploader.marker = this.codemirror.markText(from, to, {
                    replacedWith: $uploader.$el,
                    clearWhenEmpty: false,
                    inclusiveRight: true,
                    inclusiveLeft: true,
                    $component: $uploader
                });

                this.codemirror.addLineClass($uploader.marker.lines[0], 'wrap', 'SharpMarkdown__upload-line');
                $uploader.marker.lines[0].on('delete', ()=>this.removeMarker($uploader, { isCMEvent: true, relativeFallbackLine:1 }));

                return $uploader;
            },

            onCursorActivity() {
                if(this.codemirror) {
                    this.cursorPos = this.codemirror.getCursor();
                }
            },

            onChange() {
                this.codemirror && this.$emit('input', this.localizedValue(this.codemirror.getValue()));
            },

            onBeforeChange(cm, change) {
                //console.log(change);
                if(change && change.origin && change.origin.includes('delete')) {
                    let markers = cm.findMarks(change.from, change.to);
                    console.log(markers);
                    if(markers.length) {
                        markers.forEach(marker => {
                            if(marker.$component) {
                                change.cancel();
                                marker.$component.$emit('delete-intent');
                            }
                        });
                    }
                }
            },

            codemirrorOn(codemirror, eventName, callback, immediate) {
                immediate && callback(codemirror);
                codemirror.on(eventName, callback);
            },

            localizeToolbar(simplemde) {
                simplemde.toolbar.forEach(icon => {
                    if(typeof icon === 'object') {
                        let lName = icon.name.replace(/-/g,'_');
                        icon.title = lang(`form.markdown.icons.${lName}.title`);
                    }
                });
                simplemde.gui.toolbar.remove();
                simplemde.createToolbar();
            },
            setReadOnly(simplemde) {
                simplemde.codemirror.setOption('readOnly', true);
                simplemde.toolbar.forEach(icon => typeof icon === 'object' && (icon.action = noop));
            },
            bindImageAction(simplemde) {
                let imageBtn = simplemde.toolbar.find(btn => btn.name === 'image');
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
                    this.codemirror.setSelection(range.start, range.end, { scroll: false });
                    this.insertUploadImage({ replaceBySelection:true, data });
                });
                return images;
            },

            refreshOnExternalChange() {
                if(!this.simplemde.parsed) {
                    let images = this.parse();
                    this.simplemde.parsed = true;
                    if (images.length) {
                        // reset the scroll position because it change on widget insertion
                        this.$nextTick(() => window.scrollTo(0, 0));
                    }
                }

                setTimeout(()=>this.codemirror.refresh(), 50);
            },

            createSimpleMDE({ element, initialValue }) {
                //console.log('initialValue',initialValue);
                let simplemde = new SimpleMDE({
                    element,
                    initialValue,
                    placeholder: this.placeholder,
                    spellChecker: false,
                    toolbar: this.toolbar,
                    autoDownloadFontAwesome: false,
                    status: false
                });
                if(this.readOnly) {
                    this.setReadOnly(simplemde);
                }
                this.localizeToolbar(simplemde);
                this.bindImageAction(simplemde);

                this.initCM(simplemde.codemirror);

                return simplemde;
            },

            initCM(codemirror) {
                codemirror.setSize('auto',this.height);

                //// CM events bindings
                this.codemirrorOn(codemirror, 'cursorActivity', this.onCursorActivity, true);
                this.codemirrorOn(codemirror, 'change', this.onChange);
                this.codemirrorOn(codemirror, 'beforeChange',this.onBeforeChange);
            }
        },
        mounted() {
            if(this.isLocalized) {
                this.simplemdeInstances = this.locales.reduce((res, locale)=>({
                    ...res, [locale]: this.createSimpleMDE({
                        element: this.$refs[this.localizedTextareaRef(locale)][0],
                        initialValue: (this.value.text || {})[locale]
                    })
                }), {});
            }
            else this.simplemdeInstances = this.createSimpleMDE({
                element: this.$refs.textarea,
                initialValue: this.value.text
            });

            this.value.files = this.indexedFiles();

            if(this.$tab) {
                this.$tab.$once('active', () => this.refreshOnExternalChange());
            }
            else {
                this.$nextTick(() => this.refreshOnExternalChange());
            }
        }
    }
</script>
