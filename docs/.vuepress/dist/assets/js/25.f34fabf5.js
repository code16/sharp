(window.webpackJsonp=window.webpackJsonp||[]).push([[25],{224:function(t,s,a){"use strict";a.r(s);var n=a(2),e=Object(n.a)({},function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("ContentSlotsDistributor",{attrs:{"slot-key":t.$parent.slotKey}},[a("h1",{attrs:{id:"create-a-dashboard"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#create-a-dashboard","aria-hidden":"true"}},[t._v("#")]),t._v(" Create a Dashboard")]),t._v(" "),a("p",[t._v("A Dashboard is a good way to present synthetic data to the user, with graphs, stats, or personalized reminders for instance.")]),t._v(" "),a("h2",{attrs:{id:"generator"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#generator","aria-hidden":"true"}},[t._v("#")]),t._v(" Generator")]),t._v(" "),a("div",{staticClass:"language-bash extra-class"},[a("pre",{pre:!0,attrs:{class:"language-bash"}},[a("code",[t._v("php artisan sharp:make:dashboard "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("<")]),t._v("class_name"),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v("\n")])])]),a("h2",{attrs:{id:"write-the-class"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#write-the-class","aria-hidden":"true"}},[t._v("#")]),t._v(" Write the class")]),t._v(" "),a("p",[t._v("A Dashboard is very much like an Entity Form, except it readonly. So the first step is to create a new class extending "),a("code",{staticClass:"inline"},[t._v("Code16\\"),a("span",{staticClass:"token package"},[t._v("Sharp"),a("span",{staticClass:"token punctuation"},[t._v("\\")]),t._v("Dashboard"),a("span",{staticClass:"token punctuation"},[t._v("\\")]),t._v("SharpDashboard")])]),t._v(" which lead us to implement three functions:")]),t._v(" "),a("ul",[a("li",[a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildWidgets")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(", similar to Entity Form's "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildForm")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),a("li",[a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildWidgetsLayout")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(", similar to "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildLayout")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),a("li",[a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildDashboardConfig")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(", for optional filters")]),t._v(" "),a("li",[t._v("and "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildWidgetsData")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("DashboardQueryParams "),a("span",{staticClass:"token variable"},[t._v("$params")]),a("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(", for the actual Dashboard data, like Entity Form's "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("find")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(" method.")])]),t._v(" "),a("h3",{attrs:{id:"buildwidgets"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#buildwidgets","aria-hidden":"true"}},[t._v("#")]),t._v(" "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildWidgets")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),a("p",[t._v("We're suppose to use here "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token variable"},[t._v("$this")]),a("span",{staticClass:"token operator"},[t._v("-")]),a("span",{staticClass:"token operator"},[t._v(">")]),a("span",{staticClass:"token function"},[t._v("addWidget")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(" to configure all the Dashboard widgets.")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("buildWidgets")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("addWidget")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),t._v("\n        SharpLineGraphWidget"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("make")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"capacities"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("setTitle")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"Spaceships by capacity"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("addWidget")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),t._v("\n        SharpPanelWidget"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("make")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"activeSpaceships"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("setInlineTemplate")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"<h1>{{count}}</h1> spaceships in activity"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("setLink")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token single-quoted-string string"}},[t._v("'spaceship'")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])]),a("p",[t._v("As we can see in this example, we defined two widgets giving them a mandatory "),a("code",{staticClass:"inline"},[t._v("key")]),t._v(" and some optional properties depending of their type.")]),t._v(" "),a("p",[t._v("Every widget has the optional following setters:")]),t._v(" "),a("ul",[a("li",[a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("setTitle")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),a("span",{staticClass:"token variable"},[t._v("$title")]),a("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(" for the widget title displayed above it")]),t._v(" "),a("li",[a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("setLink")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),a("span",{staticClass:"token variable"},[t._v("$entityKey")]),a("span",{staticClass:"token punctuation"},[t._v(",")]),t._v(" string "),a("span",{staticClass:"token variable"},[t._v("$instanceId")]),t._v(" "),a("span",{staticClass:"token operator"},[t._v("=")]),t._v(" "),a("span",{staticClass:"token constant"},[t._v("null")]),a("span",{staticClass:"token punctuation"},[t._v(",")]),t._v(" "),a("span",{staticClass:"token keyword"},[t._v("array")]),t._v(" "),a("span",{staticClass:"token variable"},[t._v("$querystring")]),t._v(" "),a("span",{staticClass:"token operator"},[t._v("=")]),t._v(" "),a("span",{staticClass:"token punctuation"},[t._v("[")]),a("span",{staticClass:"token punctuation"},[t._v("]")]),a("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(" to make the whole widget linked to a specific entity. To link to the Entity List, pass the "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token variable"},[t._v("$entityKey")])]),t._v(", and add the "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token variable"},[t._v("$instanceId")])]),t._v(" to link to the Entity Form.")])]),t._v(" "),a("p",[t._v("And here's the full list and documentation of each widget available, for the specifics:")]),t._v(" "),a("ul",[a("li",[a("router-link",{attrs:{to:"/guide/dashboard-widgets/graph.html"}},[t._v("Graph")])],1),t._v(" "),a("li",[a("router-link",{attrs:{to:"/guide/dashboard-widgets/panel.html"}},[t._v("Panel")])],1)]),t._v(" "),a("h3",{attrs:{id:"buildwidgetslayout"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#buildwidgetslayout","aria-hidden":"true"}},[t._v("#")]),t._v(" "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildWidgetsLayout")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),a("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),a("p",[t._v("The layout API is a bit different of Entity Form here, because we think in terms of rows and not columns. So for instance:")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("buildWidgetsLayout")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("addFullWidthWidget")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"capacities"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("addRow")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),t._v("DashboardLayoutRow "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$row")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$row")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("addWidget")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token number"}},[t._v("6")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"activeSpaceships"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n                "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("addWidget")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token number"}},[t._v("6")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"inactiveSpaceships"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])]),a("p",[t._v('We can only add rows and "full width widgets" (which are a shortcut for a single widget row). A row groups widgets in a 12-based grid.')]),t._v(" "),a("h3",{attrs:{id:"buildwidgetsdata-dashboardqueryparams-params"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#buildwidgetsdata-dashboardqueryparams-params","aria-hidden":"true"}},[t._v("#")]),t._v(" "),a("code",{staticClass:"inline"},[a("span",{staticClass:"token function"},[t._v("buildWidgetsData")]),a("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("DashboardQueryParams "),a("span",{staticClass:"token variable"},[t._v("$params")]),a("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),a("p",[t._v("Widget data is set with specific methods depending of their type. The documentation is therefore split:")]),t._v(" "),a("ul",[a("li",[a("router-link",{attrs:{to:"/guide/dashboard-widgets/graph.html"}},[t._v("Graph")])],1),t._v(" "),a("li",[a("router-link",{attrs:{to:"/guide/dashboard-widgets/panel.html"}},[t._v("Panel")])],1)]),t._v(" "),a("h2",{attrs:{id:"configure-the-dashboard"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#configure-the-dashboard","aria-hidden":"true"}},[t._v("#")]),t._v(" Configure the Dashboard")]),t._v(" "),a("p",[t._v("Once this class written, we have to declare the form in the sharp config file:")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// config/sharp.php")]),t._v("\n\n"),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"entities"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"dashboards"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"company_dashboard"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"view"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" \\"),a("span",{pre:!0,attrs:{class:"token package"}},[t._v("App"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Sharp"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("CompanyDashboard")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"menu"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"label"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"Company"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"entities"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n                "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n                    "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"label"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"Dashboard"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n                    "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"icon"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"fa-dashboard"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n                    "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"dashboard"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"company_dashboard"')]),t._v("\n                "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n                "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])]),a("p",[t._v("In the menu, like an Entity, a Dashboard can be displayed anywhere.")]),t._v(" "),a("h2",{attrs:{id:"dashboard-filters"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#dashboard-filters","aria-hidden":"true"}},[t._v("#")]),t._v(" Dashboard filters")]),t._v(" "),a("p",[t._v("Just like EntityLists, Dashboard can display filters, as "),a("router-link",{attrs:{to:"/guide/filters.html"}},[t._v("documented on the Filter page")]),t._v(".")],1),t._v(" "),a("h2",{attrs:{id:"dashboard-commands"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#dashboard-commands","aria-hidden":"true"}},[t._v("#")]),t._v(" Dashboard commands")]),t._v(" "),a("p",[t._v("Like again EntityLists, Commands can be attached to a Dashboard: "),a("router-link",{attrs:{to:"/guide/commands.html"}},[t._v("see the Command documentation")]),t._v(".")],1),t._v(" "),a("h2",{attrs:{id:"dashboard-policies"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#dashboard-policies","aria-hidden":"true"}},[t._v("#")]),t._v(" Dashboard policies")]),t._v(" "),a("p",[t._v("Just like for an Entity, you can define a Policy for a Dashboard. The only available action is "),a("code",{staticClass:"inline"},[t._v("view")]),t._v(".")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// config/sharp.php")]),t._v("\n\n"),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"entities"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"dashboards"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"company_dashboard"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"view"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" \\"),a("span",{pre:!0,attrs:{class:"token package"}},[t._v("App"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Sharp"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("CompanyDashboard")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n            "),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"policy"')]),t._v(" "),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" \\"),a("span",{pre:!0,attrs:{class:"token package"}},[t._v("App"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Sharp"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Policies"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("CompanyDashboardPolicy")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(".")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])]),a("p",[t._v("And the policy class can be pretty straightforward:")]),t._v(" "),a("div",{staticClass:"language-php extra-class"},[a("pre",{pre:!0,attrs:{class:"language-php"}},[a("code",[a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token class-name"}},[t._v("CompanyDashboardPolicy")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("view")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),t._v("User "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),a("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),a("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),a("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),a("span",{pre:!0,attrs:{class:"token function"}},[t._v("hasGroup")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),a("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"boss"')]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n"),a("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])])])},[],!1,null,null,null);s.default=e.exports}}]);