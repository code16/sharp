import{d as v,r as i,ai as h,am as L,an as V,c as S,w as l,ao as g,o as w,a as n,u,O as q,b as C,t as $,au as o}from"./sharp-BDJdrkET.js";import{E as f,_ as k}from"./EntityList-1M6Xkfoy.js";import{_ as F}from"./Title.vue_vue_type_script_setup_true_lang-aFOxYZbg.js";import{u as E,s as c}from"./CommandDropdownItems.vue_vue_type_script_setup_true_lang-D_uJmeGk.js";import{_ as B}from"./PageBreadcrumb.vue_vue_type_script_setup_true_lang-LvUwjMN8.js";import"./CardDescription.vue_vue_type_script_setup_true_lang-5qhlXF-y.js";import"./TemplateRenderer.vue_vue_type_script_setup_true_lang-Bi-nQzO0.js";const O=v({__name:"EntityList",props:{entityList:{},breadcrumb:{}},setup(d){const r=d,s=i().params.entityKey,t=h(new f(r.entityList,s)),a=L(t.value.config.filters,t.value.filterValues),p=E({refresh:e=>{t.value=t.value.withRefreshedItems(e.items)}});V(()=>r.entityList,()=>{t.value=new f(r.entityList,s),a.update(r.entityList.config.filters,r.entityList.filterValues)});function y(e){c(t.value.query)!==c(e)&&o.visit(i("code16.sharp.list",{entityKey:s})+c(e),{preserveState:!0,preserveScroll:!1})}function b(e,m){o.post(i("code16.sharp.list.filters.store",{entityKey:s}),{filterValues:a.nextValues(e,m),query:t.value.query},{preserveState:!0,preserveScroll:!1})}function _(){o.post(i("code16.sharp.list.filters.store",{entityKey:s}),{filterValues:a.defaultValues(a.rootFilters),query:{...t.value.query,search:null}},{preserveState:!0,preserveScroll:!1})}return(e,m)=>(w(),S(g,null,{breadcrumb:l(()=>[n(B,{breadcrumb:e.breadcrumb},null,8,["breadcrumb"])]),default:l(()=>[n(F,{breadcrumb:e.breadcrumb},null,8,["breadcrumb"]),n(k,{"entity-key":u(s),"entity-list":t.value,filters:u(a),commands:u(p),title:e.breadcrumb.items[0].label,onReset:_,onFilterChange:b,"onUpdate:query":y},{"card-header":l(()=>[n(u(q),null,{default:l(()=>[C($(e.breadcrumb.items[0].label),1)]),_:1})]),_:1},8,["entity-key","entity-list","filters","commands","title"])]),_:1}))}});export{O as default};
