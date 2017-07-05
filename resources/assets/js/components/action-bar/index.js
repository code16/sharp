import ActionBarForm from './ActionBarForm';
import ActionBarList from './ActionBarList';

export const NameAssociation = {
    'form' : ActionBarForm.name,
    'list': ActionBarList.name
};

export default {
    [ActionBarForm.name]:ActionBarForm,
    [ActionBarList.name]:ActionBarList
};