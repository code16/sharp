import Autocomplete from './Autocomplete.vue';
import Textarea from './Textarea.vue';
import Text from './Text.vue';
import Markdown from './markdown/Markdown.vue';
import NumberInput from './Number.vue';
import Upload from './upload/Upload.vue';
import TagInput from './Tags.vue';
import DateInput from './date/Date.vue';
import Check from './Check.vue';
import List from './list/List.vue';
import Select from './Select.vue';
import Html from './Html.vue';
import Geolocation from './geolocation/Geolocation';
import Wysiwyg from './wysiwyg/Wysiwyg';
import DateRange from './date-range/DateRange';

export default {
    'autocomplete' : Autocomplete,
    'text'         : Text,
    'textarea'     : Textarea,
    'markdown'     : Markdown,
    'number'       : NumberInput,
    'upload'       : Upload,
    'tags'         : TagInput,
    'date'         : DateInput,
    'check'        : Check,
    'list'         : List,
    'select'       : Select,
    'html'         : Html,
    'geolocation'  : Geolocation,
    'wysiwyg'      : Wysiwyg,
    'daterange'    : DateRange,
};

export {
    Autocomplete,
    Text,
    Textarea,
    Markdown,
    NumberInput,
    Upload,
    TagInput,
    DateInput,
    Check,
    List,
    Select,
    Html,
    Geolocation,
    Wysiwyg,
    DateRange,
}
