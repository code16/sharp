import{d as g,r as l,ak as L,az as V,aA as w,at as S,c as C,w as o,aB as k,o as q,a as u,j as d,V as E,u as n,Q as $,b as B,t as I,aM as c}from"./sharp-NGr8F9Yr.js";import{E as f,u as x,_ as F}from"./useEntityListHighlightedItem-D4HE1h2r.js";import{_ as K}from"./Title.vue_vue_type_script_setup_true_lang-tYLV-bre.js";import{s as m}from"./DropdownChevronDown.vue_vue_type_script_setup_true_lang-8PAKNJqz.js";import{_ as N}from"./PageBreadcrumb.vue_vue_type_script_setup_true_lang-BA6cFe6u.js";import"./CardDescription.vue_vue_type_script_setup_true_lang-CeHCPQ3T.js";const Q={class:"@container"},U=g({__name:"EntityList",props:{entityList:{},breadcrumb:{}},setup(p){const a=p,s=l().params.entityKey,e=L(new f(a.entityList,s)),r=V(e.value.config.filters,e.value.filterValues),y=w("entityList",{refresh:(t,{formModal:i})=>{e.value=e.value.withRefreshedItems(t.items),i.shouldReopen&&i.reopen()}}),{highlightedEntityKey:R,highlightedInstanceId:h}=x();S(()=>a.entityList,()=>{e.value=new f(a.entityList,s),r.update(a.entityList.config.filters,a.entityList.filterValues)});function _(t){m(e.value.query)!==m(t)&&c.visit(l("code16.sharp.list",{entityKey:s})+m(t),{preserveState:!0,preserveScroll:!1})}function b(t,i){c.post(l("code16.sharp.list.filters.store",{entityKey:s}),{filterValues:r.nextValues(t,i),query:e.value.query},{preserveState:!0,preserveScroll:!1})}function v(){c.post(l("code16.sharp.list.filters.store",{entityKey:s}),{filterValues:r.defaultValues(r.rootFilters),query:{...e.value.query,search:null}},{preserveState:!0,preserveScroll:!1})}return(t,i)=>(q(),C(k,null,{breadcrumb:o(()=>[u(N,{breadcrumb:t.breadcrumb},null,8,["breadcrumb"])]),default:o(()=>[u(K,{breadcrumb:t.breadcrumb},null,8,["breadcrumb"]),d("div",Q,[d("div",{class:E(e.value.pageAlert?"pt-4":"pt-6 @3xl:pt-10")},[u(F,{"entity-key":n(s),"entity-list":e.value,filters:n(r),commands:n(y),title:t.breadcrumb.items[0].label,"highlighted-instance-id":n(h),onReset:v,onFilterChange:b,"onUpdate:query":_},{"card-header":o(()=>[u(n($),{class:"line-clamp-2 min-w-0"},{default:o(()=>[B(I(e.value.title),1)]),_:1})]),_:1},8,["entity-key","entity-list","filters","commands","title","highlighted-instance-id"])],2)])]),_:1}))}});export{U as default};
