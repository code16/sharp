import SharpWidgetPanel from './Panel';
import SharpWidgetChart from './chart/Chart';
import SharpWidgetPie from './chart/Pie';

export function widgetByType(type, display) {
    if(type === 'graph') {
        if(display === 'pie'){
            return SharpWidgetPie;
        }
        return SharpWidgetChart;
    } else if(type === 'panel') {
        return SharpWidgetPanel;
    }
}

export {
    SharpWidgetChart,
    SharpWidgetPanel
};