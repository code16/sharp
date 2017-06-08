import SharpWidgetPanel from './Panel';
import SharpWidgetChart from './chart/Chart';

export const NameAssociation = {
    'chart': SharpWidgetChart.name,
    'panel': SharpWidgetPanel.name
};

export default {
    [SharpWidgetChart.name]: SharpWidgetChart,
    [SharpWidgetPanel.name]: SharpWidgetPanel
}