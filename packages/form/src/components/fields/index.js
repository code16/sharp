import Autocomplete from './Autocomplete.vue';
import Textarea from './Textarea.vue';
import Text from './Text.vue';
import NumberInput from './Number.vue';
import Upload from './upload/Upload.vue';
import TagInput from './Tags.vue';
import DateInput from './date/Date.vue';
import Check from './Check.vue';
import List from './list/List.vue';
import Select from './Select.vue';
import Html from './Html.vue';
import Geolocation from './geolocation/Geolocation.vue';
import Editor from './editor/EditorField.vue';
import DateRange from './date-range/DateRange.vue';

export default {
    'autocomplete' : Autocomplete,
    'text'         : Text,
    'textarea'     : Textarea,
    'editor'       : Editor,
    'number'       : NumberInput,
    'upload'       : Upload,
    'tags'         : TagInput,
    'date'         : DateInput,
    'check'        : Check,
    'list'         : List,
    'select'       : Select,
    'html'         : Html,
    'geolocation'  : Geolocation,
    'daterange'    : DateRange,
};

export {
    Autocomplete,
    Text,
    Textarea,
    Editor,
    NumberInput,
    Upload,
    TagInput,
    DateInput,
    Check,
    List,
    Select,
    Html,
    Geolocation,
    DateRange,
}
