// import Vue from 'vue';


export function showNotification({ level, title, message, autoHide }) {
    // todo handle notifications
    // Vue.notify({
    //     title,
    //     type: level,
    //     text: message,
    //     duration: autoHide ? 4000 : -1
    // });
}

export function handleNotifications(notifications) {
    setTimeout(() => {
        notifications?.forEach?.(notification => showNotification(notification));
    }, 500);
}
