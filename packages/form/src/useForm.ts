import { inject } from "vue";
import { Form } from "./Form";


export function useForm() {
    return inject('form') as Form;
}
