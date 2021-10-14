import Vue from 'vue';
import VueClip from '../../src/components/fields/upload/VueClip';

import { MockInjections, MockI18n } from '@sharp/test-utils';


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

    let toLocaleString = Number.prototype.toLocaleString;
    Number.prototype.toLocaleString = function(locale, options) {
        return toLocaleString.call(this,'en-EN', options)
    };

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-vue-clip :value="value"
                    :options="{ url:'/' }"
                    field-config-identifier="my_upload" 
                    unique-identifier="my_upload.0"
                    :ratio-x="ratioX" 
                    :ratio-y="ratioY"
                    :read-only="readOnly"
                    :transformable-file-types="transformableFileTypes"
                />
            </div>
        `;
        MockI18n.mockLangFunction();
    });


    test('can mount VueClip component', async () => {
        await createVm();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount read-only VueClip component', async () => {
        await createVm({
            propsData: {
                readOnly: true
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount initially non image VueClip component', async () => {
        await createVm({
            data: () => ({
                value: {
                    name: 'Fichier.pdf',
                    path: 'storage/data/Fichier.pdf',
                    disk: 'local',
                    size: 15000
                }
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount initially image VueClip component', async () => {
        await createVm({
            propsData: {
                ratioX: 1, ratioY: 2
            },
            data: () => ({
                value: {
                    name: 'Image.jpg',
                    path: 'storage/data/Image.jpg',
                    disk: 'local',
                    thumbnail: '/image.jpg',
                    size: 15000
                }
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount non transformable image', async () => {
        await createVm({
            propsData: {
                ratioX: 1, ratioY: 2, transformableFileTypes: ['.png']
            },
            data: () => ({
                value: {
                    name: 'Image.jpg',
                    path: 'storage/data/Image.jpg',
                    disk: 'local',
                    thumbnail: '/image.jpg',
                    size: 15000
                }
            })
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "in progress" VueClip component', async () => {
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

    test('can mount "destroyed" VueClip component', async () => {
        let $vueClip = await createVm();

        $vueClip.$destroy();

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('init files and reset on value null', async () => {
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

    test('call function on state changed', async () => {
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

    test('file property is the first item of "files"', async () => {
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        expect($vueClip.files[0]).toBe($vueClip.file);
    });

    test('compute images properties correctly', async () => {
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        expect($vueClip.transformedImg).toBeFalsy();

        expect($vueClip.originalImageSrc).toBe($vueClip.file.thumbnail);
        expect($vueClip.imageSrc).toBe($vueClip.file.thumbnail);

       $vueClip.remove();

       $vueClip.addedFile(mockFile({
           name: 'Fic.pdf',
           blobUrl: 'blob:<<source image>>'
       }));

        await Vue.nextTick();

        expect($vueClip.originalImageSrc).toBe($vueClip.file.blobUrl);
        expect($vueClip.imageSrc).toBe($vueClip.file.blobUrl);

        $vueClip.transformedImg = 'blob:<<cropped image>>';

        await Vue.nextTick();

        expect($vueClip.imageSrc).toBe($vueClip.transformedImg);
    });

    test('operations finished without any active', async () =>{
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        expect($vueClip.operations).toEqual(['transform']);

        expect($vueClip.operationFinished).toEqual({
            transform: null
        });

        expect($vueClip.activeOperationsCount).toBe(0);
    });


    test('operations finished with one active', async () => {
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
            }),
        });

        // reset status to simulate new file
        $vueClip.file.status = null;

        expect($vueClip.operationFinished).toEqual({
            transform: false
        });

        expect($vueClip.activeOperationsCount).toBe(1);
        expect($vueClip.operationFinishedCount).toBe(0);


        $vueClip.transformedImg = 'data:image/jpeg';

        expect($vueClip.operationFinishedCount).toBe(1);
        expect($vueClip.operationFinished).toEqual({
            transform: true
        });
    });

    test('progress with operations', async () => {
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

        // reset status to simulate new file
        $vueClip.file.status = null;

        $vueClip.file.progress = 50;

        expect($vueClip.progress).toBe(47.5);

        $vueClip.file.progress = 100;

        expect($vueClip.progress).toBe(95);

        $vueClip.transformedImg = 'data:image/jpeg';

        expect($vueClip.progress).toBe(100);
    });

    test('file size', async () => {
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

        expect($vueClip.size).toBe('512 KB');
        expect(Number.prototype.toLocaleString).toHaveBeenCalled();

        $vueClip.file.size = 768;
        expect($vueClip.size).toBe('0.75 KB');
    });

    test('has transform', async () => {
        let $vueClip = await createVm({
            propsData: {
                ratioX:1, ratioY:1
            },
            data: ()=>({
                value: {
                    name: 'Photo2.jpg',
                    thumbnail: 'Photo2.jpg',
                }
            })
        });

        let { $root:vm } = $vueClip;

        expect($vueClip.hasInitialTransform).toBe(false);
        expect($vueClip.isTransformable).toBe(true);

        vm.ratioX = 0;

        await Vue.nextTick();

        expect($vueClip.hasInitialTransform).toBe(false);

        vm.transformableFileTypes = ['.jpg'];

        await Vue.nextTick();

        expect($vueClip.hasInitialTransform).toBe(false);
        expect($vueClip.isTransformable).toBe(true);

        vm.ratioX = 1;
        $vueClip.file.status = null;
        await Vue.nextTick();

        expect($vueClip.hasInitialTransform).toBe(true);

        $vueClip.file.name = 'visual.png';

        expect($vueClip.isTransformable).toBe(false);
        expect($vueClip.hasInitialTransform).toBe(false);
    });

    test('file extension', async () => {
        let $vueClip = await createVm({
            data: ()=>({
                value: {
                    name: 'Photo2.jpg'
                }
            })
        });
        expect($vueClip.fileExtension).toEqual('.jpg');
    });

    test('filename', async () => {
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

    test('set pending', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: 'Fichier.pdf',
                    type: '',
                }
            })
        });


        $vueClip.onStatusAdded();
        expect($vueClip.$form.setUploading).toHaveBeenCalledTimes(1);
        expect($vueClip.$form.setUploading).toHaveBeenCalledWith('my_upload.0', true);

        $vueClip.file.xhrResponse = mockXhrResponse({});

        $vueClip.onStatusSuccess();
        expect($vueClip.$form.setUploading).toHaveBeenCalledTimes(2);
        expect($vueClip.$form.setUploading).toHaveBeenLastCalledWith('my_upload.0', false);

        $vueClip.onStatusError();
        expect($vueClip.$form.setUploading).toHaveBeenCalledTimes(3);
        expect($vueClip.$form.setUploading).toHaveBeenLastCalledWith('my_upload.0', false);
    });

    test('on status added', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: 'Fichier.pdf',
                    type: 'application/pdf',
                }
            })
        });

        let handleReset = jest.fn();

        $vueClip.$on('reset', handleReset);
        $vueClip.onStatusAdded();

        expect(handleReset).toHaveBeenCalled();
    });

    test('on status error', async () => {
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

        await $vueClip.$nextTick();

        expect(handleError).toHaveBeenCalledTimes(1);
        expect(handleError).toHaveBeenCalledWith("Can't upload", expect.anything());
    });

    test('on status success', async () => {
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

    test('remove', async () => {
        let $vueClip = await createVm({
            data:()=> ({
                value: {
                    name: 'Fichier.pdf'
                }
            })
        });

        let handleInput=jest.fn(), handleReset=jest.fn();
        $vueClip.$on('input', handleInput);
        $vueClip.$on('reset', handleReset);

        $vueClip.remove();

        expect(handleInput).toHaveBeenCalledTimes(1);
        expect(handleInput).toHaveBeenCalledWith(null);

        expect(handleReset).toHaveBeenCalledTimes(1);

        expect($vueClip.files).toEqual([]);
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

    xtest('crop', async () => {
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

        expect($vueClip.allowCrop).toBe(true);
        expect($vueClip.isTransformable).toBe(true);
        expect($vueClip.onCropperReady).toHaveBeenCalled();

        expect($vueClip.transformedImg).toBe('data:image/jpeg');

        let testEmit = handler => {
            expect(handler).toHaveBeenCalledTimes(1);
            expect(handler.mock.calls[0][0]).toEqual({
                name: 'Image.jpg',
                thumbnail: '/image.jpg',
                transformed: true,
                filters: {
                    crop: {
                        width: .5,
                        height: .25,
                        x: .1,
                        y: .05,
                    },
                    rotate: {
                        angle: -0,
                    },
                }
            })
        };

        testEmit(handleInput);
        testEmit(handleUpdated);

        $vueClip.$refs.cropper.getData = jest.fn(()=>({
            width: 100, height: 200, x:20, y:40, rotate: -90
        }));

        $vueClip.updateCropData($vueClip.$refs.cropper);

        testEmit = handler => {
            expect(handler).toHaveBeenCalledTimes(2);
            expect(handler.mock.calls[1][0]).toMatchObject({
                transformed: true,
                filters: {
                    crop: {
                        width: 0.125,
                        height: 1,
                        x: 0.025,
                        y: 0.2,
                    },
                    rotate: {
                        angle: 90,
                    },
                }
            });
        };

        testEmit(handleInput);
        testEmit(handleUpdated);

        $vueClip.remove();

        expect($vueClip.transformedImg).toBe(null);
    });

    test('cropper modal', async () => {
        let $vueClip = await createVm({
            propsData: {
                ratioX: 1, ratioY:1
            },
            data:()=> ({
                value: {
                    name: 'Image.jpg',
                    thumbnail: '/image.jpg'
                }
            })
        });

        const modal = $vueClip.$refs.modal;

        $vueClip.updateTransformedImage = jest.fn();
        $vueClip.updateFilters = jest.fn();

        modal.$emit('submit');

        expect($vueClip.updateTransformedImage).toHaveBeenCalledTimes(1);
        expect($vueClip.updateFilters).toHaveBeenCalledTimes(1);

        $vueClip.handleEditButtonClick();

        expect($vueClip.showEditModal).toBe(true);
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
                    'vue-cropper': { render() {} }
                }
            }
        },

        props:['readOnly', 'ratioX', 'ratioY', 'transformableFileTypes'],

        'extends': {
            data:() => ({
                value: null
            })
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}
