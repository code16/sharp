import Vue from 'vue';
import { MockInjections, MockI18n, MockBootstrapVue, wait } from "./utils";

Vue.use(MockBootstrapVue);

import VueClip from '../components/form/fields/upload/VueClip';
import { nextRequestFulfilled } from './utils/moxios-utils';

import moxios from 'moxios';


/** Vue clip native methods
// addedFile(File)
// removedFile(File)
// sending()
// complete()
// error()
// uploadProgress()
// thumbnail()
// drop()
// dragEnter()
// dragLeave()
// totalUploadProgress()
// maxFilesExceeded()
// queueComplete()
*/


function mockFile(props) {
    return {
        upload: {},
        ...props,
    }
}

describe('vue-clip',() => {
    Vue.use(MockI18n);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-vue-clip :value="value"
                    :options="{ url:'/' }"
                    download-id="my_upload" 
                    pending-key="my_upload.0" 
                    :ratio-x="ratioX" 
                    :ratio-y="ratioY"
                    :read-only="readOnly">
                </sharp-vue-clip>
            </div>
        `;
        MockI18n.mockLangFunction();
    });


    it('can mount VueClip component', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount read-only VueClip component', async () => {
        await createVm({
            propsData: {
                readOnly: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount initially non image VueClip component', async () => {
        await createVm({
            data: () => ({
                value: {
                    name: 'Fichier.pdf',
                    size: 15000
                }
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount initially image VueClip component', async () => {
        await createVm({
            propsData: {
                ratioX: 1, ratioY: 2
            },
            data: () => ({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg',
                    size: 15000
                }
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('can mount "in progress" VueClip component', async () => {
        let $vueClip = await createVm();

        $vueClip.addedFile({
            name: 'Fichier.pdf',
            size: 15000,
            upload: {}
        });

        let { file } = $vueClip;

        file.progress = 50;

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    it('download', async () => {
        let $vueClip = await createVm({
            data: () => ({
                value: {
                    name: 'Fichier.pdf',
                    size: 15000
                }
            })
        });

        moxios.install($vueClip.axiosInstance);

        let { dlLink } = $vueClip.$refs;

        dlLink.onclick = jest.fn();
        URL.createObjectURL = jest.fn(()=>'blob:1234');

        $vueClip.$form.downloadLinkBase = '/sharp/api';

        $vueClip.download();

        let { request } = await nextRequestFulfilled({ status: 200, response: '<<file_content>>' });

        expect(URL.createObjectURL).toHaveBeenCalled();
        expect(dlLink.href).toBe('blob:1234');
        expect(dlLink.onclick).toHaveBeenCalled();

        expect(request.config).toMatchObject({
            method: 'post',
            responseType: 'blob',
            data: JSON.stringify({
                fileName: 'Fichier.pdf'
            }),
            url: '/sharp/api/my_upload'
        });

        moxios.uninstall($vueClip.axiosInstance);
    });

    it('emit update on image loaded', async () => {
        let $vueClip = await createVm({
            data: () => ({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg',
                    size: 15000
                }
            })
        });

        let $image = $vueClip.$el.querySelector('img');
        let handleImageUpdated = jest.fn();

        $vueClip.$on('image-updated', handleImageUpdated);

        $image.dispatchEvent(new UIEvent('load'));

        expect(handleImageUpdated).toHaveBeenCalledTimes(1);
    });

    it('init files and reset on value null', async () => {
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        let { $root:vm } = $vueClip;

        expect($vueClip.files).toMatchObject([{
            name: 'Fichier.pdf'
        }]);

        vm.value = null;

        await Vue.nextTick();

        expect($vueClip.files).toEqual([]);
    });

    it('call function on state changed', async () => {
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        $vueClip.onStatusAdded = jest.fn();
        $vueClip.onStatusError = jest.fn();
        $vueClip.onStatusSuccess = jest.fn();

        $vueClip.file.status = 'added';
        await Vue.nextTick();
        expect($vueClip.onStatusAdded).toHaveBeenCalledTimes(1);

        $vueClip.file.status = 'error';
        await Vue.nextTick();
        expect($vueClip.onStatusError).toHaveBeenCalledTimes(1);

        $vueClip.file.status = 'success';
        await Vue.nextTick();
        expect($vueClip.onStatusSuccess).toHaveBeenCalledTimes(1);
    });

    it('file property is the first item of "files"', async () => {
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        expect($vueClip.files[0]).toBe($vueClip.file);
    });

    it('compute images properties correctly', async () => {
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        expect($vueClip.croppedImg).toBeFalsy();

        expect($vueClip.originalImageSrc).toBe($vueClip.file.thumbnail);
        expect($vueClip.imageSrc).toBe($vueClip.file.thumbnail);

       $vueClip.remove();

       $vueClip.addedFile(mockFile({
           name: 'Fic.pdf',
           dataUrl:'data:image/jpeg,<<source image>>'
       }));

        await Vue.nextTick();

        expect($vueClip.originalImageSrc).toBe($vueClip.file.dataUrl);
        expect($vueClip.imageSrc).toBe($vueClip.file.dataUrl);

        $vueClip.croppedImg = 'data:image/jpeg,<<cropped image>>';

        await Vue.nextTick();

        expect($vueClip.imageSrc).toBe($vueClip.croppedImg);
    });

    it('file size', async () => {
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Fichier.pdf',
                    size: 0
                }
            })
        });

        Number.prototype.toLocaleString = jest.fn(Number.prototype.toLocaleString);

        $vueClip.file.size = 1024**2/2;

        expect($vueClip.size).toBe('0.5 MB');
        expect(Number.prototype.toLocaleString).toHaveBeenCalled();
    });

    it('has crop', async () => {
        let $vueClip = await createVm({
            propsData: {
                ratioX:1, ratioY:1
            }
        });

        let { $root:vm } = $vueClip;

        expect($vueClip.hasCrop).toBe(true);

        vm.ratioX = 0;

        await Vue.nextTick();

        expect($vueClip.hasCrop).toBe(false);
    });

    it('filename', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: '/sharp/entities/Image.png'
                }
            })
        });

        expect($vueClip.fileName).toBe('Image.png');
    });
});

async function createVm(customOptions={}) {
    const vm = new Vue({
        el: '#app',
        mixins: [customOptions, MockInjections],

        components: {
            'sharp-vue-clip': {
                'extends': VueClip,
                components: {
                    'vue-cropper': { render() {} },
                }
            }
        },

        props:['readOnly', 'ratioX', 'ratioY'],

        'extends': {
            data:() => ({
                value: null
            }),
            methods: {
                inputEmitted: ()=>{}
            }
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}