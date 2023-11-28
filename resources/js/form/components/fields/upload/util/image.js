
export function getImageBlobUrl(file) {
    if(!file.type.match(/^image\//)) {
        return Promise.resolve(null);
    }
    const img = new Image();
    img.src = URL.createObjectURL(file);

    return new Promise((resolve, reject) => {
        img.onload = () => resolve(img.src);
        img.onerror = (e) => {
            URL.revokeObjectURL(img.src);
            reject(`Image format not handled by browser for "${file.name}"`);
        };
    });
}
