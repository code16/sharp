import SharpActionBarForm from './ActionBarForm';



export function actionBarByContext(context) {
    if(context === 'form') {
        return SharpActionBarForm;
    }
}

export {
    SharpActionBarForm
};