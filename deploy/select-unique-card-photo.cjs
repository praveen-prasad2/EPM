const crypto = require('crypto');
const library = require('./card-photo-library.json');

// Candidate pools stay within the subject area, even on long specification pages.
const pools = {
  access: ['access control','door lock','credit card','electronics','circuit board'],
  authentication: ['access control','door lock','credit card','computer','smartphone','electronics'],
  encryption: ['computer','access control','server','circuit board','network cables','door lock'],
  mobile: ['smartphone','access control','credit card','computer'],
  face: ['access control','security camera','electronics','computer'],
  iris: ['access control','security camera','electronics'],
  servers: ['server','network cables','computer','electronics','circuit board'],
  analytics: ['computer','office','customer service','server','engineering'],
  surveillance: ['security camera','server','network cables','access control','electronics'],
  security: ['security camera','access control','server','door lock','network cables'],
  alerts: ['smartphone','security camera','access control','server'],
  safety: ['engineering','manufacturing','factory','metalworking','welding'],
  occupancy: ['office','modern building','access control','parking','warehouse'],
  parking: ['parking','access control','security camera','logistics','electronics'],
  ports: ['logistics','warehouse','industrial pipes','factory'],
  industrial: ['industrial machines','manufacturing','industrial pipes','metalworking','welding','engineering','factory','electronics'],
  inspection: ['industrial machines','metalworking','engineering','manufacturing','industrial pipes','welding','electronics','circuit board','factory','computer','server','network cables'],
  manufacturing: ['manufacturing','industrial machines','metalworking','engineering','factory'],
  radiology: ['x ray','hospital','laboratory','computer'],
  medical: ['x ray','laboratory','hospital'],
  office: ['office','modern building','customer service','computer'],
  government: ['modern building','office','access control'],
  aviation: ['access control','logistics','engineering'],
  energy: ['industrial pipes','factory','manufacturing','engineering'],
  hospitality: ['customer service','modern building','office'],
  defense: ['security camera','access control','server'],
  education: ['computer','laboratory','office'],
  healthcare: ['hospital','x ray','laboratory'],
  banking: ['credit card','office','server','access control'],
  buildings: ['modern building','office','access control'],
  visitor: ['access control','customer service','office','modern building'],
  feedback: ['customer service','smartphone','computer','office','credit card'],
  kiosk: ['credit card','smartphone','access control','computer','customer service'],
  assets: ['warehouse','logistics','electronics','access control','manufacturing'],
  iot: ['circuit board','electronics','network cables','server','manufacturing'],
  gates: ['access control','modern building','door lock','parking','warehouse','security camera'],
  keys: ['door lock','access control','office','warehouse'],
  lockers: ['door lock','warehouse','access control','office'],
  documents: ['printing machine','office','credit card','computer'],
  printer: ['printing machine','credit card','electronics','computer'],
  printer210: ['printing machine','credit card','electronics','computer'],
  cards: ['credit card','access control','electronics'],
  support: ['customer service','computer','office','smartphone'],
};
const specialSubjects = [
  [/fingerprint|biometric|anti.spoof/i, ['17155842','37838043','13657444','13657375','13657523']],
  [/\bnfc\b|contactless|felica|mifare|desfire|iso 14443/i, ['13657444','13657375','11412587','5239818','5239803','7621126','17155842']],
  [/mobile|smartphone|tablet|passkey/i, ['33335189','12368095','3945695','3945672','33797540','36680543']],
  [/turnstile|speed.?gate|swing.?gate|sweeper|easygate/i, ['5225436','7251096','8554382','13007854','13007861','13657523','30829651']],
  [/report|dashboard|cost|productiv|software|workflow|interface|windows/i, ['19895882','943596','37605911','5092815','5530447','7750129']],
];
function tie(id, card) {
  return parseInt(crypto.createHash('sha256').update(card + ':' + id).digest('hex').slice(0,8),16) / 0xffffffff;
}
function identity(asset) {
  return asset.replace(/-opaque\.svg$/,'.png').replace(/-clear\.svg$/,'.webp');
}
function select({topic, pageTopic, text, page, id, preferred, used}) {
  // A reviewed existing photo may appear once per page, never twice.
  if (preferred && !used.has(identity(preferred))) return preferred;
  const primary = pools[topic] || [];
  const context = pools[pageTopic] || [];
  const special = specialSubjects.filter(([re])=>re.test(text.slice(0,130))).flatMap(([,ids])=>ids);
  const candidates = library.filter(photo=>!used.has(identity(photo.asset))).map(photo=>{
    const matches = photo.topics.map(t=>primary.indexOf(t)).filter(n=>n>=0);
    const contextMatches = photo.topics.map(t=>context.indexOf(t)).filter(n=>n>=0);
    const rank = matches.length ? 100-Math.min(...matches)*7 : contextMatches.length ? 35-Math.min(...contextMatches)*2 : -100;
    return {photo,score:rank+(special.includes(photo.id)?75:0)+tie(photo.id,page+':'+id)*3};
  }).filter(c=>c.score>0).sort((a,b)=>b.score-a.score);
  if (!candidates.length) throw Error(`No distinct relevant photograph remains for page ${page}, card ${id}: ${text}`);
  return candidates[0].photo.asset;
}
module.exports = {select,identity,library};
