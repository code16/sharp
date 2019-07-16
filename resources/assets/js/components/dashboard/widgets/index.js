import SharpWidgetPanel from './Panel';
import SharpWidgetList from './List';
import SharpWidgetChart from './chart/Chart';

export function widgetByType(type) {
    if(type === 'graph') {
        return SharpWidgetChart;
    } else if(type === 'panel') {
        return SharpWidgetPanel;
    } else if(type === 'list') {
        return SharpWidgetList;
    }
}

export {
    SharpWidgetChart,
    SharpWidgetPanel,
    SharpWidgetList
};