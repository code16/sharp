import { UppyFile } from "@uppy/core";
import Compressor from 'compressorjs';

export function createThumbnail(file: UppyFile<any, any>, { width, height }: { width: number, height: number }) {
    return new Promise<string>((resolve, reject) => {
        new Compressor(file.data as File, {
            checkOrientation: true,
            maxWidth: width,
            maxHeight: height,
            quality: 0.8,
            convertTypes: [],
            success(blob) {
                resolve(URL.createObjectURL(blob));
            },
            error(error) {
                reject(error);
            }
        });
    });
}
