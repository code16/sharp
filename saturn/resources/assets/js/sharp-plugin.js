import Sharp from 'sharp-plugin';
import TextIcon from './components/TextIcon';
import Title from './components/Title';


Vue.use(Sharp, {
    customFields: {
        'textIcon': TextIcon,
        'title': Title,
    }
});