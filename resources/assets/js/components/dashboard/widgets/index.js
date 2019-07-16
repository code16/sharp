import SharpWidgetPanel from './Panel';
import SharpWidgetListGroup from './ListGroup';
import SharpWidgetChart from './chart/Chart';

export function widgetByType(type) {
    if(type === 'graph') {
        return SharpWidgetChart;
    } else if(type === 'panel') {
        return SharpWidgetPanel;
    } else if(type === 'list') {
        return SharpWidgetListGroup;
    }
}

export {
    SharpWidgetChart,
    SharpWidgetPanel,
    SharpWidgetListGroup
};