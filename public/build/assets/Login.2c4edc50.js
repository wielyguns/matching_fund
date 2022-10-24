import{c as h,w as k,N as w,u as s,b as n,d as b,O as x,K as y,i as c,h as m,g as a,H as v,t as V,k as u,e as r,L as $,j as p,n as B,p as C}from"./app.00632227.js";import{_ as L}from"./Button.d08a3154.js";import{_ as N}from"./Guest.cb6c4c6d.js";import{_ as f}from"./Input.eac4865a.js";import{_}from"./Label.d7ec341a.js";import{_ as R}from"./ValidationErrors.5f9fa91b.js";import"./ApplicationLogo.f79a693a.js";import"./plugin-vue_export-helper.21dcd24c.js";const S=["value"],U={__name:"Checkbox",props:{checked:{type:[Array,Boolean],default:!1},value:{default:null}},emits:["update:checked"],setup(l,{emit:e}){const d=l,i=h({get(){return d.checked},set(t){e("update:checked",t)}});return(t,o)=>k((n(),b("input",{type:"checkbox",value:l.value,"onUpdate:modelValue":o[0]||(o[0]=g=>x(i)?i.value=g:null),class:"rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"},null,8,S)),[[w,s(i)]])}},q={key:0,class:"mb-4 font-medium text-sm text-green-600"},F=["onSubmit"],P={class:"mt-4"},j={class:"block mt-4"},D={class:"flex items-center"},E=r("span",{class:"ml-2 text-sm text-gray-600"},"Remember me",-1),H={class:"flex items-center justify-end mt-4"},J={__name:"Login",props:{canResetPassword:Boolean,status:String},setup(l){const e=y({email:"",password:"",remember:!1}),d=()=>{e.post(route("login"),{onFinish:()=>e.reset("password")})};return(i,t)=>(n(),c(N,null,{default:m(()=>[a(s(v),{title:"Log in"}),a(R,{class:"mb-4"}),l.status?(n(),b("div",q,V(l.status),1)):u("",!0),r("form",{onSubmit:C(d,["prevent"])},[r("div",null,[a(_,{for:"email",value:"Email"}),a(f,{id:"email",type:"email",class:"mt-1 block w-full",modelValue:s(e).email,"onUpdate:modelValue":t[0]||(t[0]=o=>s(e).email=o),required:"",autofocus:"",autocomplete:"username"},null,8,["modelValue"])]),r("div",P,[a(_,{for:"password",value:"Password"}),a(f,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:s(e).password,"onUpdate:modelValue":t[1]||(t[1]=o=>s(e).password=o),required:"",autocomplete:"current-password"},null,8,["modelValue"])]),r("div",j,[r("label",D,[a(U,{name:"remember",checked:s(e).remember,"onUpdate:checked":t[2]||(t[2]=o=>s(e).remember=o)},null,8,["checked"]),E])]),r("div",H,[l.canResetPassword?(n(),c(s($),{key:0,href:i.route("password.request"),class:"underline text-sm text-gray-600 hover:text-gray-900"},{default:m(()=>[p(" Forgot your password? ")]),_:1},8,["href"])):u("",!0),a(L,{class:B(["ml-4",{"opacity-25":s(e).processing}]),disabled:s(e).processing},{default:m(()=>[p(" Log in ")]),_:1},8,["class","disabled"])])],40,F)]),_:1}))}};export{J as default};
