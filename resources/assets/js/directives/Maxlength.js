export default function(el,{ value }) {
    if(value) {
        el.setAttribute('maxlength', value);
    }
}