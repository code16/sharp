import Vue from 'vue';
import Markdown from '../components/form/fields/markdown/Markdown.vue';

import { MockI18n, MockInjections, wait } from './utils';
import { mount, shallow } from '@vue/test-utils';

import SimpleMDE from 'simplemde';


jest.mock('../components/form/fields/upload/VueClip', ()=>({
    data:()=>({ uploader:({ _uploader:{ hiddenFileInput:{ click:jest.fn() } } }) }),
    render:h=>h()
}));

describe('markdown-field', () => {
    Vue.use(MockI18n);

    function createWrapper(customOptions={}) {
        let { propsData, ...options } = customOptions;
        return mount(Markdown, {
            attachToDocument: true,
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

        test('mount with null localized value', ()=>{
            let wrapper = createLocalizedWrapper({ value: { text: null }, locales:['fr', 'en'], locale:'fr' });
            expect(wrapper.find(Markdown).isVueInstance()).toBe(true);
        });
    });


    describe('uploader insertion', () => {
        function triggerUploadFile(uploader) {
            setTimeout(() => uploader.$emit('added'), 0);
        }

        function mockCodemirror(codemirror, { onWidgetAdded }={}) {
            codemirror.markText = jest.fn((from,to, { $component }) => {
                if($component && onWidgetAdded) {
                    onWidgetAdded($component);
                }
                return {
                    on: jest.fn(),
                    clear: jest.fn(),
                    lines: [{ on: jest.fn() }],
                }
            });
            jest.spyOn(codemirror, 'setSelection');
            jest.spyOn(codemirror, 'replaceRange');
        }

        const mockMixin = {
            watch: {
                codemirror: {
                    sync: true,
                    handler(codemirror) {
                        mockCodemirror(codemirror, {
                            onWidgetAdded: $widgetComp => this._recordedWidgets.push($widgetComp)
                        });
                    }
                },
            },
            methods: {
                createUploader(...args) {
                    let $uploader = Markdown.methods.createUploader.apply(this, args);
                    triggerUploadFile($uploader);
                    return $uploader;
                }
            },
            created() {
                jest.spyOn(this, 'insertUploadImage');
                jest.spyOn(this, 'removeMarker');
                this._recordedWidgets = [];
            }
        };

        test('insert image uploader and text properly', async () => {
            let wrapper = createWrapper({
                mixins: [mockMixin],
                propsData: {
                    value: { text: 'Lorem Elsass ipsum' }
                }
            });

            wrapper.vm.codemirror.setSelection({line: 0, ch: 5}, {line: 0, ch: 13});

            await wrapper.vm.insertUploadImage({ isInsertion: true });

            expect(wrapper.vm.simplemde.value()).toBe('Lorem\n![]()\nipsum');
        });

        test('update image uploader and text properly', async () => {
            let wrapper = createWrapper({
                mixins:[mockMixin]
            });

            let $uploader = await wrapper.vm.insertUploadImage({ isInsertion:true });

            expect(wrapper.vm.simplemde.value()).toBe('\n![]()\n');

            $uploader.marker.find = jest.fn(() => ({ from:{ line: 1, ch: 0 }, to:{ line: 1, ch:5 }}) );
            $uploader.$emit('success', { name: 'cat.jpg' });

            expect(wrapper.vm.simplemde.value()).toBe('\n![](cat.jpg)\n');

            expect(wrapper.vm.value.files).toEqual([{
                [wrapper.vm.idSymbol]: 0,
                name: 'cat.jpg'
            }]);
        });

        test('parse and insert image uploader and text properly', () => {
            let wrapper = createWrapper({
                mixins: [mockMixin],
                propsData: {
                    value: {
                        text:'aaa\n![Cat](cat.jpg)\nbbb',
                        files:[{
                            name:'cat.jpg',
                            size: 123
                        }]
                    },
                },
            });
            /// Parse markdown content
            wrapper.vm.$tab.$emit('active');

            expect(wrapper.vm.simplemde.value()).toBe('aaa\n![Cat](cat.jpg)\nbbb');

            expect(wrapper.vm.value.files).toEqual([{
                [wrapper.vm.idSymbol]: 0,
                name:'cat.jpg',
                size: 123
            }]);

            expect(wrapper.vm.codemirror.setSelection).toHaveBeenCalledTimes(1);
            expect(wrapper.vm.codemirror.setSelection).toHaveBeenCalledWith({ line:1, ch:0 }, { line:1, ch:15 }, expect.anything());

            expect(wrapper.vm.insertUploadImage).toHaveBeenCalledTimes(1);
            expect(wrapper.vm.insertUploadImage).toHaveBeenCalledWith({ replaceBySelection:true, data:{ name:'cat.jpg', title:'Cat' } });

            expect(wrapper.vm._recordedWidgets).toHaveLength(1);
            expect(wrapper.vm._recordedWidgets[0].value).toMatchObject({
                name: 'cat.jpg',
                size: 123
            });
        });

        test('delete properly', async () => {
            let wrapper = createWrapper({
                mixins: [mockMixin],
                propsData: {
                    value: {
                        text:'aaa\n![Cat](cat.jpg)\nbbb',
                        files:[{
                            name:'cat.jpg',
                            size: 123
                        }]
                    },
                }
            });
            /// Parse markdown content
            wrapper.vm.$tab.$emit('active');

            let $uploader = wrapper.vm._recordedWidgets[0];

            jest.spyOn($uploader, '$destroy');

            wrapper.vm.removeMarker($uploader, { isCMEvent: true });

            expect($uploader.$destroy).toHaveBeenCalled();
            expect(wrapper.vm.value.files).toEqual([]);
        });

        test('register delete event and delete properly on event', async () => {
            let wrapper = createWrapper({
                mixins:[mockMixin]
            });
            let $uploader = await wrapper.vm.insertUploadImage({ isInsertion:true });

            const lineOn = $uploader.marker.lines[0].on;

            expect(lineOn).toHaveBeenCalledWith('delete', expect.any(Function));

            let deleteHandler = lineOn.mock.calls[0][1];

            deleteHandler();

            expect(wrapper.vm.removeMarker).toHaveBeenCalledTimes(1);
            expect(wrapper.vm.removeMarker).toHaveBeenCalledWith($uploader, { isCMEvent: true, relativeFallbackLine: 1 });
        });


        test('Delete properly removing from upload component', async () => {
            let wrapper = createWrapper({
                mixins:[mockMixin]
            });

            let $uploader = await wrapper.vm.insertUploadImage({ isInsertion:true });

            $uploader.marker.find = jest.fn(()=>({from:{ line: 1, ch:0 }, to:{ line:1, ch:5 }}));

            jest.spyOn($uploader, '$destroy');

            $uploader.$emit('remove');

            expect(wrapper.vm.removeMarker).toHaveBeenCalledWith($uploader, { relativeFallbackLine: 1 });

            expect(wrapper.vm.codemirror.replaceRange).toHaveBeenCalledWith('', { line: 0 }, { line: 2, ch:0 });

            expect($uploader.$destroy).toHaveBeenCalled();
        });

        test('expose appropriate props to markdown upload component', async () =>{
            let wrapper = createWrapper({
                mixins: [mockMixin],
                propsData: {
                    value: {
                        text:'aaa\n![Cat](cat.jpg)\nbbb',
                        files:[{
                            name:'cat.jpg',
                            size: 123
                        }]
                    },
                },
            });

            /// Parse markdown content
            wrapper.vm.$tab.$emit('active');

            expect(wrapper.vm._recordedWidgets[0].$props).toMatchObject({
                id: 0,
                value: {
                    name:'cat.jpg',
                    size: 123
                },
                maxImageSize: 3,
                downloadId: 'my_markdown',
                pendingKey: 'my_markdown.upload.0'
            });

            await wrapper.vm.insertUploadImage();

            expect(wrapper.vm._recordedWidgets[1].$props).toMatchObject({
                id: 1,
                value: undefined,
                maxImageSize: 3,
                downloadId: 'my_markdown',
                pendingKey: 'my_markdown.upload.1'
            });
        });

        test('index files correctly on mounted', async () => {
            let wrapper = createWrapper({
                propsData: {
                    value: {
                        files:[{
                            name:'cat.jpg',
                            size: 123
                        }, {
                            name:'dog.png',
                            size: 456
                        }]
                    }
                }
            });

            expect(wrapper.vm.value.files).toEqual([{
                [wrapper.vm.idSymbol]: 0,
                name:'cat.jpg',
                size: 123
            }, {
                [wrapper.vm.idSymbol]: 1,
                name:'dog.png',
                size: 456
            }])
        });

        test('refresh properly', async () => {
            let wrapper = createWrapper({ mixins:[mockMixin] });

            let { simplemde } = wrapper.vm;
            let { codemirror } = simplemde;

            let $uploader = await wrapper.vm.insertUploadImage();

            codemirror.refresh = jest.fn();
            codemirror.focus = jest.fn();

            $uploader.$emit('refresh');

            expect(codemirror.refresh).toHaveBeenCalled();
            expect(codemirror.focus).toHaveBeenCalled();
        });

        test('update file data properly', async () => {
            let wrapper = createWrapper({ mixins:[mockMixin] });
            let $uploader = await wrapper.vm.insertUploadImage({ isInsertion: true });

            $uploader.marker.find = jest.fn(() => ({ from:{ line: 1, ch: 0 }, to:{ line: 1, ch:5 }}) );

            $uploader.$emit('success', { name: 'cat.jpg' });
            $uploader.$emit('update', { cropData: { } });

            expect(wrapper.vm.value.files).toMatchObject([{
                name: 'cat.jpg',
                cropData: { }
            }]);
        })

    });
});

