import * as util from '../util';

function getActionListener(vm, action) {
    if(typeof action === 'string') {
        if(typeof vm[action] !== 'function') {
            util.error(`${vm.$options.name} (ActionEvents) : this.${action} is not a function`);
            return;
        }
        return (...args) => vm[action].apply(vm, args);
    }
    else if(typeof action === 'function') {
        return (...args) => action.apply(vm,args);
    }
    else util.error(`${vm.$options.name} (ActionEvents) : unprocessable action type (only function on string)`);
}

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
            const action = actions[actionName];
            const listener = getActionListener(this, action);

            if(listener) {
                this.actionsBus.$on(actionName, listener);
                this.$once('hook:beforeDestroy', ()=>{
                    this.actionsBus.$off(actionName, listener);
                });
            }
        }
    }
}