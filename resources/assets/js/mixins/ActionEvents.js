import util from '../util';

export default {
    created() {
        let actions = this.$options.actions;

        if(!actions) return;
        if(!this.actionsBus) {
            util.error('Use of action options without actionsBus injected');
            return;
        }

        for(let actionName of Object.keys(this.$options.actions)) {
            let action = actions[actionName];
            if(typeof action === 'string') {
                if(typeof this[action] !== 'function') {
                    util.error(`${this.$options.name} (ActionEvents) : this.${action} is not a function`)
                    continue;
                }

                this.actionsBus.$on(actionName, () => this[action].apply(this,arguments));
            }
            else if(typeof action === 'function') {
                this.actionsBus.$on(actionName, () => action.apply(this,arguments));
            }
            else util.error(`${this.$options.name} (ActionEvents) : unprocessable action type (only function on string)`);
        }
    }
}