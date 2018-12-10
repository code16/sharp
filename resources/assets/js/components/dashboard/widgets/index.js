import SharpWidgetPanel from './Panel';
import SharpWidgetChart from './chart/Chart';

export function widgetByType(type) {
    if(type === 'graph') {
        return SharpWidgetChart;
    } else if(type === 'panel') {
        return SharpWidgetPanel;
    }
}

export {
    SharpWidgetChart,
    SharpWidgetPanel
};