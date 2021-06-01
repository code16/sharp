import fr from 'apexcharts/dist/locales/fr.json';
import ru from 'apexcharts/dist/locales/ru.json';
import es from 'apexcharts/dist/locales/es.json';
import de from 'apexcharts/dist/locales/de.json';
import en from 'apexcharts/dist/locales/en.json';


export function defaultChartOptions() {
    return {
        chart: {
            animations: {
                enabled: false,
            },
            toolbar: {
                show: false,
                tools: {
                    pan: false,
                    zoom: true,
                    download: false,
                },
            },
            locales: [
                en, fr, ru, es, de,
            ],
            defaultLocale: document.documentElement.lang,
        },
        legend: {
            showForSingleSeries: true,
        },
        tooltip: {
            y: {
                title: {
                    formatter: (seriesName, { seriesIndex }) =>
                        seriesName !== `series-${seriesIndex + 1}` ? `${seriesName}:` : ''
                }
            }
        },
    }
}

export function hasLegends(options) {
    return !!(options.legend?.show ?? true)
}

export function normalizeColors(colors) {
    const ctx = document.createElement('canvas').getContext('2d');
    return colors.map(color => {
        ctx.fillStyle = color;
        return ctx.fillStyle
    });
}
