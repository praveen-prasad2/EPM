/* Only reviewed subject images may be assigned to card backgrounds.
 * Select relevant photographs without repeating an image within a page.
 * Run from any directory with: node deploy/update-card-backgrounds.cjs
 */
const fs = require('fs');
const path = require('path');
const base = path.resolve(__dirname, '../app/public/wp-content/mu-plugins');
const pages = require('./card-background-pages.json');
const uniquePhotos = require('./select-unique-card-photo.cjs');
const map = JSON.parse(fs.readFileSync(path.join(base, 'epm-sitewide-images.json')));
// Rendering wrappers restore unintended transparency/white haze without
// changing the source photos or applying filters to the card's text.
const clearPhotos = new Set(['f0344d552c29', '07c4e7cb2f0d', '1bff23d8324f', '67fda15a94b9', 'a9acae8aec57', 'b01c04497a0d']);
const photo = name => `epm-light-assets/features/${name}${clearPhotos.has(name) ? '-clear.svg' : '.webp'}`;
const industry = name => `epm-light-assets/industries/${name}-opaque.svg`;
const catalog = {
  access: photo('access-control'), mobile: photo('fa2eb209407f'), support: photo('a9acae8aec57'),
  authentication: 'epm-light-assets/photography/pexels-17155842.jpg', encryption: 'epm-light-assets/photography/pexels-943596.jpg',
  face: photo('b01c04497a0d'), iris: photo('af4bd37f6c0b'),
  servers: photo('d1aac1e81455'), analytics: photo('reporting-dashboards'),
  surveillance: photo('video-analytics'), security: photo('security-operations'),
  alerts: photo('incident-alerts'), safety: photo('workplace-safety'),
  occupancy: photo('occupancy-movement'), parking: photo('smart-parking'),
  ports: photo('ports'), industrial: photo('6f2dd952675b'),
  inspection: photo('8d7360141f91'), manufacturing: photo('70af25ead453'),
  radiology: photo('528a24cea0ac'), medical: photo('d462392c7e0f'),
  office: industry('enterprise'), government: industry('government'),
  aviation: industry('transport-aviation'), energy: industry('construction-oil-gas'),
  hospitality: industry('hospitality-tourism'), defense: industry('police-military'),
  education: industry('education'), healthcare: industry('healthcare'), banking: industry('banking'),
  buildings: photo('ff0b7f3667c0'), visitor: photo('f8e54aae3384'),
  feedback: photo('07c4e7cb2f0d'), kiosk: photo('67fda15a94b9'),
  assets: photo('1bff23d8324f'), iot: photo('f0344d552c29'),
  gates: photo('0218c3b7c7f5'), keys: photo('1d2c75378b5e'),
  lockers: photo('59d07765c9cf'), documents: photo('2c85f1387eff'),
  printer: photo('3be329e17153'), printer210: photo('21fa2d0cdf11'),
  cards: photo('2e446f7897ba'),
};
const rules = [
  [/^(corporate offices|enterprise environments|built for enterprise|workspace$|3 branches)/i, 'office'],
  [/^(government|100\+ government)/i, 'government'],
  [/^(police|military|defense)/i, 'defense'],
  [/^(healthcare|hospitals?|patient)/i, 'healthcare'],
  [/^(data cent(?:er|re)s?|cloud|server|massive memory|storage|scalab|infrastructure)/i, 'servers'],
  [/^(banking|financial|bill payment)/i, 'banking'],
  [/^(education|learning|campus|school)/i, 'education'],
  [/^(hospitality|hotel|make dining)/i, 'hospitality'],
  [/^(aerospace|aviation|transportation|ticketing|train station)/i, 'aviation'],
  [/^(shipping|ports?\b)/i, 'ports'],
  [/^(oil|construction|feed water|condensers|coolers|evaporators|chillers)/i, 'energy'],
  [/^(manufacturing|industrial solutions)/i, 'manufacturing'],
  [/^(smart buildings|multi.location management)/i, 'buildings'],
  [/^(reduced? (it |operational )?cost|cost savings|enjoy a lower cost|increases? sales|productivity|improves productivity)/i, 'analytics'],
  [/^nfc\b/i, 'mobile'],
  [/^rfid\b/i, 'assets'],
  [/^(felica|mifare|desfire|hid iclass|iso 14443|contact\s+(?:&\s*)?contactless|contactless|smart contactless|access control readers|smart card readers|credential technology|multi.band rf)/i, 'access'],
  [/^(mobile|google passkeys|microsoft passkeys|qr code|enables secure access with a mobile|enhances the transnational)/i, 'mobile'],
  [/^(fido2|webauthn|passkeys|passwordless|enterprise login|windows authentication)/i, 'encryption'],
  [/^(fingerprint|anti.spoof|high.speed fingerprint|multi.factor|faster authentication|stronger security|future.ready)/i, 'authentication'],
  [/^(pki|otp|secure encrypt|encrypted|mutual authentication|end.to.end encrypt|vpn|high.level security|privacy|secure reading)/i, 'encryption'],
  [/^(face|high speed face|all lighting|high quality face|infra.red based|integrated high resolution face)/i, 'face'],
  [/^(iris|non.contact|easy non.intrusive)/i, 'iris'],
  [/^(report|audit|saved report|csv|custom dashboard|data centric|visual dashboard|real.time display|real time 3.d|real.time.*dashboard|workspace analytics)/i, 'analytics'],
  [/^(alert|notification|instant.*notification|smart alerts|automated incident)/i, 'alerts'],
  [/^(occupancy|people|real.time people|entry & exit|heatmaps|ai.based crowd|desk booking|room booking|hybrid space)/i, 'occupancy'],
  [/^(parking|vehicle|automatic.*plate|simultaneous vehicle|electronic toll|object speed)/i, 'parking'],
  [/^(workplace safety|enhance workplace|safety|ip\d|industrial strength)/i, 'safety'],
  [/^(ai solutions|ai.powered video|video surveillance|dome cameras|ptz cameras|bullet camera|real.time anomaly|multi.site centralized|integration with existing cctv|vms)/i, 'surveillance'],
  [/^(security inspection|detects contraband|complete head.to.toe|organic$|inorganic$|metal$)/i, 'inspection'],
  [/^(health imaging|medical|radiology)/i, 'radiology'],
  [/^(asset management|real.time key tracking|real.time assets)/i, 'assets'],
  [/^(iot|ble|connectivity|communication|multi communication|network)/i, 'iot'],
  [/^(e gate|speed gate|swing gate|tripod|full.height|easygate|sweeper|speedblade|jetpan|xgate|gate-|onyx|bastion|centurion|twix|rexon|sesame|bicyclone|glassgo)/i, 'gates'],
  [/^(smart key|register keys|assign access|keys placed|each key|a key fob)/i, 'keys'],
  [/^(folders with documents|sealers|weapons|money boxes|hard drives|post and letters|other important)/i, 'lockers'],
  [/^(customer service|customer engagement|collection of customer feedback|improves overall consumer|improves the relationship)/i, 'feedback'],
  [/^(public information|attention grabbing|in.store entertainment|small form factor|create an|interactive merchandise|benefits of touch|have fun)/i, 'kiosk'],
  [/^(visitor|advance booking|badges with|visit expiration)/i, 'visitor'],
  [/^(secure card issuance|cards issued|50,000,000)/i, 'printer'],
];
function pageTopic(title) {
  const t = title.toLowerCase();
  if (/medical|radiology|pacs|health imaging/.test(t)) return 'radiology';
  if (/ndt|film solutions|digital imaging|directional|water cooled|control evo|industrial|accessories/.test(t)) return 'inspection';
  if (/drug|explosive|x-ray|hidden people|security inspection/.test(t)) return 'inspection';
  if (/vehicle key|parking|anpr|long range/.test(t)) return 'parking';
  if (/locker/.test(t)) return 'lockers';
  if (/key/.test(t)) return /software/.test(t) ? 'analytics' : 'keys';
  if (/gate|turnstile/.test(t)) return 'gates';
  if (/face recognition/.test(t)) return 'face';
  if (/iris/.test(t)) return 'iris';
  if (/fingerprint|optical biometric|multispectral|invixium|suprema|idemia|attendance/.test(t)) return 'authentication';
  if (/mobile virtual|mobile pda|qr code/.test(t)) return 'mobile';
  if (/rfid|smart.*card|wireless reader|workspace|access control/.test(t)) return 'access';
  if (/video server|network video|cloud/.test(t)) return 'servers';
  if (/camera|video|hikvision|dahua/.test(t)) return 'surveillance';
  if (/people|occupancy/.test(t)) return 'occupancy';
  if (/asset|nedap/.test(t)) return 'assets';
  if (/iot|ble/.test(t)) return 'iot';
  if (/visitor/.test(t)) return 'visitor';
  if (/webta|time.*space|spica/.test(t)) return 'analytics';
  if (/self service software/.test(t)) return 'support';
  if (/touch screen|kiosk|queue/.test(t)) return 'kiosk';
  if (/customer|survey/.test(t)) return 'feedback';
  if (/mc 210/.test(t)) return 'printer210';
  if (/mc\s?\d|xid|printer|card issuance|laminat|pre printed/.test(t)) return 'printer';
  if (/hologram|embosser|certificates|papers/.test(t)) return 'documents';
  if (/ai/.test(t)) return 'security';
  return 'office';
}
const report = [];
const homeIndustries = {
  '828d847': 'government', 'a9e81d6': 'transport-aviation', '60f8d70': 'construction-oil-gas',
  'f5521d1': 'enterprise', 'ac862d4': 'hospitality-tourism', 'e452e90': 'police-military',
  'e4ead11': 'education', 'a4ffecb': 'healthcare', '3f5951a': 'banking',
};
for (const [page, entries] of Object.entries(map)) {
  if (!pages[page]) throw Error(`Review the page subject before assigning backgrounds: ${page}`);
  const used = new Set();
  // Reserve fixed homepage industry scenes before choosing other backgrounds.
  if (page === '5') for (const [id, entry] of Object.entries(entries)) {
    if (homeIndustries[id]) used.add(uniquePhotos.identity(industry(homeIndustries[id])));
    else if (/^(Our Vision|Our Mission)/.test(entry.text)) used.add(uniquePhotos.identity(entry.asset));
  }
  for (const [id, entry] of Object.entries(entries)) {
    // Keep the nine reviewed industry images and the Vision/Mission CSS patterns.
    if (page === '5' && homeIndustries[id]) {
      entry.asset = industry(homeIndustries[id]);
      if (!fs.existsSync(path.join(base, entry.asset))) throw Error(`Missing industry image: ${entry.asset}`);
      continue;
    }
    if (page === '5' && /^(Our Vision|Our Mission)/.test(entry.text)) continue;
    const text = entry.text.replace(/&amp;|&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
    let topic = rules.find(([regex]) => regex.test(text))?.[1];
    const reason = topic ? 'card subject' : 'page subject';
    if (!topic) topic = pageTopic(pages[page] || '');
    const asset = uniquePhotos.select({topic, pageTopic:pageTopic(pages[page]), text, page, id, preferred:catalog[topic], used});
    used.add(uniquePhotos.identity(asset));
    // Explicit import mode is used only while preparing new, reviewed stock assets.
    if (!fs.existsSync(path.join(base,asset)) && process.argv.includes('--import-photos')) {
      const stockId = asset.match(/pexels-(\d+)\.jpg$/)?.[1];
      const source = path.resolve(__dirname, '../temp/stock-photos', stockId+'.jpg');
      if (stockId && fs.existsSync(source)) {
        fs.mkdirSync(path.dirname(path.join(base,asset)),{recursive:true});
        fs.copyFileSync(source,path.join(base,asset));
      }
    }
    if (!asset || !fs.existsSync(path.join(base, asset))) throw Error(`Missing reviewed image: ${topic} ${asset}`);
    report.push({ page, id, title: pages[page], text: entry.text, previous: entry.asset, asset, topic, reason });
    entry.asset = asset;
  }
}
// Fail before publishing either map when a duplicate would be introduced.
for (const [page, entries] of Object.entries(map)) {
  const seen = new Set();
  for (const [id, entry] of Object.entries(entries)) {
    if (page === '5' && /^(Our Vision|Our Mission)/.test(entry.text)) continue;
    const key = uniquePhotos.identity(entry.asset);
    if (seen.has(key)) throw Error(`Repeated photograph on page ${page}: ${id} ${entry.asset}`);
    seen.add(key);
  }
}
const rulesCss = [];
for (const [page, entries] of Object.entries(map)) for (const [id, entry] of Object.entries(entries)) {
  rulesCss.push(`body.epm-sitewide .elementor.elementor.elementor[data-elementor-type="wp-page"][data-elementor-id="${page}"] .elementor-element.elementor-element-${id}{--epm-card-photo:url("${entry.asset}");}`);
}
fs.writeFileSync(path.join(base, 'epm-sitewide-images.json'), JSON.stringify(map, null, 2));
fs.writeFileSync(path.join(base, 'epm-sitewide-images.css'), rulesCss.join('\n') + '\n');
fs.mkdirSync(path.resolve(__dirname, '../temp'), { recursive: true });
fs.writeFileSync(path.resolve(__dirname, '../temp/card-background-audit.json'), JSON.stringify(report, null, 2));
console.log(JSON.stringify({ pages: Object.keys(map).length, reviewed: report.length, changed: report.filter(r => r.previous !== r.asset).length, byCard: report.filter(r => r.reason === 'card subject').length }));
