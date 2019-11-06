import SharpWidgetPanel from './Panel';
import SharpWidgetOrderedList from './OrderedList';
import SharpWidgetChart from './chart/Chart';

export function widgetByType(type) {
    if(type === 'graph') {
        return SharpWidgetChart;
    } else if(type === 'panel') {
        return SharpWidgetPanel;
    } else if(type === 'list') {
        return SharpWidgetOrderedList;
    }
}

export {
    SharpWidgetChart,
    SharpWidgetPanel,
    SharpWidgetOrderedList,
};