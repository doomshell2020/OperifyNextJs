const fs = require('fs');

const buf = fs.readFileSync('test-90.pdf');
console.log('Size:', buf.length);
console.log('Starts with:', buf.toString('utf8', 0, 5));
console.log('Ends with:', buf.toString('utf8', buf.length - 6));
