import SharpActionBarForm from './ActionBarForm';
import SharpActionBarList from './ActionBarList';


export function actionBarByContext(context) {
    if(context === 'form') {
        return SharpActionBarForm;
    } else if(context === 'list') {
        return SharpActionBarList;
    }
}

export {
    SharpActionBarForm,
    SharpActionBarList,
};