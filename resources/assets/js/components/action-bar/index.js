import ActionBarForm from './ActionBarForm';
import ActionBarList from './ActionBarList';
//import ActionBarDashboard from './ActionBarDashboard';

export const NameAssociation = {
    'form' : ActionBarForm.name,
    'list': ActionBarList.name,
    //'dashboard': ActionBarDashboard.name
};

export default {
    [ActionBarForm.name]:ActionBarForm,
    [ActionBarList.name]:ActionBarList,
    //[ActionBarDashboard.name]:ActionBarDashboard
};