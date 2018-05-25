import Sharp from 'sharp-plugin';
import TextIcon from './components/TextIcon';

Vue.use(Sharp, {
    customFields: {
        'texticon': TextIcon
    }
});