import SharpAutocomplete from './Autocomplete.vue';
import SharpTextarea from './Textarea.vue';
import SharpText from './Text.vue';
import SharpMarkdown from './markdown/Markdown.vue';
import SharpNumber from './Number.vue';
import SharpUpload from './upload/Upload.vue';
import SharpTagInput from './Tags.vue';
import SharpDate from './date/Date.vue';
import SharpCheck from './Check.vue';
import SharpList from './list/List.vue';
import SharpSelect from './Select.vue';
import SharpHtml from './Html.vue';
import SharpGeolocation from './geolocation/Geolocation';
import SharpTrix from './wysiwyg/TrixEditor.vue';

export default {
    'autocomplete' : SharpAutocomplete,
    'text'         : SharpText,
    'textarea'     : SharpTextarea,
    'markdown'     : SharpMarkdown,
    'number'       : SharpNumber,
    'upload'       : SharpUpload,
    'tags'         : SharpTagInput,
    'date'         : SharpDate,
    'check'        : SharpCheck,
    'list'         : SharpList,
    'select'       : SharpSelect,
    'html'         : SharpHtml,
    'geolocation'  : SharpGeolocation,
    'wysiwyg'      : SharpTrix
};