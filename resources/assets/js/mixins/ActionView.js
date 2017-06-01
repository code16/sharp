import ActionView from '../components/ActionView';

export default {
    inject: ['isSharp'],

    computed: {
        actionViewOrDefault() {
            return this.isSharp ? ActionView.name : 'div';
        }
    }
}