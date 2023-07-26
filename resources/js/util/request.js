
export function parseBlobJSONContent(blob) {
    return new Promise(resolve => {
        let reader = new FileReader();
        reader.addEventListener("loadend", function() {
            resolve(JSON.parse(reader.result));
        });
        reader.readAsText(blob);
    });
}

export function getFileName(headers={}) {
    let { ['content-disposition']: disposition } = headers;
    if (disposition && disposition.includes('attachment')) {
        let filenameRE = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
        let matches = filenameRE.exec(disposition);
        if (matches != null && matches[1]) {
            return matches[1].replace(/['"]/g, '');
        }
    }
    return null;
}