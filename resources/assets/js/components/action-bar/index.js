import SharpActionBarForm from './ActionBarForm';
import SharpActionBarList from './ActionBarList';
import SharpActionBarDashboard from './ActionBarDashboard';


export function actionBarByContext(context) {
    if(context === 'form') {
        return SharpActionBarForm;
    } else if(context === 'list') {
        return SharpActionBarList;
    } else if(context === 'dashboard') {
        return SharpActionBarDashboard;
    }
}

export {
    SharpActionBarForm,
    SharpActionBarList,
    SharpActionBarDashboard,
};