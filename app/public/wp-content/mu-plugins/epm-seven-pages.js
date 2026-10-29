/* Layout-only corrections for the seven explicitly selected pages. */
(() => {
 const root=document.querySelector('body.epm-seven-pages .elementor[data-elementor-type="wp-page"]');
 if(!root)return;
 const page=root.dataset.elementorId;
 const byId=id=>root.querySelector('.elementor-element-'+id);
 // Keep all six benefits in one flex layout; saved page content is unchanged.
 const split={2454:['5dcb84ef','24ce6b97'],2464:['7ff0030','1c094994'],2466:['16bb995f',null]}[page];
 if(split){
   const tail=byId(split[0]);
   const destination=split[1]?byId(split[1]):tail?.previousElementSibling;
   if(tail&&destination&&destination.classList.contains('epm-feature-grid')){
     tail.querySelectorAll('.epm-seven-card').forEach(card=>destination.append(card));
     tail.hidden=true;tail.style.setProperty('display','none','important');
   }
 }
 // Restore original illustrations as real content images on white surfaces.
 const images={
  2925:{'985c021':['/wp-content/uploads/2026/08/Group-1000006380.webp','AEOS access control capabilities']},
  3196:{'e48b1c5':['/wp-content/uploads/2026/07/multi-device-management-software-1.webp','IXM WEB centralized device management']}
 }[page]||{};
 for(const [id,[src,alt]]of Object.entries(images)){
   const section=byId(id);if(!section||section.querySelector('.epm-seven-restored-image'))continue;
   const image=document.createElement('img');image.src=src;image.alt=alt;
   image.className='epm-seven-restored-image';image.loading='lazy';image.decoding='async';
   section.append(image);
 }
 // Let prose flow around the illustration, then use the full section width.
 const wrapSections={
  2925:['4cca7ad5','985c021','0a051ba','48c0fb0','ff06432','66a4e53'],
  3196:['e48b1c5','a03d11e'],
  2883:['c7712c7','97e564e','dc9ca01','e57a623']
 }[page]||[];
 for(const id of wrapSections){
   const section=byId(id);if(!section)continue;
   const children=Array.from(section.children);
   const media=children.find(el=>!el.textContent.trim() &&
     (el.matches('img,.epm-seven-media')||el.querySelector('img,.epm-seven-media')));
   if(!media)continue;
   section.classList.add('epm-prose-wrap');
   media.classList.add('epm-prose-media');
   if(children.indexOf(media)>0)section.classList.add('epm-prose-image-right');
   // Floats must precede the text they wrap. Move, never duplicate, content.
   section.prepend(media);
   for(const child of children.filter(el=>el!==media)){
     child.classList.add('epm-prose-text');
     child.querySelectorAll('.e-con,.e-con-inner,.elementor-widget,.elementor-widget-container').forEach(el=>el.classList.add('epm-prose-text'));
   }
 }
})();
