import moxios from 'moxios';

export function nextRequestFulfilled(config) {
    return new Promise(resolve => {
        moxios.wait(async () => {
            let request = moxios.requests.mostRecent();
            let response = await request.respondWith(config);
            resolve(response); // moxios Response object
        })
    });
}
