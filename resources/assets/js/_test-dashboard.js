export const layout = {
    tabbed:false,
    tabs: [
        {
            title:'tab1',
            columns: [
                {
                    widgets: [
                        [
                            {
                                key: 'SuperPanel'
                            },
                        ],
                        [
                            {
                                key:'PieChart'
                            }
                        ]
                    ]
                },
                {
                    widgets: [
                        [
                            {
                                key:'Panel2'
                            }
                        ],
                        [
                            {
                                key:'LineChart'
                            }
                        ]
                    ]
                }
            ]
        }
    ]
};

export const data = {

};

export const widgets = {
    SuperPanel: {
        type:'panel',
        template:`
            Lorem Elsass ipsum ac Christkindelsmärik munster Wurschtsalad geïz habitant turpis hopla Richard Schirmeck Gal. quam. sit météor geht's id turpis, amet, mänele ornare Strasbourg semper libero.
        `,
        url:'/embedded.html'
    },
    Panel2: {
        type:'panel',
        template:'Panel 2'
    },
    PieChart: {
        type:'chart',
        display:'pie',
        labels: ['VueJs', 'EmberJs', 'ReactJs', 'AngularJs'],
        datasets: [
            {
                backgroundColor: [
                    '#41B883',
                    '#E46651',
                    '#00D8FF',
                    '#DD1B16'
                ],
                data: [40, 20, 80, 10]
            }
        ]
    },
    LineChart: {
        type: 'chart',
        display:'line',
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [
            {
                label: 'Data One',
                backgroundColor: '#f87979',
                data: [40, 39, 10, 40, 39, 80, 40]
            }
        ],
        title: 'Super graphe',
    }
};