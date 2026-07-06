var e=`embodied-cart`;function t(){try{return JSON.parse(localStorage.getItem(e))??[]}catch{return[]}}function n(t){localStorage.setItem(e,JSON.stringify(t)),document.dispatchEvent(new CustomEvent(`cart:updated`))}function r({name:e,priceValue:r,gradient:i,size:a}){let o=t(),c=o.find(t=>t.name===e&&t.size===a);c?c.qty+=1:o.push({name:e,priceValue:r,gradient:i,size:a,qty:1}),n(o),s()}function i(e,r){let i=t(),a=i[e];a&&(a.qty+=r,a.qty<=0&&i.splice(e,1),n(i))}function a(e){return`Rp `+e.toLocaleString(`id-ID`)}function o(){let e=document.querySelector(`[data-cart-list]`);if(!e)return;let n=t(),r=document.querySelector(`[data-cart-empty]`),i=document.querySelector(`[data-cart-subtotal]`);e.innerHTML=``,r?.classList.toggle(`hidden`,n.length>0);let o=0,s=0;n.forEach((t,n)=>{o+=t.priceValue*t.qty,s+=t.qty;let r=document.createElement(`div`);r.className=`flex gap-4 border-b border-neutral-200 px-6 py-6`,r.innerHTML=`
            <div class="h-20 w-20 shrink-0 overflow-hidden bg-neutral-100">
                <div class="h-full w-full bg-linear-to-br ${t.gradient}"></div>
            </div>
            <div class="flex flex-1 flex-col">
                <p class="text-sm font-medium">${t.name}</p>
                <p class="mb-3 text-xs text-neutral-500">Size: ${t.size}</p>
                <div class="mt-auto flex items-center justify-between">
                    <div class="flex items-center gap-3 border border-neutral-300 px-3 py-1">
                        <button type="button" class="text-neutral-500 hover:text-neutral-900" data-qty-decrease="${n}">&minus;</button>
                        <span class="w-4 text-center text-sm">${t.qty}</span>
                        <button type="button" class="text-neutral-500 hover:text-neutral-900" data-qty-increase="${n}">&plus;</button>
                    </div>
                    <span class="text-sm">${a(t.priceValue*t.qty)}</span>
                </div>
            </div>
        `,e.appendChild(r)}),i&&(i.textContent=a(o)),document.querySelectorAll(`[data-cart-count]`).forEach(e=>{e.textContent=s})}function s(){document.querySelector(`[data-cart-drawer]`)?.classList.remove(`translate-x-full`),document.querySelector(`[data-cart-overlay]`)?.classList.remove(`hidden`)}function c(){document.querySelector(`[data-cart-drawer]`)?.classList.add(`translate-x-full`),document.querySelector(`[data-cart-overlay]`)?.classList.add(`hidden`)}document.addEventListener(`DOMContentLoaded`,o),document.addEventListener(`cart:updated`,o),document.addEventListener(`click`,e=>{let t=e.target.closest(`[data-add-to-cart]`);if(t){let e=t.closest(`[data-product-form]`)?.querySelector(`input[name="size"]:checked`);r({name:t.dataset.name,priceValue:Number(t.dataset.priceValue),gradient:t.dataset.gradient,size:e?.value??t.dataset.size});return}if(e.target.closest(`[data-cart-open]`)){s();return}if(e.target.closest(`[data-cart-close]`)){c();return}let n=e.target.closest(`[data-qty-decrease]`);if(n){i(Number(n.dataset.qtyDecrease),-1);return}let a=e.target.closest(`[data-qty-increase]`);a&&i(Number(a.dataset.qtyIncrease),1)});