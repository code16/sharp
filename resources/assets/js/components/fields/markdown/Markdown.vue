<template>
    <div class="SharpMarkdown" :style="style">
        <textarea ref="textarea"></textarea>
    </div>
</template>

<script>
    import SimpleMDE from 'simplemde';
    import MarkdownUpload from './MarkdownUpload';

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
                uploadWidgets:[]
            }
        },
        computed: {
            style() {
                let s = {};
                if(this.height)
                    s.height = `${this.height}px`;
                return s;
            }
        },
        methods : {
            insertUploadImage({codemirror}) {
                let pos = codemirror.getCursor();
                let uploadIdx = this.uploadWidgets.length;
                let selection = codemirror.getSelection(' ');

                let lh = codemirror.getLineHandle(pos.line);
                /*if(lh.widgets || (lh.widgets && lh.widgets.length)) {
                    codemirror.focus();
                    return;
                }*/


                let widgets = this.uploadWidgets;

                let uploader = new MarkdownUpload({
                    propsData: {
                        onSuccess(file) {

                        },
                        onAdded() {
                            codemirror.refresh();
                        },
                        onRemoved() {
                            codemirror.removeLineWidget(widgets[uploadIdx]);
                            widgets.splice(uploadIdx,1);
                            codemirror.focus();
                        }
                    }
                });

                if(selection)
                    codemirror.replaceSelection('');

                let from=codemirror.getCursor('from'), to=codemirror.getCursor('to');
                console.log(from,to);
                codemirror.replaceRange('\n\n',to);
                let mpos = {line:from.line+1, ch:from.ch+1};
                codemirror.markText(mpos,mpos,{replacedWith:uploader.$mount().$el, clearWhenEmpty:false});
                codemirror.setCursor({line:to.line+1, ch:0});
                codemirror.focus();

                //this.uploadWidgets.push(widget);
            }
        },
        mounted() {
            this.simplemde = new SimpleMDE({
                element: this.$refs.textarea,
                initialValue: this.value,
                placeholder: this.placeholder,
                spellChecker: false,
                toolbar: this.toolbar,
            });
            let imageBtn = this.simplemde.options.toolbar.find(btn => btn.name === 'image');
            if(imageBtn) {
                imageBtn.action = this.insertUploadImage
            }

            let cm=this.simplemde.codemirror;
            /*cm.on('change', () => {
                cm.eachLine(line=>{
                    //if(line.widgets)
                    console.log(line,line.widgets);
                });
            });*/
            //console.log(cm);
        }
    }
</script>
