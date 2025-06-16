import Uppy, { MinimalRequiredUppyFile, UppyFile } from "@uppy/core";
import ThumbnailGenerator from "@uppy/thumbnail-generator";


export function createThumbnail(file: UppyFile<any, any>, { width, height }: { width: number, height: number }) {
    return new Promise<string>((resolve, reject) => {
        new Uppy()
            .use(ThumbnailGenerator, { thumbnailWidth: width, thumbnailHeight: height, thumbnailType: 'image/png' })
            .on('thumbnail:generated', (thumbnailFile, preview) => resolve(preview))
            .on('thumbnail:error', (error) => reject(error))
            .addFile({
                ...(file as MinimalRequiredUppyFile<{}, {}>),
                preview: null
            });
    });
}
