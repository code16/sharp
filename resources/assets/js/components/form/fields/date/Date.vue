    <template>
    <div class="SharpDate">
        <input class="SharpDate__input"
               :value="inputValue"
               :disabled="readOnly"
               @input="handleInput"
               @focus="forceShowPicker"
               @blur="handleBlur"
               @keydown.up.prevent="increase"
               @keydown.down.prevent="decrease"
               ref="input">
        <div class="SharpDate__picker" v-show="showPicker">
            <sharp-date-picker v-if="hasDate"
                               class="SharpDate__picker-inner SharpDate__date"
                               language="fr"
                               inline monday-first
                               :value="dateObject"
                               @selected="handleDateSelect">
            </sharp-date-picker>
            <sharp-time-picker v-if="hasTime"
                               class=" SharpDate__time"
                               :value="timeObject"
                               :active="showPicker"
                               :format="displayFormat"
                               :minute-interval="stepTime"
                               :min="minTime" :max="maxTime"
                               @change="handleTimeSelect">
            </sharp-time-picker>
        </div>
    </div>
</template>

<script>
    import SharpDatePicker from './Datepicker';
    import SharpTimePicker from './Timepicker';

    import { Focusable } from '../../../../mixins';

    import moment from 'moment';

    export default {
        name:'SharpDate',
        components: {
            SharpDatePicker,
            SharpTimePicker
        },

        inject:['$field'],

        mixins: [Focusable],

        props: {
            value: {
                type:[Object, String]
            },
            hasDate: {
                type:Boolean,
                default:true
            },
            hasTime: {
                type:Boolean,
                default:false
            },
            displayFormat: {
                type: String,
                default:'DD/MM/YYYY HH:mm'
            },
            stepTime: {
                type:Number,
                default:30
            },
            minTime: String,
            maxTime: String,

            readOnly: Boolean
        },
        data() {
            return {
                showPicker:false,
                force:false
            }
        },
        computed: {
            moment() {
                return moment(this.value||Date.now());
            },
            dateObject() {
                return this.moment.toDate();
            },
            timeObject() {
                return {
                    HH: this.moment.format('HH'),
                    mm: this.moment.format('mm'),
                    ss: this.moment.format('ss')
                }
            },
            inputValue() {
                return this.moment.format(this.displayFormat);
            },
        },
        methods: {
            handleDateSelect(date) {
                this.moment.set({
                    year:date.getFullYear(),
                    month:date.getMonth(),
                    date:date.getDate()
                });
                this.$emit('input', this.moment);
            },
            handleTimeSelect({ data }) {
                this.moment.set({
                    hour:data.HH,
                    minute:data.mm,
                    second:data.ss,
                });
                this.$emit('input', this.moment);
            },
            handleInput(e) {
                let m = moment(e.target.value, this.displayFormat, true);
                if(!m.isValid()) {
                    this.$field.$emit('error', "Format de l'heure invalide");
                }
                else {
                    this.$field.$emit('ok');
                    this.$emit('input', m);
                }
            },
            handleBlur() {
                this.$field.$emit('clear');
            },

            increase(e) {
                this.translate(e.target, 1)
            },
            decrease(e) {
                this.translate(e.target, -1)
            },
            translate(input, amount) {
                let selection = this.changeOnArrowPressed(input.selectionStart, amount);

                if(selection)  {
                    this.$nextTick(_=>input.setSelectionRange(selection.start,selection.end));
                }
            },
            add(amount, key) {
                this.moment.add.apply(this.moment,arguments);
                this.$emit('input',this.moment);
            },
            nearestMinutesDist(dir) { //dir = 1 or -1
                let curM = this.moment.minutes(); //current minutes
                if(curM%this.stepTime === 0) {
                    return dir*this.stepTime;
                }
                let fn = dir<0 ? 'floor' : 'ceil';
                return this.stepTime * Math[fn](curM/this.stepTime) - curM;
            },
            updateMoment(ch, amount) {
                //console.log('add',ch,amount);
                switch(ch) {
                    case 'H': this.add(amount,'hours'); break;
                    case 'm': this.add(this.nearestMinutesDist(amount),'minutes'); break;
                    case 's': this.add(amount,'seconds'); break;
                    case 'Y': this.add(amount,'years'); break;
                    case 'M': this.add(amount,'months'); break;
                    case 'D': this.add(amount,'days'); break;
                    default: return false;
                }
                return true;
            },
            changeOnArrowPressed(pos,amount) {
                let lookupPos=  pos;
                if(!this.updateMoment(this.displayFormat[lookupPos],amount) && pos) {
                    lookupPos--;
                    if(!this.updateMoment(this.displayFormat[lookupPos],amount))
                        return null;
                }
                let ch=this.displayFormat[lookupPos];
                return {
                    start:this.displayFormat.indexOf(ch),
                    end:this.displayFormat.lastIndexOf(ch)+1
                };
            },
            forceShowPicker() {
                this.showPicker = true;
                //this.force = true;
            }
        },
        mounted() {
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target) && this.showPicker && !this.force) {
                    this.showPicker = false;
                }
                this.force = false;
            }, false);


            this.setFocusable(this.$refs.input);
        }
    }
</script>