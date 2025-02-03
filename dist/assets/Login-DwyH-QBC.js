import{d as C,C as x,c as p,w as s,o as n,a,b as r,t as l,u as e,_ as i,e as v,g as V,i as u,s as _,f as I,j as f,k as N,l as g,m as y,J as c,A as $,r as E,K as U,n as j,L}from"./sharp-ClLWbqcq.js";import{_ as S,a as h,b as q}from"./FormItem.vue_vue_type_script_setup_true_lang-BZZBD8qD.js";import{_ as A}from"./Title.vue_vue_type_script_setup_true_lang-CavU8pqV.js";import"./CardDescription.vue_vue_type_script_setup_true_lang-B8DPS8LM.js";const D={class:"grid gap-4"},J={class:"flex items-center"},K=["href"],M={class:"flex items-center space-x-2"},O={key:2,class:"mt-4"},H=C({__name:"Login",props:{loginIsEmail:{type:Boolean},message:{},prefill:{}},setup(B){var w,k;const b=B,t=x({login:(w=b.prefill)==null?void 0:w.login,password:(k=b.prefill)==null?void 0:k.password,remember:!1});return(d,o)=>(n(),p(S,null,{default:s(()=>[a(A,null,{default:s(()=>[r(l(e(i)("sharp::pages/auth/login.title")),1)]),_:1}),e(t).hasErrors?(n(),p(e(v),{key:0,class:"mb-4",variant:"destructive"},{default:s(()=>[a(e(V),{class:"mb-0"},{default:s(()=>[r(l(Object.values(e(t).errors)[0]),1)]),_:1})]),_:1})):u("",!0),e(_)("status")?(n(),p(e(v),{key:1,class:"mb-4",variant:e(_)("status_level")==="error"?"destructive":"default"},{default:s(()=>[e(_)("status_level")!=="error"?(n(),p(e(I),{key:0,class:"w-4 h-4"})):u("",!0),a(e(V),{class:"mb-0"},{default:s(()=>[r(l(e(_)("status")),1)]),_:1})]),_:1},8,["variant"])):u("",!0),f("form",{onSubmit:o[3]||(o[3]=j(m=>e(t).post(e(E)("code16.sharp.login.post")),["prevent"]))},[a(q,null,{title:s(()=>[r(l(e(i)("sharp::pages/auth/login.title")),1)]),footer:s(()=>[a(e(N),{type:"submit",class:"w-full"},{default:s(()=>[r(l(e(i)("sharp::pages/auth/login.button")),1)]),_:1})]),default:s(()=>[f("div",D,[a(e(h),null,{default:s(()=>[a(e(g),{for:"login"},{default:s(()=>[r(l(d.loginIsEmail?e(i)("sharp::pages/auth/login.login_field_for_email"):e(i)("sharp::pages/auth/login.login_field")),1)]),_:1}),a(e(y),{id:"login",type:d.loginIsEmail?"email":"text",autocomplete:d.loginIsEmail?"email":"username",modelValue:e(t).login,"onUpdate:modelValue":o[0]||(o[0]=m=>e(t).login=m),autofocus:""},null,8,["type","autocomplete","modelValue"])]),_:1}),a(e(h),null,{default:s(()=>[f("div",J,[a(e(g),{for:"password"},{default:s(()=>[r(l(e(i)("sharp::pages/auth/login.password_field")),1)]),_:1}),e(c)("sharp.auth.forgotten_password.enabled")&&e(c)("sharp.auth.forgotten_password.show_reset_link_in_login_form")?(n(),$("a",{key:0,href:e(E)("code16.sharp.password.request"),class:"ml-auto inline-block text-sm underline"},l(e(i)("sharp::pages/auth/login.forgot_password_link")),9,K)):u("",!0)]),a(e(y),{id:"password",type:"password",modelValue:e(t).password,"onUpdate:modelValue":o[1]||(o[1]=m=>e(t).password=m),autocomplete:"current-password"},null,8,["modelValue"])]),_:1}),e(c)("sharp.auth.suggest_remember_me")?(n(),p(e(h),{key:0},{default:s(()=>[f("div",M,[a(e(U),{id:"remember_me",modelValue:e(t).remember,"onUpdate:modelValue":o[2]||(o[2]=m=>e(t).remember=m)},null,8,["modelValue"]),a(e(g),{for:"remember_me"},{default:s(()=>[r(l(e(i)("sharp::pages/auth/login.remember")),1)]),_:1})])]),_:1})):u("",!0)])]),_:1})],32),d.message?(n(),$("div",O,[a(L,{template:d.message},null,8,["template"])])):u("",!0)]),_:1}))}});export{H as default};
