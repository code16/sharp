import { inject } from "vue";
import { Form } from "./Form";


export function useParentForm() {
    return inject('form') as Form;
}
