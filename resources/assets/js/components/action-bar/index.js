import SharpActionBarForm from './ActionBarForm';
import SharpActionBarDashboard from './ActionBarDashboard';


export function actionBarByContext(context) {
    if(context === 'form') {
        return SharpActionBarForm;
    } else if(context === 'dashboard') {
        return SharpActionBarDashboard;
    }
}

export {
    SharpActionBarForm,
    SharpActionBarDashboard,
};