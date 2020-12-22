import Vue from 'vue';
import { handleNotifications } from "../../util/notifications";

jest.mock('vue', () => ({
    notify: jest.fn(),
}));

describe('notifications', () => {
    test('handleNotifications', () => {
        jest.useFakeTimers();

        handleNotifications(null);
        jest.runAllTimers();

        handleNotifications([]);
        jest.runAllTimers();

        let alert1 = { title:'title', message:'message', level:'danger', autoHide: true },
            alert2 = { title:'alert2' };

        handleNotifications([alert1, alert2]);
        jest.runAllTimers();

        expect(Vue.notify).toHaveBeenCalledWith(expect.objectContaining({ title:'title', text:'message', type:'danger', duration:4000 }));
        expect(Vue.notify).toHaveBeenCalledWith(expect.objectContaining({ duration:-1 }));
    });
});
