const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/sharp-BDJdrkET.js","assets/sharp-D3EbSYob.css","assets/leaflet-src-CSm5Mvdr.js"])))=>i.map(i=>d[i]);
import{d as h,ai as p,ck as S,aZ as j,cl as m,cm as T,bQ as g,cn as V,b$ as mt,aC as w,y as oe,co as Y,an as Fe,b2 as ce,cp as ft}from"./sharp-BDJdrkET.js";const pe=(t,o)=>{for(const e of Object.keys(o))t.on(e,o[e])},ye=t=>{for(const o of Object.keys(t)){const e=t[o];e&&k(e.cancel)&&e.cancel()}},We=t=>!t||typeof t.charAt!="function"?t:t.charAt(0).toUpperCase()+t.slice(1),k=t=>typeof t=="function",L=(t,o,e)=>{for(const r in e){const n="set"+We(r);t[n]?Fe(()=>e[r],(l,s)=>{t[n](l,s)}):o[n]&&Fe(()=>e[r],l=>{o[n](l)})}},_=(t,o,e={})=>{const r={...e};for(const n in t){const l=o[n],s=t[n];l&&(l&&l.custom===!0||s!==void 0&&(r[n]=s))}return r},A=t=>{const o={},e={};for(const r in t)if(r.startsWith("on")&&!r.startsWith("onUpdate")&&r!=="onReady"){const n=r.slice(2).toLocaleLowerCase();o[n]=t[r]}else e[r]=t[r];return{listeners:o,attrs:e}},Je=async t=>{const o=await Promise.all([m(()=>import("./sharp-BDJdrkET.js").then(e=>e.cs),__vite__mapDeps([0,1])),m(()=>import("./sharp-BDJdrkET.js").then(e=>e.cr),__vite__mapDeps([0,1])),m(()=>import("./sharp-BDJdrkET.js").then(e=>e.ct),__vite__mapDeps([0,1]))]);delete t.Default.prototype._getIconUrl,t.Default.mergeOptions({iconRetinaUrl:o[0].default,iconUrl:o[1].default,shadowUrl:o[2].default})},K=t=>{const o=p((...r)=>console.warn(`Method ${t} has been invoked without being replaced`)),e=(...r)=>o.value(...r);return e.wrapped=o,w(t,e),e},Q=(t,o)=>t.wrapped.value=o,b=typeof self=="object"&&self.self===self&&self||typeof global=="object"&&global.global===global&&global||globalThis,v=t=>{const o=S(t);if(o===void 0)throw new Error(`Attempt to inject ${t.description} before it was provided.`);return o},Jt=Object.freeze(Object.defineProperty({__proto__:null,WINDOW_OR_GLOBAL:b,assertInject:v,bindEventHandlers:pe,cancelDebounces:ye,capitalizeFirstLetter:We,isFunction:k,propsBinder:L,propsToLeafletOptions:_,provideLeafletWrapper:K,remapEvents:A,resetWebpackIcon:Je,updateLeafletWrapper:Q},Symbol.toStringTag,{value:"Module"})),O=Symbol("useGlobalLeaflet"),I=Symbol("addLayer"),ee=Symbol("removeLayer"),F=Symbol("registerControl"),ve=Symbol("registerLayerControl"),me=Symbol("canSetParentHtml"),fe=Symbol("setParentHtml"),be=Symbol("setIcon"),_e=Symbol("bindPopup"),ge=Symbol("bindTooltip"),Le=Symbol("unbindPopup"),Oe=Symbol("unbindTooltip"),qt=Object.freeze(Object.defineProperty({__proto__:null,AddLayerInjection:I,BindPopupInjection:_e,BindTooltipInjection:ge,CanSetParentHtmlInjection:me,RegisterControlInjection:F,RegisterLayerControlInjection:ve,RemoveLayerInjection:ee,SetIconInjection:be,SetParentHtmlInjection:fe,UnbindPopupInjection:Le,UnbindTooltipInjection:Oe,UseGlobalLeafletInjection:O},Symbol.toStringTag,{value:"Module"})),Z={options:{type:Object,default:()=>({}),custom:!0}},W=t=>({options:t.options,methods:{}}),bt=Object.freeze(Object.defineProperty({__proto__:null,componentProps:Z,setupComponent:W},Symbol.toStringTag,{value:"Module"})),G={...Z,pane:{type:String},attribution:{type:String},name:{type:String,custom:!0},layerType:{type:String,custom:!0},visible:{type:Boolean,custom:!0,default:!0}},J=(t,o,e)=>{const r=v(I),n=v(ee),{options:l,methods:s}=W(t),a=_(t,G,l),i=()=>r({leafletObject:o.value}),u=()=>n({leafletObject:o.value}),d={...s,setAttribution(y){u(),o.value.options.attribution=y,t.visible&&i()},setName(){u(),t.visible&&i()},setLayerType(){u(),t.visible&&i()},setVisible(y){o.value&&(y?i():u())},bindPopup(y){if(!o.value||!k(o.value.bindPopup)){console.warn("Attempt to bind popup before bindPopup method available on layer.");return}o.value.bindPopup(y)},bindTooltip(y){if(!o.value||!k(o.value.bindTooltip)){console.warn("Attempt to bind tooltip before bindTooltip method available on layer.");return}o.value.bindTooltip(y)},unbindTooltip(){o.value&&(k(o.value.closeTooltip)&&o.value.closeTooltip(),k(o.value.unbindTooltip)&&o.value.unbindTooltip())},unbindPopup(){o.value&&(k(o.value.closePopup)&&o.value.closePopup(),k(o.value.unbindPopup)&&o.value.unbindPopup())},updateVisibleProp(y){e.emit("update:visible",y)}};return w(_e,d.bindPopup),w(ge,d.bindTooltip),w(Le,d.unbindPopup),w(Oe,d.unbindTooltip),ce(()=>{d.unbindPopup(),d.unbindTooltip(),u()}),{options:a,methods:d}},M=(t,o)=>{if(t&&o.default)return V("div",{style:{display:"none"}},o.default())},_t=Object.freeze(Object.defineProperty({__proto__:null,layerProps:G,render:M,setupLayer:J},Symbol.toStringTag,{value:"Module"})),he={...G,interactive:{type:Boolean,default:void 0},bubblingMouseEvents:{type:Boolean,default:void 0}},qe=(t,o,e)=>{const{options:r,methods:n}=J(t,o,e);return{options:_(t,he,r),methods:n}},gt=Object.freeze(Object.defineProperty({__proto__:null,interactiveLayerProps:he,setupInteractiveLayer:qe},Symbol.toStringTag,{value:"Module"})),re={...he,stroke:{type:Boolean,default:void 0},color:{type:String},weight:{type:Number},opacity:{type:Number},lineCap:{type:String},lineJoin:{type:String},dashArray:{type:String},dashOffset:{type:String},fill:{type:Boolean,default:void 0},fillColor:{type:String},fillOpacity:{type:Number},fillRule:{type:String},className:{type:String}},Se=(t,o,e)=>{const{options:r,methods:n}=qe(t,o,e),l=_(t,re,r),s=v(ee),a={...n,setStroke(i){o.value.setStyle({stroke:i})},setColor(i){o.value.setStyle({color:i})},setWeight(i){o.value.setStyle({weight:i})},setOpacity(i){o.value.setStyle({opacity:i})},setLineCap(i){o.value.setStyle({lineCap:i})},setLineJoin(i){o.value.setStyle({lineJoin:i})},setDashArray(i){o.value.setStyle({dashArray:i})},setDashOffset(i){o.value.setStyle({dashOffset:i})},setFill(i){o.value.setStyle({fill:i})},setFillColor(i){o.value.setStyle({fillColor:i})},setFillOpacity(i){o.value.setStyle({fillOpacity:i})},setFillRule(i){o.value.setStyle({fillRule:i})},setClassName(i){o.value.setStyle({className:i})}};return Y(()=>{s({leafletObject:o.value})}),{options:l,methods:a}},Lt=Object.freeze(Object.defineProperty({__proto__:null,pathProps:re,setupPath:Se},Symbol.toStringTag,{value:"Module"})),le={...re,radius:{type:Number},latLng:{type:[Object,Array],required:!0,custom:!0}},je=(t,o,e)=>{const{options:r,methods:n}=Se(t,o,e),l=_(t,le,r),s={...n,setRadius(a){o.value.setRadius(a)},setLatLng(a){o.value.setLatLng(a)}};return{options:l,methods:s}},Ot=Object.freeze(Object.defineProperty({__proto__:null,circleMarkerProps:le,setupCircleMarker:je},Symbol.toStringTag,{value:"Module"})),Pe={...le,radius:{type:Number}},He=(t,o,e)=>{const{options:r,methods:n}=je(t,o,e),l=_(t,Pe,r),s={...n};return{options:l,methods:s}},ht=Object.freeze(Object.defineProperty({__proto__:null,circleProps:Pe,setupCircle:He},Symbol.toStringTag,{value:"Module"})),Ht=h({name:"LCircle",props:Pe,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{options:s,methods:a}=He(t,e,o);return j(async()=>{const{circle:i}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(i(t.latLng,s));const{listeners:u}=A(o.attrs);e.value.on(u),L(a,e.value,t),l({...t,...a,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),Kt=h({name:"LCircleMarker",props:le,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{options:s,methods:a}=je(t,e,o);return j(async()=>{const{circleMarker:i}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(i(t.latLng,s));const{listeners:u}=A(o.attrs);e.value.on(u),L(a,e.value,t),l({...t,...a,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),U={...Z,position:{type:String}},q=(t,o)=>{const{options:e,methods:r}=W(t),n=_(t,U,e),l={...r,setPosition(s){o.value&&o.value.setPosition(s)}};return ce(()=>{o.value&&o.value.remove()}),{options:n,methods:l}},Ke=t=>t.default?V("div",{ref:"root"},t.default()):null,St=Object.freeze(Object.defineProperty({__proto__:null,controlProps:U,renderLControl:Ke,setupControl:q},Symbol.toStringTag,{value:"Module"})),Qt=h({name:"LControl",props:{...U,disableClickPropagation:{type:Boolean,custom:!0,default:!0},disableScrollPropagation:{type:Boolean,custom:!0,default:!1}},setup(t,o){const e=p(),r=p(),n=S(O),l=v(F),{options:s,methods:a}=q(t,e);return j(async()=>{const{Control:i,DomEvent:u}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]),d=i.extend({onAdd(){return r.value}});e.value=T(new d(s)),L(a,e.value,t),l({leafletObject:e.value}),t.disableClickPropagation&&r.value&&u.disableClickPropagation(r.value),t.disableScrollPropagation&&r.value&&u.disableScrollPropagation(r.value),g(()=>o.emit("ready",e.value))}),{root:r,leafletObject:e}},render(){return Ke(this.$slots)}}),Te={...U,prefix:{type:String}},Qe=(t,o)=>{const{options:e,methods:r}=q(t,o),n=_(t,Te,e),l={...r,setPrefix(s){o.value.setPrefix(s)}};return{options:n,methods:l}},jt=Object.freeze(Object.defineProperty({__proto__:null,controlAttributionProps:Te,setupControlAttribution:Qe},Symbol.toStringTag,{value:"Module"})),Xt=h({name:"LControlAttribution",props:Te,setup(t,o){const e=p(),r=S(O),n=v(F),{options:l,methods:s}=Qe(t,e);return j(async()=>{const{control:a}=r?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(a.attribution(l)),L(s,e.value,t),n({leafletObject:e.value}),g(()=>o.emit("ready",e.value))}),{leafletObject:e}},render(){return null}}),Ce={...U,collapsed:{type:Boolean,default:void 0},autoZIndex:{type:Boolean,default:void 0},hideSingleBase:{type:Boolean,default:void 0},sortLayers:{type:Boolean,default:void 0},sortFunction:{type:Function}},Xe=(t,o)=>{const{options:e}=q(t,o);return{options:_(t,Ce,e),methods:{addLayer(r){r.layerType==="base"?o.value.addBaseLayer(r.leafletObject,r.name):r.layerType==="overlay"&&o.value.addOverlay(r.leafletObject,r.name)},removeLayer(r){o.value.removeLayer(r.leafletObject)}}}},Pt=Object.freeze(Object.defineProperty({__proto__:null,controlLayersProps:Ce,setupControlLayers:Xe},Symbol.toStringTag,{value:"Module"})),Yt=h({name:"LControlLayers",props:Ce,setup(t,o){const e=p(),r=S(O),n=v(ve),{options:l,methods:s}=Xe(t,e);return j(async()=>{const{control:a}=r?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(a.layers(void 0,void 0,l)),L(s,e.value,t),n({...t,...s,leafletObject:e.value}),g(()=>o.emit("ready",e.value))}),{leafletObject:e}},render(){return null}}),Ae={...U,maxWidth:{type:Number},metric:{type:Boolean,default:void 0},imperial:{type:Boolean,default:void 0},updateWhenIdle:{type:Boolean,default:void 0}},Ye=(t,o)=>{const{options:e,methods:r}=q(t,o);return{options:_(t,Ae,e),methods:r}},Tt=Object.freeze(Object.defineProperty({__proto__:null,controlScaleProps:Ae,setupControlScale:Ye},Symbol.toStringTag,{value:"Module"})),eo=h({name:"LControlScale",props:Ae,setup(t,o){const e=p(),r=S(O),n=v(F),{options:l,methods:s}=Ye(t,e);return j(async()=>{const{control:a}=r?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(a.scale(l)),L(s,e.value,t),n({leafletObject:e.value}),g(()=>o.emit("ready",e.value))}),{leafletObject:e}},render(){return null}}),Ie={...U,zoomInText:{type:String},zoomInTitle:{type:String},zoomOutText:{type:String},zoomOutTitle:{type:String}},et=(t,o)=>{const{options:e,methods:r}=q(t,o);return{options:_(t,Ie,e),methods:r}},Ct=Object.freeze(Object.defineProperty({__proto__:null,controlZoomProps:Ie,setupControlZoom:et},Symbol.toStringTag,{value:"Module"})),to=h({name:"LControlZoom",props:Ie,setup(t,o){const e=p(),r=S(O),n=v(F),{options:l,methods:s}=et(t,e);return j(async()=>{const{control:a}=r?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(a.zoom(l)),L(s,e.value,t),n({leafletObject:e.value}),g(()=>o.emit("ready",e.value))}),{leafletObject:e}},render(){return null}}),te={...G},ne=(t,o,e)=>{const{options:r,methods:n}=J(t,o,e),l=_(t,te,r),s={...n,addLayer(a){o.value.addLayer(a.leafletObject)},removeLayer(a){o.value.removeLayer(a.leafletObject)}};return w(I,s.addLayer),w(ee,s.removeLayer),{options:l,methods:s}},At=Object.freeze(Object.defineProperty({__proto__:null,layerGroupProps:te,setupLayerGroup:ne},Symbol.toStringTag,{value:"Module"})),Re={...te},tt=(t,o,e)=>{const{options:r,methods:n}=ne(t,o,e),l=_(t,Re,r),s={...n};return{options:l,methods:s}},It=Object.freeze(Object.defineProperty({__proto__:null,featureGroupProps:Re,setupFeatureGroup:tt},Symbol.toStringTag,{value:"Module"})),oo=h({props:Re,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{methods:s,options:a}=tt(t,e,o);return j(async()=>{const{featureGroup:i}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(i(void 0,a));const{listeners:u}=A(o.attrs);e.value.on(u),L(s,e.value,t),l({...t,...s,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),Be={...te,geojson:{type:[Object,Array],custom:!0},optionsStyle:{type:Function,custom:!0}},ot=(t,o,e)=>{const{options:r,methods:n}=ne(t,o,e),l=_(t,Be,r);Object.prototype.hasOwnProperty.call(t,"optionsStyle")&&(l.style=t.optionsStyle);const s={...n,setGeojson(a){o.value.clearLayers(),o.value.addData(a)},setOptionsStyle(a){o.value.setStyle(a)},getGeoJSONData(){return o.value.toGeoJSON()},getBounds(){return o.value.getBounds()}};return{options:l,methods:s}},Rt=Object.freeze(Object.defineProperty({__proto__:null,geoJSONProps:Be,setupGeoJSON:ot},Symbol.toStringTag,{value:"Module"})),ro=h({props:Be,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{methods:s,options:a}=ot(t,e,o);return j(async()=>{const{geoJSON:i}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(i(t.geojson,a));const{listeners:u}=A(o.attrs);e.value.on(u),L(s,e.value,t),l({...t,...s,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),ae={...G,opacity:{type:Number},zIndex:{type:Number},tileSize:{type:[Number,Array,Object]},noWrap:{type:Boolean,default:void 0},minZoom:{type:Number},maxZoom:{type:Number},className:{type:String}},Ee=(t,o,e)=>{const{options:r,methods:n}=J(t,o,e),l=_(t,ae,r),s={...n,setTileComponent(){var a;(a=o.value)==null||a.redraw()}};return ce(()=>{o.value.off()}),{options:l,methods:s}},rt=(t,o,e,r)=>t.extend({initialize(n){this.tileComponents={},this.on("tileunload",this._unloadTile),e.setOptions(this,n)},createTile(n){const l=this._tileCoordsToKey(n);this.tileComponents[l]=o.create("div");const s=V({setup:r,props:["coords"]},{coords:n});return ft(s,this.tileComponents[l]),this.tileComponents[l]},_unloadTile(n){const l=this._tileCoordsToKey(n.coords);this.tileComponents[l]&&(this.tileComponents[l].innerHTML="",this.tileComponents[l]=void 0)}}),Bt=Object.freeze(Object.defineProperty({__proto__:null,CreateVueGridLayer:rt,gridLayerProps:ae,setupGridLayer:Ee},Symbol.toStringTag,{value:"Module"})),lo=h({props:{...ae,childRender:{type:Function,required:!0}},setup(t,o){const e=p(),r=p(null),n=p(!1),l=S(O),s=v(I),{options:a,methods:i}=Ee(t,e,o);return j(async()=>{const{GridLayer:u,DomUtil:d,Util:y}=l?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]),B=rt(u,d,y,t.childRender);e.value=T(new B(a));const{listeners:f}=A(o.attrs);e.value.on(f),L(i,e.value,t),s({...t,...i,leafletObject:e.value}),n.value=!0,g(()=>o.emit("ready",e.value))}),{root:r,ready:n,leafletObject:e}},render(){return this.ready?V("div",{style:{display:"none"},ref:"root"}):null}}),de={iconUrl:{type:String},iconRetinaUrl:{type:String},iconSize:{type:[Object,Array]},iconAnchor:{type:[Object,Array]},popupAnchor:{type:[Object,Array]},tooltipAnchor:{type:[Object,Array]},shadowUrl:{type:String},shadowRetinaUrl:{type:String},shadowSize:{type:[Object,Array]},shadowAnchor:{type:[Object,Array]},bgPos:{type:[Object,Array]},className:{type:String}},Et=Object.freeze(Object.defineProperty({__proto__:null,iconProps:de},Symbol.toStringTag,{value:"Module"})),no=h({name:"LIcon",props:{...de,...Z},setup(t,o){const e=p(),r=S(O),n=v(me),l=v(fe),s=v(be);let a,i,u,d,y;const B=(D,C,R)=>{const E=D&&D.innerHTML;if(!C){R&&y&&n()&&l(E);return}const{listeners:$}=A(o.attrs);y&&i(y,$);const{options:ue}=W(t),N=_(t,de,ue);E&&(N.html=E),y=N.html?u(N):d(N),a(y,$),s(y)},f=()=>{g(()=>B(e.value,!0,!1))},z=()=>{g(()=>B(e.value,!1,!0))},x={setIconUrl:f,setIconRetinaUrl:f,setIconSize:f,setIconAnchor:f,setPopupAnchor:f,setTooltipAnchor:f,setShadowUrl:f,setShadowRetinaUrl:f,setShadowAnchor:f,setBgPos:f,setClassName:f,setHtml:f};return j(async()=>{const{DomEvent:D,divIcon:C,icon:R}=r?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);a=D.on,i=D.off,u=C,d=R,L(x,{},t),new MutationObserver(z).observe(e.value,{attributes:!0,childList:!0,characterData:!0,subtree:!0}),f()}),{root:e}},render(){const t=this.$slots.default?this.$slots.default():void 0;return V("div",{ref:"root"},t)}}),we={...G,opacity:{type:Number},alt:{type:String},interactive:{type:Boolean,default:void 0},crossOrigin:{type:Boolean,default:void 0},errorOverlayUrl:{type:String},zIndex:{type:Number},className:{type:String},url:{type:String,required:!0,custom:!0},bounds:{type:[Array,Object],required:!0,custom:!0}},lt=(t,o,e)=>{const{options:r,methods:n}=J(t,o,e),l=_(t,we,r),s={...n,setOpacity(a){return o.value.setOpacity(a)},setUrl(a){return o.value.setUrl(a)},setBounds(a){return o.value.setBounds(a)},getBounds(){return o.value.getBounds()},getElement(){return o.value.getElement()},bringToFront(){return o.value.bringToFront()},bringToBack(){return o.value.bringToBack()},setZIndex(a){return o.value.setZIndex(a)}};return{options:l,methods:s}},wt=Object.freeze(Object.defineProperty({__proto__:null,imageOverlayProps:we,setupImageOverlay:lt},Symbol.toStringTag,{value:"Module"})),ao=h({name:"LImageOverlay",props:we,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{options:s,methods:a}=lt(t,e,o);return j(async()=>{const{imageOverlay:i}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(i(t.url,t.bounds,s));const{listeners:u}=A(o.attrs);e.value.on(u),L(a,e.value,t),l({...t,...a,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),so=h({props:te,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{methods:s}=ne(t,e,o);return j(async()=>{const{layerGroup:a}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(a(void 0,t.options));const{listeners:i}=A(o.attrs);e.value.on(i),L(s,e.value,t),l({...t,...s,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}});function nt(t,o,e){var r,n,l;o===void 0&&(o=50),e===void 0&&(e={});var s=(r=e.isImmediate)!=null&&r,a=(n=e.callback)!=null&&n,i=e.maxWait,u=Date.now(),d=[];function y(){if(i!==void 0){var f=Date.now()-u;if(f+o>=i)return i-f}return o}var B=function(){var f=[].slice.call(arguments),z=this;return new Promise(function(x,D){var C=s&&l===void 0;if(l!==void 0&&clearTimeout(l),l=setTimeout(function(){if(l=void 0,u=Date.now(),!s){var E=t.apply(z,f);a&&a(E),d.forEach(function($){return(0,$.resolve)(E)}),d=[]}},y()),C){var R=t.apply(z,f);return a&&a(R),x(R)}d.push({resolve:x,reject:D})})};return B.cancel=function(f){l!==void 0&&clearTimeout(l),d.forEach(function(z){return(0,z.reject)(f)}),d=[]},B}const Ze={...Z,center:{type:[Object,Array]},bounds:{type:[Array,Object]},maxBounds:{type:[Array,Object]},zoom:{type:Number},minZoom:{type:Number},maxZoom:{type:Number},paddingBottomRight:{type:[Object,Array]},paddingTopLeft:{type:Object},padding:{type:Object},worldCopyJump:{type:Boolean,default:void 0},crs:{type:[String,Object]},maxBoundsViscosity:{type:Number},inertia:{type:Boolean,default:void 0},inertiaDeceleration:{type:Number},inertiaMaxSpeed:{type:Number},easeLinearity:{type:Number},zoomAnimation:{type:Boolean,default:void 0},zoomAnimationThreshold:{type:Number},fadeAnimation:{type:Boolean,default:void 0},markerZoomAnimation:{type:Boolean,default:void 0},noBlockingAnimations:{type:Boolean,default:void 0},useGlobalLeaflet:{type:Boolean,default:!0,custom:!0}},io=h({inheritAttrs:!1,emits:["ready","update:zoom","update:center","update:bounds"],props:Ze,setup(t,o){const e=p(),r=mt({ready:!1,layersToAdd:[],layersInControl:[]}),{options:n}=W(t),l=_(t,Ze,n),{listeners:s,attrs:a}=A(o.attrs),i=K(I),u=K(ee),d=K(F),y=K(ve);w(O,t.useGlobalLeaflet);const B=oe(()=>{const C={};return t.noBlockingAnimations&&(C.animate=!1),C}),f=oe(()=>{const C=B.value;return t.padding&&(C.padding=t.padding),t.paddingTopLeft&&(C.paddingTopLeft=t.paddingTopLeft),t.paddingBottomRight&&(C.paddingBottomRight=t.paddingBottomRight),C}),z={moveend:nt(C=>{r.leafletRef&&(o.emit("update:zoom",r.leafletRef.getZoom()),o.emit("update:center",r.leafletRef.getCenter()),o.emit("update:bounds",r.leafletRef.getBounds()))}),overlayadd(C){const R=r.layersInControl.find(E=>E.name===C.name);R&&R.updateVisibleProp(!0)},overlayremove(C){const R=r.layersInControl.find(E=>E.name===C.name);R&&R.updateVisibleProp(!1)}};j(async()=>{t.useGlobalLeaflet&&(b.L=b.L||await m(()=>import("./leaflet-src-CSm5Mvdr.js").then(c=>c.l),__vite__mapDeps([2,0,1])));const{map:C,CRS:R,Icon:E,latLngBounds:$,latLng:ue,stamp:N}=t.useGlobalLeaflet?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);try{l.beforeMapMount&&await l.beforeMapMount()}catch(c){console.error(`The following error occurred running the provided beforeMapMount hook ${c.message}`)}await Je(E);const vt=typeof l.crs=="string"?R[l.crs]:l.crs;l.crs=vt||R.EPSG3857;const H={addLayer(c){c.layerType!==void 0&&(r.layerControl===void 0?r.layersToAdd.push(c):r.layersInControl.find(P=>N(P.leafletObject)===N(c.leafletObject))||(r.layerControl.addLayer(c),r.layersInControl.push(c))),c.visible!==!1&&r.leafletRef.addLayer(c.leafletObject)},removeLayer(c){c.layerType!==void 0&&(r.layerControl===void 0?r.layersToAdd=r.layersToAdd.filter(P=>P.name!==c.name):(r.layerControl.removeLayer(c.leafletObject),r.layersInControl=r.layersInControl.filter(P=>N(P.leafletObject)!==N(c.leafletObject)))),r.leafletRef.removeLayer(c.leafletObject)},registerLayerControl(c){r.layerControl=c,r.layersToAdd.forEach(P=>{r.layerControl.addLayer(P)}),r.layersToAdd=[],d(c)},registerControl(c){r.leafletRef.addControl(c.leafletObject)},setZoom(c){const P=r.leafletRef.getZoom();c!==P&&r.leafletRef.setZoom(c,B.value)},setCrs(c){const P=r.leafletRef.getBounds();r.leafletRef.options.crs=c,r.leafletRef.fitBounds(P,{animate:!1,padding:[0,0]})},fitBounds(c){r.leafletRef.fitBounds(c,f.value)},setBounds(c){if(!c)return;const P=$(c);P.isValid()&&!(r.lastSetBounds||r.leafletRef.getBounds()).equals(P,0)&&(r.lastSetBounds=P,r.leafletRef.fitBounds(P))},setCenter(c){if(c==null)return;const P=ue(c),$e=r.lastSetCenter||r.leafletRef.getCenter();($e.lat!==P.lat||$e.lng!==P.lng)&&(r.lastSetCenter=P,r.leafletRef.panTo(P,B.value))}};Q(i,H.addLayer),Q(u,H.removeLayer),Q(d,H.registerControl),Q(y,H.registerLayerControl),r.leafletRef=T(C(e.value,l)),L(H,r.leafletRef,t),pe(r.leafletRef,z),pe(r.leafletRef,s),r.ready=!0,g(()=>o.emit("ready",r.leafletRef))}),Y(()=>{ye(z),r.leafletRef&&(r.leafletRef.off(),r.leafletRef.remove())});const x=oe(()=>r.leafletRef),D=oe(()=>r.ready);return{root:e,ready:D,leafletObject:x,attrs:a}},render({attrs:t}){return t.style||(t.style={}),t.style.width||(t.style.width="100%"),t.style.height||(t.style.height="100%"),V("div",{...t,ref:"root"},this.ready&&this.$slots.default?this.$slots.default():{})}}),Mt=["Symbol(Comment)","Symbol(Text)"],zt=["LTooltip","LPopup"],Me={...G,draggable:{type:Boolean,default:void 0},icon:{type:[Object]},zIndexOffset:{type:Number},latLng:{type:[Object,Array],custom:!0,required:!0}},at=(t,o,e)=>{const{options:r,methods:n}=J(t,o,e),l=_(t,Me,r),s={...n,setDraggable(a){o.value.dragging&&(a?o.value.dragging.enable():o.value.dragging.disable())},latLngSync(a){e.emit("update:latLng",a.latlng),e.emit("update:lat-lng",a.latlng)},setLatLng(a){if(a!=null&&o.value){const i=o.value.getLatLng();(!i||!i.equals(a))&&o.value.setLatLng(a)}}};return{options:l,methods:s}},st=(t,o)=>{const e=o.slots.default&&o.slots.default();return e&&e.length&&e.some(Dt)};function Dt(t){return!(Mt.includes(t.type.toString())||zt.includes(t.type.name))}const Nt=Object.freeze(Object.defineProperty({__proto__:null,markerProps:Me,setupMarker:at,shouldBlankIcon:st},Symbol.toStringTag,{value:"Module"})),uo=h({name:"LMarker",props:Me,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I);w(me,()=>{var u;return!!((u=e.value)!=null&&u.getElement())}),w(fe,u=>{var d,y;const B=k((d=e.value)==null?void 0:d.getElement)&&((y=e.value)==null?void 0:y.getElement());B&&(B.innerHTML=u)}),w(be,u=>{var d;return((d=e.value)==null?void 0:d.setIcon)&&e.value.setIcon(u)});const{options:s,methods:a}=at(t,e,o),i={moveHandler:nt(a.latLngSync)};return j(async()=>{const{marker:u,divIcon:d}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);st(s,o)&&(s.icon=d({className:""})),e.value=T(u(t.latLng,s));const{listeners:y}=A(o.attrs);e.value.on(y),e.value.on("move",i.moveHandler),L(a,e.value,t),l({...t,...a,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),Y(()=>ye(i)),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),se={...re,smoothFactor:{type:Number},noClip:{type:Boolean,default:void 0},latLngs:{type:Array,required:!0,custom:!0}},ze=(t,o,e)=>{const{options:r,methods:n}=Se(t,o,e),l=_(t,se,r),s={...n,setSmoothFactor(a){o.value.setStyle({smoothFactor:a})},setNoClip(a){o.value.setStyle({noClip:a})},addLatLng(a){o.value.addLatLng(a)}};return{options:l,methods:s}},kt=Object.freeze(Object.defineProperty({__proto__:null,polylineProps:se,setupPolyline:ze},Symbol.toStringTag,{value:"Module"})),X={...se},De=(t,o,e)=>{const{options:r,methods:n}=ze(t,o,e),l=_(t,X,r),s={...n,toGeoJSON(a){return o.value.toGeoJSON(a)}};return{options:l,methods:s}},Vt=Object.freeze(Object.defineProperty({__proto__:null,polygonProps:X,setupPolygon:De},Symbol.toStringTag,{value:"Module"})),po=h({name:"LPolygon",props:X,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{options:s,methods:a}=De(t,e,o);return j(async()=>{const{polygon:i}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(i(t.latLngs,s));const{listeners:u}=A(o.attrs);e.value.on(u),L(a,e.value,t),l({...t,...a,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),co=h({name:"LPolyline",props:se,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{options:s,methods:a}=ze(t,e,o);return j(async()=>{const{polyline:i}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(i(t.latLngs,s));const{listeners:u}=A(o.attrs);e.value.on(u),L(a,e.value,t),l({...t,...a,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),Ne={...Z,content:{type:String,default:null}},ke=(t,o)=>{const{options:e,methods:r}=W(t),n={...r,setContent(l){o.value&&l!==null&&l!==void 0&&o.value.setContent(l)}};return{options:e,methods:n}},Ve=t=>t.default?V("div",{ref:"root"},t.default()):null,Gt=Object.freeze(Object.defineProperty({__proto__:null,popperProps:Ne,render:Ve,setupPopper:ke},Symbol.toStringTag,{value:"Module"})),it={...Ne,latLng:{type:[Object,Array],default:()=>[]}},ut=(t,o)=>{const{options:e,methods:r}=ke(t,o);return{options:e,methods:r}},Ut=Object.freeze(Object.defineProperty({__proto__:null,popupProps:it,setupPopup:ut},Symbol.toStringTag,{value:"Module"})),yo=h({name:"LPopup",props:it,setup(t,o){const e=p(),r=p(null),n=S(O),l=v(_e),s=v(Le),{options:a,methods:i}=ut(t,e);return j(async()=>{const{popup:u}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(u(a)),t.latLng!==void 0&&e.value.setLatLng(t.latLng),L(i,e.value,t);const{listeners:d}=A(o.attrs);e.value.on(d),e.value.setContent(t.content||r.value||""),l(e.value),g(()=>o.emit("ready",e.value))}),Y(()=>{s()}),{root:r,leafletObject:e}},render(){return Ve(this.$slots)}}),Ge={...X,latLngs:{...X.latLngs,required:!1},bounds:{type:Object,custom:!0}},pt=(t,o,e)=>{const{options:r,methods:n}=De(t,o,e),l=_(t,Ge,r),s={...n,setBounds(a){o.value.setBounds(a)},setLatLngs(a){o.value.setBounds(a)}};return{options:l,methods:s}},xt=Object.freeze(Object.defineProperty({__proto__:null,rectangleProps:Ge,setupRectangle:pt},Symbol.toStringTag,{value:"Module"})),vo=h({name:"LRectangle",props:Ge,setup(t,o){const e=p(),r=p(!1),n=S(O),l=v(I),{options:s,methods:a}=pt(t,e,o);return j(async()=>{const{rectangle:i,latLngBounds:u}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]),d=t.bounds?u(t.bounds):u(t.latLngs||[]);e.value=T(i(d,s));const{listeners:y}=A(o.attrs);e.value.on(y),L(a,e.value,t),l({...t,...a,leafletObject:e.value}),r.value=!0,g(()=>o.emit("ready",e.value))}),{ready:r,leafletObject:e}},render(){return M(this.ready,this.$slots)}}),ie={...ae,tms:{type:Boolean,default:void 0},subdomains:{type:[String,Array],validator:t=>typeof t=="string"?!0:Array.isArray(t)?t.every(o=>typeof o=="string"):!1},detectRetina:{type:Boolean,default:void 0},url:{type:String,required:!0,custom:!0}},Ue=(t,o,e)=>{const{options:r,methods:n}=Ee(t,o,e),l=_(t,ie,r),s={...n};return{options:l,methods:s}},$t=Object.freeze(Object.defineProperty({__proto__:null,setupTileLayer:Ue,tileLayerProps:ie},Symbol.toStringTag,{value:"Module"})),mo=h({props:ie,setup(t,o){const e=p(),r=S(O),n=v(I),{options:l,methods:s}=Ue(t,e,o);return j(async()=>{const{tileLayer:a}=r?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(a(t.url,l));const{listeners:i}=A(o.attrs);e.value.on(i),L(s,e.value,t),n({...t,...s,leafletObject:e.value}),g(()=>o.emit("ready",e.value))}),{leafletObject:e}},render(){return null}}),dt={...Ne},ct=(t,o)=>{const{options:e,methods:r}=ke(t,o),n=v(Oe);return Y(()=>{n()}),{options:e,methods:r}},Ft=Object.freeze(Object.defineProperty({__proto__:null,setupTooltip:ct,tooltipProps:dt},Symbol.toStringTag,{value:"Module"})),fo=h({name:"LTooltip",props:dt,setup(t,o){const e=p(),r=p(null),n=S(O),l=v(ge),{options:s,methods:a}=ct(t,e);return j(async()=>{const{tooltip:i}=n?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(i(s)),L(a,e.value,t);const{listeners:u}=A(o.attrs);e.value.on(u),e.value.setContent(t.content||r.value||""),l(e.value),g(()=>o.emit("ready",e.value))}),{root:r,leafletObject:e}},render(){return Ve(this.$slots)}}),xe={...ie,layers:{type:String,required:!0},styles:{type:String},format:{type:String},transparent:{type:Boolean,default:void 0},version:{type:String},crs:{type:Object},uppercase:{type:Boolean,default:void 0}},yt=(t,o,e)=>{const{options:r,methods:n}=Ue(t,o,e);return{options:_(t,xe,r),methods:{...n}}},Zt=Object.freeze(Object.defineProperty({__proto__:null,setupWMSTileLayer:yt,wmsTileLayerProps:xe},Symbol.toStringTag,{value:"Module"})),bo=h({props:xe,setup(t,o){const e=p(),r=S(O),n=v(I),{options:l,methods:s}=yt(t,e,o);return j(async()=>{const{tileLayer:a}=r?b.L:await m(()=>import("./leaflet-src.esm-HdBnhJze.js"),[]);e.value=T(a.wms(t.url,l));const{listeners:i}=A(o.attrs);e.value.on(i),L(s,e.value,t),n({...t,...s,leafletObject:e.value}),g(()=>o.emit("ready",e.value))}),{leafletObject:e}},render(){return null}}),_o=Object.freeze(Object.defineProperty({__proto__:null,Circle:ht,CircleMarker:Ot,Component:bt,Control:St,ControlAttribution:jt,ControlLayers:Pt,ControlScale:Tt,ControlZoom:Ct,FeatureGroup:It,GeoJSON:Rt,GridLayer:Bt,Icon:Et,ImageOverlay:wt,InteractiveLayer:gt,Layer:_t,LayerGroup:At,Marker:Nt,Path:Lt,Polygon:Vt,Polyline:kt,Popper:Gt,Popup:Ut,Rectangle:xt,TileLayer:$t,Tooltip:Ft,WmsTileLayer:Zt},Symbol.toStringTag,{value:"Module"}));export{_o as Functions,qt as InjectionKeys,Ht as LCircle,Kt as LCircleMarker,Qt as LControl,Xt as LControlAttribution,Yt as LControlLayers,eo as LControlScale,to as LControlZoom,oo as LFeatureGroup,ro as LGeoJson,lo as LGridLayer,no as LIcon,ao as LImageOverlay,so as LLayerGroup,io as LMap,uo as LMarker,po as LPolygon,co as LPolyline,yo as LPopup,vo as LRectangle,mo as LTileLayer,fo as LTooltip,bo as LWmsTileLayer,Jt as Utilities};
