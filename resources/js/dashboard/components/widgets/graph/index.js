import { transformLineData } from "./line";
import { transformBarData } from "./bar";
import { transformPieData } from "./pie";


export function transformData(type, value) {
    if(type === 'line') {
        return transformLineData(value);
    } else if(type === 'bar') {
        return transformBarData(value);
    } else if(type === 'pie') {
        return transformPieData(value)
    }
}
