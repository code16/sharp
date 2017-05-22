import Autocomplete from './Autocomplete';
import Textarea from './Textarea';
import TextInput from './TextInput';
import Password from './Password';
import Markdown from './Markdown';


export const NameAssociation = {
    'Autocomplete' : Autocomplete.name,
    'Text'         : TextInput.name,
    'Password'     : Password.name,
    'Textarea'     : Textarea.name,
    'Markdown'     : Markdown.name
}

export default {
    [Autocomplete.name]:Autocomplete,
    [TextInput.name]:TextInput,
    [Password.name]:Password,
    [Textarea.name]:Textarea,
    [Markdown.name]:Markdown
};
