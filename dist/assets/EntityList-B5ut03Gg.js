import{d as v,r as l,ai as h,ax as L,ay as V,ar as S,c as g,w as n,az as w,o as q,a as o,u,O as C,b as $,t as k,aK as c}from"./sharp-DDNPuC1w.js";import{E as f,_ as E}from"./EntityList-CrgsiVon.js";import{_ as F}from"./Title.vue_vue_type_script_setup_true_lang-MxfiLW2l.js";import{s as m}from"./DropdownChevronDown.vue_vue_type_script_setup_true_lang-DZQFkxFU.js";import{_ as K}from"./PageBreadcrumb.vue_vue_type_script_setup_true_lang-D6ipke--.js";import"./CardDescription.vue_vue_type_script_setup_true_lang-BVFKuiG8.js";import"./TemplateRenderer.vue_vue_type_script_setup_true_lang-td6RXtbk.js";const G=v({__name:"EntityList",props:{entityList:{},breadcrumb:{}},setup(p){const r=p,s=l().params.entityKey,t=h(new f(r.entityList,s)),a=L(t.value.config.filters,t.value.filterValues),d=V("entityList",{refresh:(e,{formModal:i})=>{t.value=t.value.withRefreshedItems(e.items),i.shouldReopen&&i.reopen()}});S(()=>r.entityList,()=>{t.value=new f(r.entityList,s),a.update(r.entityList.config.filters,r.entityList.filterValues)});function y(e){m(t.value.query)!==m(e)&&c.visit(l("code16.sharp.list",{entityKey:s})+m(e),{preserveState:!0,preserveScroll:!1})}function b(e,i){c.post(l("code16.sharp.list.filters.store",{entityKey:s}),{filterValues:a.nextValues(e,i),query:t.value.query},{preserveState:!0,preserveScroll:!1})}function _(){c.post(l("code16.sharp.list.filters.store",{entityKey:s}),{filterValues:a.defaultValues(a.rootFilters),query:{...t.value.query,search:null}},{preserveState:!0,preserveScroll:!1})}return(e,i)=>(q(),g(w,null,{breadcrumb:n(()=>[o(K,{breadcrumb:e.breadcrumb},null,8,["breadcrumb"])]),default:n(()=>[o(F,{breadcrumb:e.breadcrumb},null,8,["breadcrumb"]),o(E,{"entity-key":u(s),"entity-list":t.value,filters:u(a),commands:u(d),title:e.breadcrumb.items[0].label,onReset:_,onFilterChange:b,"onUpdate:query":y},{"card-header":n(()=>[o(u(C),null,{default:n(()=>[$(k(e.breadcrumb.items[0].label),1)]),_:1})]),_:1},8,["entity-key","entity-list","filters","commands","title"])]),_:1}))}});export{G as default};