

export * from './filters';

export function filesEquals(file1, file2) {
    if(!file1.path || !file2.path) {
        return file1.name === file2.name;
    }
    return `${file1.disk}:${file1.path}` === `${file2.disk}:${file2.path}`;
}
