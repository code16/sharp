import Vue from 'vue';
import Markdown from '../components/form/fields/markdown/Markdown.vue';

import { MockI18n, MockInjections } from './utils';

describe('markdown-field', () => {
    Vue.use(MockI18n);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-markdown :value="value" 
                    :read-only="readOnly" 
                    placeholder="Champ md" 
                    :toolbar="toolbar" 
                    :height="310"
                    :inner-components="{upload:{ maxImageSize:3 }}"
                    field-config-identifier="my_markdown"
                    unique-identifier="my_markdown"
                    :locale="locale"
                    @input="inputEmitted">
                </sharp-markdown>
            </div>
        `;
        // mock range functions
        document.body.createTextRange = () => ({
            getBoundingClientRect: () => ({ }),
            getClientRects: () => ({ })
        });

        MockI18n.mockLangFunction();
    });

    afterEach(() => {
        jest.resetAllMocks();
    });

    describe('basic tests', () => {
        test('can mount Markdown field', async () => {
            await createVm();

            expect(document.body.innerHTML).toMatchSnapshot();
        });

        test('can mount "localized" Markdown field', async () => {
            await createVm();

            expect(document.body.innerHTML).toMatchSnapshot();
        });

        test('can mount "read only" Markdown field', async () => {
            await createVm({
                propsData: {
                    readOnly: true
                }
            });

            expect(document.body.innerHTML).toMatchSnapshot();
        });

        test('update value on locale changed', async () => {
            let $markdown = await createVm({
                propsData: {
                    locale: 'fr'
                },
                data: ()=>({
                    value: { text:'Valeur 1' }
                })
            });

            let { $root:vm, simplemde } = $markdown;

            vm.value.text = 'Valeur 2';
            vm.locale = 'en';

            await Vue.nextTick();

            expect(simplemde.value()).toBe('Valeur 2');
        });

        test('expose appropriate props to simplemde', async () => {
            let $markdown = await createVm({
                propsData: {
                    toolbar: [{ name:'my action' }]
                },
                data: ()=>({
                    value: { text:'Valeur 1' }
                })
            });

            let { simplemde } = $markdown;
            let { textarea } = $markdown.$refs;

            expect(simplemde.options).toMatchObject({
                element: textarea,
                initialValue: 'Valeur 1',
                placeholder: 'Champ md',
                spellChecker: false,
                autoDownloadFontAwesome: false,
                toolbar: [{ name:'my action' }],
            });


        });

        test('bound toolbar buttons custom action properly', async () =>{
            let $markdown = await createVm({
                propsData: {
                    toolbar: [{ name:'image'}]
                }
            });

            let { simplemde } = $markdown;
            $markdown.insertUploadImage = jest.fn();

            expect(simplemde.toolbar[0].action).toBeInstanceOf(Function);
            simplemde.toolbar[0].action();

            expect($markdown.insertUploadImage).toHaveBeenCalled();
        });

        test('set read only properly', async () => {
            let $markdown = await createVm();

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            expect(codemirror.getOption('readOnly')).toBe(false);

            $markdown.setReadOnly();

            expect(codemirror.getOption('readOnly')).toBe(true);
        });

        test('add codemirror event listener properly', async () => {
            let $markdown = await createVm();

            let { simplemde: {codemirror}} = $markdown;

            let callback = jest.fn();
            codemirror.on = jest.fn();

            $markdown.codemirrorOn('event', callback);
            expect(codemirror.on).toHaveBeenCalledWith('event', callback);
            expect(callback).not.toHaveBeenCalled();

            $markdown.codemirrorOn('event', callback, true);
            expect(codemirror.on).toHaveBeenCalledTimes(2);
            expect(codemirror.on).toHaveBeenLastCalledWith('event', callback);
            expect(callback).toHaveBeenCalled();
        });

        test('emit input on text changed', async () => {
            let inputEmitted = jest.fn();
            let $markdown = await createVm({
                methods: {
                    inputEmitted
                }
            });

            let { simplemde } = $markdown;

            simplemde.value('AAA');

            expect(inputEmitted).toHaveBeenLastCalledWith(expect.objectContaining({ text:'AAA' }));
        });
    });


    describe('uploader insertion', () => {

        let mockCodemirror = codemirror => {
            codemirror.markText = jest.fn(() => ({
                on: jest.fn(),
                clear: jest.fn(),
                lines: [{ on: jest.fn() }]
            }));
        };

        let mockMarkdown = setUploader => ({
            'extends': Markdown,
            created() {
                this.insertUploadImage = jest.fn((...args)=>
                    setUploader(Markdown.methods.insertUploadImage.apply(this, args))
                );
            }
        });

        test('insert image uploader and text properly', async () => {
            let $markdown = await createVm();

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            simplemde.value("Lorem Elsass ipsum");
            codemirror.setSelection({ line: 0, ch:5 }, { line: 0, ch:13 });

            let $uploader = $markdown.insertUploadImage({ isInsertion:true });

            expect(simplemde.value()).toBe('Lorem\n![]()\nipsum');
        });

        test('update image uploader and text properly', async () => {
            let $markdown = await createVm();

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            let $uploader = $markdown.insertUploadImage({ isInsertion:true });

            expect(simplemde.value()).toBe('\n![]()\n');

            $uploader.marker.find = jest.fn(() => ({ from:{ line: 1, ch: 0 }, to:{ line: 1, ch:5 }}) );
            $uploader.$emit('success', { name: 'cat.jpg' });

            expect(simplemde.value()).toBe('\n![](cat.jpg)\n');

            expect($markdown.value.files).toEqual([{
                [$markdown.idSymbol]: 0,
                name: 'cat.jpg'
            }]);
        });

        test('parse and insert image uploader and text properly', async () => {
            let $uploaders = [];
            let $markdown = await createVm({
                data: ()=>({
                    value: {
                        text:'aaa\n![Cat](cat.jpg)\nbbb',
                        files:[{
                            name:'cat.jpg',
                            size: 123
                        }]
                    },
                }),
                components: {
                    'sharp-markdown': mockMarkdown(u => $uploaders.push(u))
                }
            });

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            codemirror.setSelection = jest.fn(codemirror.setSelection);

            /// Parse markdown content
            $markdown.$tab.$emit('active');

            expect(simplemde.value()).toBe('aaa\n![Cat](cat.jpg)\nbbb');

            expect($markdown.value.files).toEqual([{
                [$markdown.idSymbol]: 0,
                name:'cat.jpg',
                size: 123
            }]);

            expect(codemirror.setSelection).toHaveBeenCalledTimes(1);
            expect(codemirror.setSelection).toHaveBeenCalledWith({ line:1, ch:0 }, { line:1, ch:15 }, expect.anything());

            expect($markdown.insertUploadImage).toHaveBeenCalledTimes(1);
            expect($markdown.insertUploadImage).toHaveBeenCalledWith({ replaceBySelection:true, data:{ name:'cat.jpg', title:'Cat' } });

            expect($uploaders).toHaveLength(1);

            expect($uploaders[0].value).toMatchObject({
                name: 'cat.jpg',
                size: 123
            });
        });

        test('delete properly', async () => {
            let $uploader = null;
            let $markdown = await createVm({
                data: ()=>({
                    value: {
                        text:'aaa\n![Cat](cat.jpg)\nbbb',
                        files:[{
                            name:'cat.jpg',
                            size: 123
                        }]
                    },
                }),
                components: {
                    'sharp-markdown': mockMarkdown(u => $uploader = u)
                }
            });

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            /// Parse markdown content
            $markdown.$tab.$emit('active');

            $uploader.$destroy = jest.fn($uploader.$destroy);

            $markdown.removeMarker($uploader, { isCMEvent: true });

            expect($uploader.$destroy).toHaveBeenCalled();
            expect($markdown.value.files).toEqual([]);
        });

        test('register delete event and delete properly on backspace ', async () => {
            let $markdown = await createVm();

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            let $uploader = $markdown.insertUploadImage({ isInsertion:true });

            const lineOnMockFn = $uploader.marker.lines[0].on;

            expect(lineOnMockFn).toHaveBeenCalledWith('delete', expect.any(Function));

            let deleteHandler = lineOnMockFn.mock.calls[0][1];

            $markdown.removeMarker = jest.fn($markdown.removeMarker);


            deleteHandler();

            expect($markdown.removeMarker).toHaveBeenCalledTimes(1);
            expect($markdown.removeMarker).toHaveBeenCalledWith($uploader, { isCMEvent: true, relativeFallbackLine: 1 });


        });

        test('Delete properly removing from upload component', async () => {
            let $markdown = await createVm();

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            let $uploader = $markdown.insertUploadImage({ isInsertion:true });

            $markdown.removeMarker = jest.fn($markdown.removeMarker);
            codemirror.replaceRange = jest.fn(codemirror.replaceRange);
            $uploader.marker.find = jest.fn(()=>({from:{ line: 1, ch:0 }, to:{ line:1, ch:5 }}));
            $uploader.$destroy = jest.fn($uploader.$destroy);

            $uploader.$emit('remove');

            expect($markdown.removeMarker).toHaveBeenCalledTimes(1);
            expect($markdown.removeMarker).toHaveBeenCalledWith($uploader, { relativeFallbackLine: 1 });

            expect(codemirror.replaceRange).toHaveBeenCalledTimes(1);
            expect(codemirror.replaceRange).toHaveBeenCalledWith('', { line: 0 }, { line: 2, ch:0 });

            expect($uploader.$destroy).toHaveBeenCalled();
        });

        test('expose appropriate props to markdown upload component', async () =>{
            let $uploaders = [];
            let $markdown = await createVm({
                data: ()=>({
                    value: {
                        text:'aaa\n![Cat](cat.jpg)\nbbb',
                        files:[{
                            name:'cat.jpg',
                            size: 123
                        }]
                    },
                }),
                components: {
                    'sharp-markdown': mockMarkdown(u => $uploaders.push(u))
                }
            });

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            /// Parse markdown content
            $markdown.$tab.$emit('active');

            expect($uploaders[0].$props).toMatchObject({
                id: 0,
                value: {
                    name:'cat.jpg',
                    size: 123
                },
                maxImageSize: 3,
                downloadId: 'my_markdown',
                pendingKey: 'my_markdown.upload.0'
            });

            $markdown.insertUploadImage();

            expect($uploaders[1].$props).toMatchObject({
                id: 1,
                value: undefined,
                maxImageSize: 3,
                downloadId: 'my_markdown',
                pendingKey: 'my_markdown.upload.1'
            });
        });

        test('index files correctly on mounted', async () => {
            let $markdown = await createVm({
                data:() => ({
                    value: {
                        files:[{
                            name:'cat.jpg',
                            size: 123
                        }, {
                            name:'dog.png',
                            size: 456
                        }]
                    }
                })
            });

            expect($markdown.value.files).toEqual([{
                [$markdown.idSymbol]: 0,
                name:'cat.jpg',
                size: 123
            }, {
                [$markdown.idSymbol]: 1,
                name:'dog.png',
                size: 456
            }])
        });

        test('refresh properly', async () => {
            let $markdown = await createVm();

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            let $uploader = $markdown.insertUploadImage();

            codemirror.refresh = jest.fn();
            codemirror.focus = jest.fn();

            $uploader.$emit('refresh');

            expect(codemirror.refresh).toHaveBeenCalled();
            expect(codemirror.focus).toHaveBeenCalled();
        })

        test('update file data properly', async () => {
            let $markdown = await createVm();

            let { simplemde } = $markdown;
            let { codemirror } = simplemde;

            mockCodemirror(codemirror);

            let $uploader = $markdown.insertUploadImage({ isInsertion: true });

            $uploader.marker.find = jest.fn(() => ({ from:{ line: 1, ch: 0 }, to:{ line: 1, ch:5 }}) );

            $uploader.$emit('success', { name: 'cat.jpg' });
            $uploader.$emit('update', { cropData: { } });

            expect($markdown.value.files).toMatchObject([{
                name: 'cat.jpg',
                cropData: { }
            }]);
        })
    });
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [customOptions, MockInjections],

        props:['readOnly', 'toolbar', 'locale'],

        'extends': {
            data:() => ({
                value: {}
            }),
            methods: {
                inputEmitted: ()=>{}
            },
            components: {
                'sharp-markdown': Markdown
            },
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}