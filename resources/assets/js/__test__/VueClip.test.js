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

function mockXhrResponse(data) {
    return {
        responseText: JSON.stringify(data)
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

    it('can mount "destroyed" VueClip component', async () => {
        let $vueClip = await createVm();

        $vueClip.$destroy();

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

    it('operations finished without any active', async () =>{
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        expect($vueClip.operations).toEqual(['crop']);

        expect($vueClip.operationFinished).toEqual({
            crop: null
        });

        expect($vueClip.activeOperationsCount).toBe(0);
    });


    it('operations finished with one active', async () => {
        let $vueClip = await createVm({
            propsData: {
                ratioX: 1,
                ratioY: 1
            },
            data: ()=>({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        expect($vueClip.operationFinished).toEqual({
            crop: false
        });

        expect($vueClip.activeOperationsCount).toBe(1);
        expect($vueClip.operationFinishedCount).toBe(0);


        $vueClip.croppedImg = 'data:image/jpeg';

        expect($vueClip.operationFinishedCount).toBe(1);
        expect($vueClip.operationFinished).toEqual({
            crop: true
        });
    });

    it('progress with operations', async () => {
        let $vueClip = await createVm({
            propsData: {
                ratioX: 1,
                ratioY: 1
            },
            data: ()=>({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        $vueClip.file.progress = 50;

        expect($vueClip.progress).toBe(47.5);

        $vueClip.file.progress = 100;

        expect($vueClip.progress).toBe(95);

        $vueClip.croppedImg = 'data:image/jpeg';

        expect($vueClip.progress).toBe(100);
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

        $vueClip.file.size = 1024*1024/2;

        expect($vueClip.size).toBe('0.5 MB');
        expect(Number.prototype.toLocaleString).toHaveBeenCalled();

        $vueClip.file.size = 1024;
        expect($vueClip.size).toBe('<0.1 MB');
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

        $vueClip.file.name = 'Photo2.jpg';

        await Vue.nextTick();

        expect($vueClip.fileName).toBe('Photo2.jpg');
    });

    it('set pending', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        let setPendingListener = jest.fn();

        $vueClip.actionsBus.$on('setPendingJob', setPendingListener);

        $vueClip.onStatusAdded();
        expect(setPendingListener).toHaveBeenCalledTimes(1);
        expect(setPendingListener).toHaveBeenCalledWith({
            key: 'my_upload.0',
            origin: 'upload',
            value: true
        });

        $vueClip.file.xhrResponse = mockXhrResponse({});

        $vueClip.onStatusSuccess();
        expect(setPendingListener).toHaveBeenCalledTimes(2);
        expect(setPendingListener).toHaveBeenLastCalledWith({
            key: 'my_upload.0',
            origin: 'upload',
            value: false
        });

        $vueClip.onStatusError();
        expect(setPendingListener).toHaveBeenCalledTimes(3);
        expect(setPendingListener).toHaveBeenLastCalledWith({
            key: 'my_upload.0',
            origin: 'upload',
            value: false
        });
    });

    it('on status added', async () => {
        let $vueClip = await createVm();

        let handleReset = jest.fn();

        $vueClip.$on('reset', handleReset);
        $vueClip.onStatusAdded();

        expect(handleReset).toHaveBeenCalled();
    });

    it('on status error', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        let handleError = jest.fn();
        $vueClip.remove = jest.fn();

        $vueClip.file.errorMessage = "Can't upload";

        $vueClip.$on('error', handleError);
        $vueClip.onStatusError();

        expect(handleError).toHaveBeenCalledTimes(1);
        expect(handleError).toHaveBeenCalledWith("Can't upload");
        expect($vueClip.remove).toHaveBeenCalled();
    });

    it('on status success', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        $vueClip.file.xhrResponse = mockXhrResponse({
            fileName: 'storage/Fichier.pdf'
        });

        let handleSuccess = jest.fn(), handleInput = jest.fn();

        $vueClip.$on('success', handleSuccess);
        $vueClip.$on('input', handleInput);

        $vueClip.onStatusSuccess();

        expect(handleSuccess).toHaveBeenCalledTimes(1);
        expect(handleSuccess).toHaveBeenCalledWith({
            fileName: 'storage/Fichier.pdf',
            uploaded: true
        });

        expect(handleInput).toHaveBeenCalledTimes(1);
        expect(handleInput).toHaveBeenCalledWith({
            fileName: 'storage/Fichier.pdf',
            uploaded: true
        });
    });

    it('remove', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        let handleInput=jest.fn(), handleReset=jest.fn(), handleRemoved=jest.fn();
        $vueClip.$on('input', handleInput);
        $vueClip.$on('reset', handleReset);
        $vueClip.$on('removed', handleRemoved);

        $vueClip.remove();

        expect(handleInput).toHaveBeenCalledTimes(1);
        expect(handleInput).toHaveBeenCalledWith(null);

        expect(handleReset).toHaveBeenCalledTimes(1);
        expect(handleRemoved).toHaveBeenCalledTimes(1);

        expect($vueClip.files).toEqual([]);
        expect($vueClip.canDownload).toBe(false);
    });

    function mockCropper() {
        return {
            cropper: {
                ready: false,
            },
            getCroppedCanvas:jest.fn(()=>({
                toDataURL:jest.fn(()=>'data:image/jpeg')
            })),
            getData:jest.fn(()=>({
                width:100, height:200, x:20, y:40, rotate: 0
            })),
            getImageData:jest.fn(()=>({
                naturalWidth: 200, naturalHeight: 800
            }))
        }
    }

    it('crop', async () => {
        let $vueClip = await createVm({
            propsData: {
                ratioX: 1,
                ratioY: 1
            },
            data:()=> ({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        expect($vueClip.$refs.cropper).toBeTruthy();

        $vueClip.$refs.cropper = mockCropper();
        let { cropper } = $vueClip.$refs.cropper;

        expect($vueClip.isCropperReady()).toBe(false);
        cropper.ready = true;
        expect($vueClip.isCropperReady()).toBe(true);


        $vueClip.file.xhrResponse = mockXhrResponse({});
        $vueClip.onCropperReady = jest.fn($vueClip.onCropperReady);

        $vueClip.onStatusSuccess();

        let handleInput = jest.fn(), handleUpdated = jest.fn();
        $vueClip.$on('input', handleInput);
        $vueClip.$on('updated', handleUpdated);

        await Vue.nextTick();

        expect($vueClip.croppable).toBe(true);
        expect($vueClip.onCropperReady).toHaveBeenCalled();

        expect($vueClip.croppedImg).toBe('data:image/jpeg');

        let testEmit = handler => {
            expect(handler).toHaveBeenCalledTimes(1);
            expect(handler.mock.calls[0][0]).toEqual({
                name: 'Image.jpg',
                thumbnail: '/image.jpg',
                cropData: {
                    width: .5,
                    height: .25,
                    x: .1,
                    y: .05,
                    rotate: -0
                }
            })
        };

        testEmit(handleInput);
        testEmit(handleUpdated);

        $vueClip.$refs.cropper.getData = jest.fn(()=>({
            width: 100, height: 200, x:20, y:40, rotate: -90
        }));

        $vueClip.updateCropData();

        testEmit = handler => {
            expect(handler).toHaveBeenCalledTimes(2);
            expect(handler.mock.calls[1][0]).toMatchObject({
                cropData: {
                    width: 0.125,
                    height: 1,
                    x: 0.025,
                    y: 0.2,
                    rotate: 90
                }
            });
        };

        testEmit(handleInput);
        testEmit(handleUpdated);

        $vueClip.remove();

        expect($vueClip.croppedImg).toBe(null);
    });

    it('cropper modal', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        let { modal } = $vueClip.$refs;

        $vueClip.updateCroppedImage = jest.fn();
        $vueClip.updateCropData = jest.fn();

        modal.$emit('ok');

        expect($vueClip.updateCroppedImage).toHaveBeenCalledTimes(1);
        expect($vueClip.updateCropData).toHaveBeenCalledTimes(1);

        let handleInactive = jest.fn(), handleActive = jest.fn();

        $vueClip.$on('inactive', handleInactive);
        $vueClip.$on('active', handleActive);

        $vueClip.onEditButtonClick();

        expect(handleActive).toHaveBeenCalledTimes(1);
        expect($vueClip.showEditModal).toBe(true);


        modal.$emit('hidden');

        expect(handleInactive).toHaveBeenCalledTimes(1);
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
            })
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}