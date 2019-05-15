(window.webpackJsonp=window.webpackJsonp||[]).push([[30],{228:function(t,a,s){"use strict";s.r(a);var e=s(2),n=Object(e.a)({},function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("ContentSlotsDistributor",{attrs:{"slot-key":t.$parent.slotKey}},[s("h1",{attrs:{id:"autocomplete"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#autocomplete","aria-hidden":"true"}},[t._v("#")]),t._v(" Autocomplete")]),t._v(" "),s("p",[t._v("Class: "),s("code",{staticClass:"inline"},[t._v("Code16\\"),s("span",{staticClass:"token package"},[t._v("Sharp"),s("span",{staticClass:"token punctuation"},[t._v("\\")]),t._v("Form"),s("span",{staticClass:"token punctuation"},[t._v("\\")]),t._v("Fields"),s("span",{staticClass:"token punctuation"},[t._v("\\")]),t._v("SharpFormAutocompleteField")])])]),t._v(" "),s("h2",{attrs:{id:"configuration"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#configuration","aria-hidden":"true"}},[t._v("#")]),t._v(" Configuration")]),t._v(" "),s("h3",{attrs:{id:"self-make-string-key-string-mode"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#self-make-string-key-string-mode","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[t._v("self"),s("span",{staticClass:"token punctuation"},[t._v(":")]),s("span",{staticClass:"token punctuation"},[t._v(":")]),s("span",{staticClass:"token function"},[t._v("make")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$key")]),s("span",{staticClass:"token punctuation"},[t._v(",")]),t._v(" string "),s("span",{staticClass:"token variable"},[t._v("$mode")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[s("code",{staticClass:"inline"},[s("span",{staticClass:"token variable"},[t._v("$mode")])]),t._v(' must be either "local" (dictionary is defined locally with '),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setLocalValues")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),s("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(') or "remote" (a endpoint must be provided).')]),t._v(" "),s("h3",{attrs:{id:"setlocalvalues-localvalues"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setlocalvalues-localvalues","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setLocalValues")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),s("span",{staticClass:"token variable"},[t._v("$localValues")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("Set the values of the dictionary on mode=local, as an object array with at least an "),s("code",{staticClass:"inline"},[t._v("id")]),t._v(" attribute (or the "),s("code",{staticClass:"inline"},[t._v("setItemIdAttribute")]),t._v(" value).")]),t._v(" "),s("h3",{attrs:{id:"setlocalsearchkeys-array-searchkeys"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setlocalsearchkeys-array-searchkeys","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setLocalSearchKeys")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),s("span",{staticClass:"token keyword"},[t._v("array")]),t._v(" "),s("span",{staticClass:"token variable"},[t._v("$searchKeys")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("Set the names of the attributes used in the search (mode=local).\nDefault: "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token punctuation"},[t._v("[")]),s("span",{staticClass:"token double-quoted-string string"},[t._v('"value"')]),s("span",{staticClass:"token punctuation"},[t._v("]")])])]),t._v(" "),s("h3",{attrs:{id:"setsearchminchars-int-searchminchars"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setsearchminchars-int-searchminchars","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setSearchMinChars")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("int "),s("span",{staticClass:"token variable"},[t._v("$searchMinChars")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("Set a minimum number of character to type before performing the search.\nDefault: "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token number"},[t._v("1")])])]),t._v(" "),s("h3",{attrs:{id:"setremoteendpoint-string-remoteendpoint"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setremoteendpoint-string-remoteendpoint","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setRemoteEndpoint")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$remoteEndpoint")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("The endpoint to hit with mode=remote.")]),t._v(" "),s("p",[t._v("If this endpoint is yours ("),s("code",{staticClass:"inline"},[t._v("remote")]),t._v(" mode here is useful to avoid loading a lot of data in the view), you can add the "),s("code",{staticClass:"inline"},[t._v("sharp_auth")]),t._v(" middleware to the API route to handle authentication and prevent this API endpoint to be called by non-sharp users:")]),t._v(" "),s("div",{staticClass:"language-php extra-class"},[s("pre",{pre:!0,attrs:{class:"language-php"}},[s("code",[t._v("Route"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("get")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token single-quoted-string string"}},[t._v("'/api/sharp/clients'")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("middleware")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token single-quoted-string string"}},[t._v("'sharp_auth'")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("uses")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"MySharpApiClientController@index"')]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n")])])]),s("h3",{attrs:{id:"setremotesearchattribute-string-remotesearchattribute"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setremotesearchattribute-string-remotesearchattribute","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setRemoteSearchAttribute")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$remoteSearchAttribute")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("The attribute name sent to the remote endpoint as search key.\nDefault: "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token double-quoted-string string"},[t._v('"query"')])])]),t._v(" "),s("h3",{attrs:{id:"setremotemethodget"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setremotemethodget","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setRemoteMethodGET")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("h3",{attrs:{id:"setremotemethodpost"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setremotemethodpost","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setRemoteMethodPOST")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("Set the remote method to GET (default) or POST.")]),t._v(" "),s("h3",{attrs:{id:"setitemidattribute-string-itemidattribute"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setitemidattribute-string-itemidattribute","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setItemIdAttribute")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$itemIdAttribute")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("Set the name of the id attribute for items. This is useful :")]),t._v(" "),s("ul",[s("li",[t._v("if you pass an object as the data for the autocomplete (meaning: in the formatter's "),s("code",{staticClass:"inline"},[t._v("toFront")]),t._v(").")]),t._v(" "),s("li",[t._v("to designate the id attribute in the remote API call return.\nDefault: "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token double-quoted-string string"},[t._v('"id"')])])])]),t._v(" "),s("h3",{attrs:{id:"setlistiteminlinetemplate-string-template"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setlistiteminlinetemplate-string-template","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setListItemInlineTemplate")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$template")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("h3",{attrs:{id:"setresultiteminlinetemplate-string-template"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setresultiteminlinetemplate-string-template","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setResultItemInlineTemplate")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$template")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("div",{pre:!0},[s("p",[t._v("Just write the template as a string, using placeholders for data like this: "),s("code",{pre:!0,attrs:{class:"inline"}},[s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),s("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("var")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")])]),t._v(".")])]),s("p",[t._v("Example:")]),t._v(" "),s("div",{staticClass:"language-php extra-class"},[s("pre",{pre:!0,attrs:{class:"language-php"}},[s("code",[s("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$panel")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("setInlineTemplate")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),t._v("\n    "),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"Foreground: <strong>{{color}}</strong>"')]),t._v("\n"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n")])])]),s("p",[t._v("The template will be use, depending on the function, to display either the list item (in the result dropdown) or the result item (meaning the valuated form input).")]),t._v(" "),s("p",[t._v("Be aware that you'll need for this to work to pass a valuated object to the Autocomplete, as data.")]),t._v(" "),s("h3",{attrs:{id:"setlistitemtemplatepath-string-listitemtemplatepath"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setlistitemtemplatepath-string-listitemtemplatepath","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setListItemTemplatePath")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$listItemTemplatePath")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("h3",{attrs:{id:"setresultitemtemplatepath-string-resultitemtemplate"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setresultitemtemplatepath-string-resultitemtemplate","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setResultItemTemplatePath")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$resultItemTemplate")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("Use this if you need more control: give the path of a full template, in its own file.")]),t._v(" "),s("p",[t._v("The template will be "),s("a",{attrs:{href:"https://vuejs.org/v2/guide/syntax.html",target:"_blank",rel:"noopener noreferrer"}},[t._v("interpreted by Vue.js"),s("OutboundLink")],1),t._v(", meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. For instance:")]),t._v(" "),s("div",{staticClass:"language-vue extra-class"},[s("pre",{pre:!0,attrs:{class:"language-vue"}},[s("code",[s("span",{pre:!0,attrs:{class:"token tag"}},[s("span",{pre:!0,attrs:{class:"token tag"}},[s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("<")]),t._v("div")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token attr-name"}},[t._v("v-if")]),s("span",{pre:!0,attrs:{class:"token attr-value"}},[s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("=")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v('"')]),t._v("show"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v('"')])]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(">")])]),t._v("result is {{value}}"),s("span",{pre:!0,attrs:{class:"token tag"}},[s("span",{pre:!0,attrs:{class:"token tag"}},[s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("</")]),t._v("div")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(">")])]),t._v("\n"),s("span",{pre:!0,attrs:{class:"token tag"}},[s("span",{pre:!0,attrs:{class:"token tag"}},[s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("<")]),t._v("div")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token attr-name"}},[t._v("v-else")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(">")])]),t._v("result is unknown"),s("span",{pre:!0,attrs:{class:"token tag"}},[s("span",{pre:!0,attrs:{class:"token tag"}},[s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("</")]),t._v("div")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(">")])]),t._v("\n")])])]),s("p",[t._v("The template will be use, depending on the function, to display either the list item (in the result dropdown) or the result item (meaning the valuated form input).")]),t._v(" "),s("p",[t._v("Be aware that you'll need for this to work to pass a valuated object to the Autocomplete, as data.")]),t._v(" "),s("h3",{attrs:{id:"setlocalvalueslinkedto-string-fieldkeys"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setlocalvalueslinkedto-string-fieldkeys","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setLocalValuesLinkedTo")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token punctuation"},[t._v(".")]),s("span",{staticClass:"token punctuation"},[t._v(".")]),s("span",{staticClass:"token punctuation"},[t._v(".")]),s("span",{staticClass:"token variable"},[t._v("$fieldKeys")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("This method is useful to link the dataset of a local autocomplete (aka: the "),s("code",{staticClass:"inline"},[t._v("localValues")]),t._v(") to another form field. Please refer to "),s("router-link",{attrs:{to:"/guide/form-fields/select.html"}},[t._v("the documentation of the select field's "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setOptionsLinkedTo")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),s("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(" method")]),t._v(", which is identical.")],1),t._v(" "),s("h3",{attrs:{id:"setdynamicremoteendpoint-string-dynamicremoteendpoint-array-defaultvalues"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#setdynamicremoteendpoint-string-dynamicremoteendpoint-array-defaultvalues","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setDynamicRemoteEndpoint")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),t._v("string "),s("span",{staticClass:"token variable"},[t._v("$dynamicRemoteEndpoint")]),s("span",{staticClass:"token punctuation"},[t._v(",")]),t._v(" "),s("span",{staticClass:"token keyword"},[t._v("array")]),t._v(" "),s("span",{staticClass:"token variable"},[t._v("$defaultValues")]),s("span",{staticClass:"token punctuation"},[t._v(")")])])]),t._v(" "),s("p",[t._v("In a remote autocomplete case, you can use this method instead of "),s("code",{staticClass:"inline"},[t._v("setRemoteEndpoint")]),t._v(" to handle a dynamic URL, based on another form field. Here's how, for example:")]),t._v(" "),s("div",{staticClass:"language-php extra-class"},[s("pre",{pre:!0,attrs:{class:"language-php"}},[s("code",[t._v("SharpFormAutocompleteField"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("make")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"brand"')]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"remote"')]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("setDynamicRemoteEndpoint")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"/brands/{{country}}"')]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])]),s("p",[t._v("In this example, the "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token punctuation"},[t._v("{")]),s("span",{staticClass:"token punctuation"},[t._v("{")]),t._v("country"),s("span",{staticClass:"token punctuation"},[t._v("}")]),s("span",{staticClass:"token punctuation"},[t._v("}")])]),t._v(" placeholder will be replaced by the value of the "),s("code",{staticClass:"inline"},[t._v("country")]),t._v(" form field. You can define multiple replacements if necessary.")]),t._v(" "),s("p",[t._v("You may need to provide a default value for the endpoint, used when "),s("code",{staticClass:"inline"},[t._v("country")]),t._v(" (in our example) is not valued (without default, the autocomplete field will be displayed as disabled). To do that,\nfill the second argument:")]),t._v(" "),s("div",{staticClass:"language-php extra-class"},[s("pre",{pre:!0,attrs:{class:"language-php"}},[s("code",[t._v("SharpFormAutocompleteField"),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("make")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"model"')]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"remote"')]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("-")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),s("span",{pre:!0,attrs:{class:"token function"}},[t._v("setDynamicRemoteEndpoint")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"/models/{{country}}/{{brand}}"')]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n        "),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"country"')]),t._v(" "),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"france"')]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n        "),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"brand"')]),t._v(" "),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),s("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),s("span",{pre:!0,attrs:{class:"token double-quoted-string string"}},[t._v('"renault"')]),t._v("\n    "),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),s("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n")])])]),s("p",[t._v("The default endpoint would be "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token operator"},[t._v("/")]),t._v("brands"),s("span",{staticClass:"token operator"},[t._v("/")]),t._v("france"),s("span",{staticClass:"token operator"},[t._v("/")]),t._v("renault")]),t._v(".")]),t._v(" "),s("h2",{attrs:{id:"formatter"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#formatter","aria-hidden":"true"}},[t._v("#")]),t._v(" Formatter")]),t._v(" "),s("h3",{attrs:{id:"tofront"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#tofront","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[t._v("toFront")])]),t._v(" "),s("p",[t._v("If "),s("strong",[t._v("mode=local")]),t._v(", you must pass there either:")]),t._v(" "),s("ul",[s("li",[t._v("an single id, since the label will be grabbed from the "),s("code",{staticClass:"inline"},[t._v("localValues")]),t._v(" array,")]),t._v(" "),s("li",[t._v("or an object with an "),s("code",{staticClass:"inline"},[t._v("id")]),t._v(" (or whatever was configure through "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setItemIdAttribute")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),s("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(") property.")])]),t._v(" "),s("p",[t._v("If "),s("strong",[t._v("mode=remote")]),t._v(", you must pass an object with at least an "),s("code",{staticClass:"inline"},[t._v("id")]),t._v(" (or whatever was configure through "),s("code",{staticClass:"inline"},[s("span",{staticClass:"token function"},[t._v("setItemIdAttribute")]),s("span",{staticClass:"token punctuation"},[t._v("(")]),s("span",{staticClass:"token punctuation"},[t._v(")")])]),t._v(") attribute and all attributes needed by the item templates.")]),t._v(" "),s("h3",{attrs:{id:"fromfront"}},[s("a",{staticClass:"header-anchor",attrs:{href:"#fromfront","aria-hidden":"true"}},[t._v("#")]),t._v(" "),s("code",{staticClass:"inline"},[t._v("fromFront")])]),t._v(" "),s("p",[t._v("Returns the selected item id.")])])},[],!1,null,null,null);a.default=n.exports}}]);