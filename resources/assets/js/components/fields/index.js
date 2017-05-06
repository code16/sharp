import SharpAutocomplete from './Autocomplete';
import SharpTextarea from './Textarea';
import SharpText from './Text';
import SharpPassword from './Password';
import SharpMarkdown from './Markdown';
import SharpNumber from './Number';
import SharpUpload from './upload/Upload';
import SharpTagInput from './TagInput';
import SharpDate from './Date';
import SharpCheck from './Check';


export const NameAssociation = {
    'autocomplete' : SharpAutocomplete.name,
    'text'         : SharpText.name,
    'password'     : SharpPassword.name,
    'textarea'     : SharpTextarea.name,
    'markdown'     : SharpMarkdown.name,
    'number'       : SharpNumber.name,
    'upload'       : SharpUpload.name,
    'taginput'     : SharpTagInput.name,
    'date'         : SharpDate.name,
    'check'        : SharpCheck.name,
};

export default {
    [SharpAutocomplete.name] : SharpAutocomplete,
    [SharpText.name] : SharpText,
    [SharpPassword.name] : SharpPassword,
    [SharpTextarea.name] : SharpTextarea,
    [SharpMarkdown.name] : SharpMarkdown,
    [SharpNumber.name] : SharpNumber,
    [SharpUpload.name] : SharpUpload,
    [SharpTagInput.name] : SharpTagInput,
    [SharpDate.name] : SharpDate,
    [SharpCheck.name] : SharpCheck,
};
