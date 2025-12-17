<script setup lang="ts">
    import { computed, useTemplateRef } from "vue";
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisSingleContainer, VisDonut, VisTooltip, VisBulletLegend } from "@unovis/vue";
    import { BulletLegendConfigInterface, CrosshairConfigInterface, DonutConfigInterface, Donut, Tooltip } from "@unovis/ts";
    import {
        ChartConfig,
        ChartContainer, ChartLegendContent,
        ChartTooltip,
        ChartTooltipContent,
        componentToString
    } from "@/components/ui/chart";
    import { UseElementSize } from "@vueuse/components";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const data = computed(() => props.value?.datasets?.map((dataset, i) => ({
        name: dataset.label ?? '',
        color: dataset.color,
        value: dataset.data[0] ?? 0,
        [i]: dataset.data[0] ?? 0,
    })));
    type Datum = typeof data.value[number];
    const chartConfig = computed((): ChartConfig =>
        Object.fromEntries(props.value?.datasets.map((dataset, i) => [i, ({ label: dataset.label, color: dataset.color })]))
    );
    const tooltipTemplate = componentToString(chartConfig, ChartTooltipContent, { hideLabel: true });
    const log = (v, ...args) => {
        console.log(v, ...args);
        return v;
    }
</script>

<template>
    <ChartContainer class="min-h-[250px] sm:min-h-0 flex flex-col gap-y-2 gap-x-4 sm:gap-4" :config="chartConfig" ref="container">
        <UseElementSize class="flex-1 min-h-0 flex flex-col" v-slot="{ width, height }">
            <VisSingleContainer class="flex-1 min-h-0">
                <VisDonut
                    v-bind="{
                    data: data,
                    value: d => d.value,
                    color: d => d.color,
                    // arcWidth: 47,
                    arcWidth: log(Math.round(Math.min(width, height) * .15), width, height),
                    attributes: {
                         // [Donut.selectors.segment]: {
                         //     'class'() {
                         //         return `${Donut.selectors.segment} relative fill-(--color) hover:stroke-10! stroke-(--color)! transition-all [transform-box:fill-box] origin-center`;
                         //     },
                         //     'style'(data: CallbackData) {
                         //         return `--color:${data.data.color}`;
                         //         // return { '--color': data.color }
                         //     }
                         // }
                    }
                } as DonutConfigInterface<Datum>"
                />
                <ChartTooltip :triggers="{ [Donut.selectors.segment]: tooltipTemplate }" />
            </VisSingleContainer>
        </UseElementSize>


        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <ChartLegendContent />
        </template>
    </ChartContainer>
</template>

<!--<script setup lang="ts">-->
<!--    import type {-->
<!--        ChartConfig,-->
<!--    } from "@/components/ui/chart"-->

<!--    import { Donut } from "@unovis/ts"-->
<!--    import { VisDonut, VisSingleContainer } from "@unovis/vue"-->
<!--    import { TrendingUp } from "lucide-vue-next"-->
<!--    import {-->
<!--        Card,-->
<!--        CardContent,-->
<!--        CardDescription,-->
<!--        CardFooter,-->
<!--        CardHeader,-->
<!--        CardTitle,-->
<!--    } from "@/components/ui/card"-->
<!--    import {-->
<!--        ChartContainer,-->
<!--        ChartTooltip,-->
<!--        ChartTooltipContent,-->
<!--        componentToString,-->
<!--    } from "@/components/ui/chart"-->

<!--    const description = "A simple pie chart"-->

<!--    const chartData = [-->
<!--        { browser: "chrome", visitors: 275, fill: "var(&#45;&#45;color-chrome)" },-->
<!--        { browser: "safari", visitors: 200, fill: "var(&#45;&#45;color-safari)" },-->
<!--        { browser: "firefox", visitors: 187, fill: "var(&#45;&#45;color-firefox)" },-->
<!--        { browser: "edge", visitors: 173, fill: "var(&#45;&#45;color-edge)" },-->
<!--        { browser: "other", visitors: 90, fill: "var(&#45;&#45;color-other)" },-->
<!--    ]-->
<!--    type Data = typeof chartData[number]-->

<!--    const chartConfig = {-->
<!--        visitors: {-->
<!--            label: "Visitors",-->
<!--            color: undefined,-->
<!--        },-->
<!--        chrome: {-->
<!--            label: "Chrome",-->
<!--            color: "var(&#45;&#45;chart-1)",-->
<!--        },-->
<!--        safari: {-->
<!--            label: "Safari",-->
<!--            color: "var(&#45;&#45;chart-2)",-->
<!--        },-->
<!--        firefox: {-->
<!--            label: "Firefox",-->
<!--            color: "var(&#45;&#45;chart-3)",-->
<!--        },-->
<!--        edge: {-->
<!--            label: "Edge",-->
<!--            color: "var(&#45;&#45;chart-4)",-->
<!--        },-->
<!--        other: {-->
<!--            label: "Other",-->
<!--            color: "var(&#45;&#45;chart-5)",-->
<!--        },-->
<!--    } satisfies ChartConfig-->
<!--</script>-->

<!--<template>-->
<!--    <Card class="flex flex-col">-->
<!--        <CardHeader class="items-center pb-0">-->
<!--            <CardTitle>Pie Chart</CardTitle>-->
<!--            <CardDescription>January - June 2024</CardDescription>-->
<!--        </CardHeader>-->
<!--        <CardContent class="flex-1 pb-0">-->
<!--            <ChartContainer-->
<!--                :config="chartConfig"-->
<!--                class="mx-auto aspect-square max-h-[250px]"-->
<!--            >-->
<!--                <VisSingleContainer-->
<!--                    :data="chartData"-->
<!--                    :margin="{ top: 30, bottom: 30 }"-->
<!--                >-->
<!--                    <VisDonut-->
<!--                        :value="(d: Data) => d.visitors"-->
<!--                        :color="(d: Data) => chartConfig[d.browser as keyof typeof chartConfig].color"-->
<!--                        :arc-width="30"-->
<!--                    />-->
<!--                    <ChartTooltip-->
<!--                        :triggers="{-->
<!--              [Donut.selectors.segment]: componentToString(chartConfig, ChartTooltipContent, { hideLabel: true })!,-->
<!--            }"-->
<!--                    />-->
<!--                </VisSingleContainer>-->
<!--            </ChartContainer>-->
<!--        </CardContent>-->
<!--        <CardFooter class="flex-col gap-2 text-sm">-->
<!--            <div class="flex items-center gap-2 font-medium leading-none">-->
<!--                Trending up by 5.2% this month <TrendingUp class="h-4 w-4" />-->
<!--            </div>-->
<!--            <div class="leading-none text-muted-foreground">-->
<!--                Showing total visitors for the last 6 months-->
<!--            </div>-->
<!--        </CardFooter>-->
<!--    </Card>-->
<!--</template>-->
