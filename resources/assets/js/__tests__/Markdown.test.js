import Vue from 'vue';
import Markdown from '../components/form/fields/markdown/Markdown.vue';

import { MockI18n, MockInjections } from './utils';
import { mount } from '@vue/test-utils';

import SimpleMDE from 'simplemde';

describe('markdown-field', () => {
    Vue.use(MockI18n);

    function createWrapper(customOptions={}) {
        let { propsData, ...options } = customOptions;
        return mount(Markdown, {
            provide: MockInjections.provide,
            propsData: {
                value: { text:'' },
                readOnly: false,
                placeholder: 'Champ md',
                height: 310,
                innerComponents: { upload:{ maxImageSize:3 } },
                fieldConfigIdentifier: 'my_markdown',
                uniqueIdentifier: 'my_markdown',
                ...propsData
            },
            ...options
        });
    }

    function createLocalizedWrapper({ value, locale, locales }) {
        return createWrapper({
            propsData: { value, locale },
            computed: {
                isLocalized:()=>true,
                locales:()=>locales
            }
        });
    }

    beforeEach(()=>{
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
        test('can mount Markdown field', () => {
            let wrapper = createWrapper();

            expect(wrapper.html()).toMatchSnapshot();
        });

        test('can mount "localized" Markdown field', () => {
            let wrapper = createLocalizedWrapper({
                value: { text:{ fr:'', en: '' } },
                locales: ['fr', 'en'],
                locale: 'fr'
            });

            expect(wrapper.html()).toMatchSnapshot();
        });


        test('can mount "read only" Markdown field', () => {
           let wrapper = createWrapper({
                propsData: {
                    readOnly: true
                }
            });

            expect(wrapper.html()).toMatchSnapshot();
        });

        test('handle locale changed', async () => {
            let wrapper = createLocalizedWrapper({
                value: { text:{ fr:'', en: '' } },
                locales:['fr', 'en'],
                locale: 'fr',
            });

            wrapper.setProps({ locale:'en' });
            wrapper.setMethods({ refreshOnExternalChange: jest.fn() });

            await Vue.nextTick();

            expect(wrapper.vm.refreshOnExternalChange).toHaveBeenCalled();
        });

        test('localized: simplemde instances, current simplemde', ()=>{
            let wrapper = createLocalizedWrapper({
                value: { text:{ fr:'', en: '' } },
                locales:['fr', 'en'],
                locale: 'fr',
            });

            expect(wrapper.vm.simplemdeInstances).toEqual({ 'fr':expect.any(SimpleMDE), 'en':expect.any(SimpleMDE) });
            expect(wrapper.vm.simplemde).toBe(wrapper.vm.simplemdeInstances['fr']);
            expect(wrapper.vm.codemirror).toBe(wrapper.vm.simplemdeInstances['fr'].codemirror);

            wrapper.setProps({ locale: 'en' });

            expect(wrapper.vm.simplemde).toBe(wrapper.vm.simplemdeInstances['en']);
            expect(wrapper.vm.codemirror).toBe(wrapper.vm.simplemdeInstances['en'].codemirror);
        });

        test('simplemde instances, current simplemde', ()=>{
            let wrapper = createWrapper();
            expect(wrapper.vm.simplemdeInstances).toEqual(expect.any(SimpleMDE));
            expect(wrapper.vm.simplemde).toEqual(wrapper.vm.simplemdeInstances);
            expect(wrapper.vm.codemirror).toEqual(wrapper.vm.simplemdeInstances.codemirror);
        });

        test('filesByName', ()=>{
            let wrapper = createWrapper({
                propsData: {
                    value: {
                        files: [{ name:'aaa.jpg' }, { name:'bbb.jpg' }]
                    }
                }
            });
            expect(wrapper.vm.filesByName).toMatchObject({
                'aaa.jpg': { name:'aaa.jpg' },
                'bbb.jpg': { name:'bbb.jpg' }
            });
        });

        test('indexByFileId', ()=>{
            let wrapper = createWrapper();
            let id = wrapper.vm.idSymbol;
            wrapper.setProps({
                value: {
                    files: [{ [id]:1 }, { [id]:6 }]
                }
            });
            expect(wrapper.vm.indexByFileId).toEqual({
                1: 0,
                6: 1
            });
        });

        test('createSimpleMDE', async () => {
            let wrapper = createWrapper({
                propsData:{
                    value: { text: 'value' },
                    toolbar: [{ name:'my action' }]
                },
                created() {
                    jest.spyOn(this, 'createSimpleMDE');
                }
            });
            let { element } = wrapper.find({ ref:'textarea' });
            expect(wrapper.vm.createSimpleMDE).toHaveBeenCalledWith({ element, initialValue:'value' });
            expect(wrapper.vm.simplemdeInstances.options).toMatchObject({
                initialValue: 'value',
                placeholder: 'Champ md',
                spellChecker: false,
                autoDownloadFontAwesome: false,
                toolbar: [{ name:'my action' }],
            });
        });

        test('localizedTextareaRef', ()=>{
            let wrapper = createWrapper();
            expect(wrapper.vm.localizedTextareaRef('fr')).toEqual('textarea_fr');
        });

        test('bound toolbar buttons custom action properly', () =>{
            let wrapper = createWrapper({
                propsData: {
                    toolbar: [{ name:'image'}]
                }
            });

            let { simplemde } = wrapper.vm;
            wrapper.vm.insertUploadImage = jest.fn();

            expect(simplemde.toolbar[0].action).toBeInstanceOf(Function);
            simplemde.toolbar[0].action();

            expect(wrapper.vm.insertUploadImage).toHaveBeenCalled();
        });

        test('set read only properly', async () => {
            let wrapper = createWrapper();

            let { simplemde, codemirror } = wrapper.vm;

            expect(codemirror.getOption('readOnly')).toBe(false);

            wrapper.vm.setReadOnly(simplemde);

            expect(codemirror.getOption('readOnly')).toBe(true);
        });

        test('add codemirror event listener properly', () => {
            let wrapper = createWrapper();

            let { codemirror } = wrapper.vm;

            let callback = jest.fn();
            codemirror.on = jest.fn();

            wrapper.vm.codemirrorOn(codemirror, 'event', callback);
            expect(codemirror.on).toHaveBeenCalledWith('event', callback);
            expect(callback).not.toHaveBeenCalled();

            wrapper.vm.codemirrorOn(codemirror, 'event', callback, true);
            expect(codemirror.on).toHaveBeenCalledTimes(2);
            expect(codemirror.on).toHaveBeenLastCalledWith('event', callback);
            expect(callback).toHaveBeenCalled();
        });

        test('emit input on text changed', async () => {
            let wrapper = createWrapper();

            let { simplemde } = wrapper.vm;

            wrapper.setMethods({
                localizedValue: jest.fn(()=>'localizedValue')
            });

            simplemde.value('AAA');

            expect(wrapper.emitted().input).toHaveLength(1);
            expect(wrapper.emitted().input[0]).toEqual(['localizedValue'])
        });


        test('has localized editor mixin with appropriate text prop', ()=>{
            let wrapper = mount(Markdown, MockInjections);
            expect(wrapper.vm.$options._localizedEditor).toEqual({ textProp:'text' });
        });
    });

/*
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
*/
});

