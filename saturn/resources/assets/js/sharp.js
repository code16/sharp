import Sharp from 'sharp-plugin';

Vue.use(Sharp, {
    customFields: {
        map: {
            template: '<div>custom map field</div>'
        }
    }
});