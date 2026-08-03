const fs = require('fs');
const content = fs.readFileSync('tirupati_tppl.sql', 'utf8');

const regex1 = /CREATE TABLE `designsheet` \([\s\S]*?;\n/g;
const match1 = content.match(regex1);
if (match1) console.log(match1[0]);

const regex2 = /CREATE TABLE `designsheetdetails` \([\s\S]*?;\n/g;
const match2 = content.match(regex2);
if (match2) console.log(match2[0]);
