
// export default {
//     install(Vue, { customFields }) {
//         Object.entries(customFields).forEach(([name, field])=>{
//             Vue.component(`SharpCustomField_${name}`, field);
//         });
//     }
// };

export function defineSharpPlugin({ customFields }) {
    window.sharpPlugin = {
        customFields,
    }
}
