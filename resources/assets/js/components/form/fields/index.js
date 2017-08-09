import SharpAutocomplete from './Autocomplete';
import SharpTextarea from './Textarea';
import SharpText from './Text';
import SharpMarkdown from './markdown/Markdown';
import SharpNumber from './Number';
import SharpUpload from './upload/Upload';
import SharpTagInput from './Tags';
import SharpDate from './date/Date';
import SharpCheck from './Check';
import SharpList from './list/List';
import SharpSelect from './Select';
import SharpHtml from './Html';

//debugger

export const NameAssociation = {
    'autocomplete' : SharpAutocomplete.name,
    'text'         : SharpText.name,
    'textarea'     : SharpTextarea.name,
    'markdown'     : SharpMarkdown.name,
    'number'       : SharpNumber.name,
    'upload'       : SharpUpload.name,
    'tags'         : SharpTagInput.name,
    'date'         : SharpDate.name,
    'check'        : SharpCheck.name,
    'list'         : SharpList.name,
    'select'       : SharpSelect.name,
    'html'         : SharpHtml.name
};

export default {
    [SharpAutocomplete.name] : SharpAutocomplete,
    [SharpText.name] : SharpText,
    [SharpTextarea.name] : SharpTextarea,
    [SharpMarkdown.name] : SharpMarkdown,
    [SharpNumber.name] : SharpNumber,
    [SharpUpload.name] : SharpUpload,
    [SharpTagInput.name] : SharpTagInput,
    [SharpDate.name] : SharpDate,
    [SharpCheck.name] : SharpCheck,
    [SharpList.name] : SharpList,
    [SharpSelect.name] : SharpSelect,
    [SharpHtml.name] : SharpHtml
};
