import { Line, transformLineData } from "./line";
import { Bar, transformBarData } from "./bar";
import { Pie, transformPieData } from "./pie";

export function getChartByType(type) {
    if(type === 'line') {
        return Line;
    } else if(type === 'bar') {
        return Bar;
    } else if(type === 'pie') {
        return Pie;
    }
}

export function transformData(type, value) {
    if(type === 'line') {
        return transformLineData(value);
    } else if(type === 'bar') {
        return transformBarData(value);
    } else if(type === 'pie') {
        return transformPieData(value)
    }
}