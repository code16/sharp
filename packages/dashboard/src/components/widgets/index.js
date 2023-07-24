import SharpWidgetPanel from './Panel.vue';
import SharpWidgetOrderedList from './OrderedList.vue';
import SharpWidgetFigure from './Figure.vue';
import SharpWidgetChart from './chart/Chart.vue';

export function widgetByType(type) {
    if(type === 'graph') {
        return SharpWidgetChart;
    } else if(type === 'panel') {
        return SharpWidgetPanel;
    } else if(type === 'list') {
        return SharpWidgetOrderedList;
    } else if(type === 'figure') {
        return SharpWidgetFigure;
    }
}
