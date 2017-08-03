import * as util from '../util';

export default {
    inject:['actionsBus'],
    created() {
        let actions = this.$options.actions;

        if(!actions) return;
        if(typeof actions._disabled === 'function' && actions._disabled.call(this)) {
            util.log(`${this.$options.name} : actions are disabled`);
            return;
        }
        if(!this.actionsBus) {
            util.error('Use of action options without actionsBus injected');
            return;
        }

        for(let actionName of Object.keys(this.$options.actions)) {
            let action = actions[actionName];
            if(typeof action === 'string') {
                if(typeof this[action] !== 'function') {
                    util.error(`${this.$options.name} (ActionEvents) : this.${action} is not a function`);
                    continue;
                }

                this.actionsBus.$on(actionName, (...args) => this[action].apply(this,args));
            }
            else if(typeof action === 'function') {
                this.actionsBus.$on(actionName, (...args) => action.apply(this,args));
            }
            else util.error(`${this.$options.name} (ActionEvents) : unprocessable action type (only function on string)`);
        }
    }
}