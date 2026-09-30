const fs = require('fs');
const path = require('path');

const appJsPath = path.join(__dirname, 'src', 'app.js');
let content = fs.readFileSync(appJsPath, 'utf8');

if (!content.includes('jobChallanRoutes')) {
  content = content.replace(
    "const stockRegisterRoutes = require('./modules/stockRegister/stockRegister.routes');",
    "const stockRegisterRoutes = require('./modules/stockRegister/stockRegister.routes');\nconst jobChallanRoutes = require('./modules/jobChallan/jobChallan.routes');"
  );
  
  content = content.replace(
    "app.use('/api/stock-register', stockRegisterRoutes);",
    "app.use('/api/stock-register', stockRegisterRoutes);\napp.use('/api/job-challan', jobChallanRoutes);"
  );
  
  fs.writeFileSync(appJsPath, content);
  console.log('app.js updated');
} else {
  console.log('Already updated');
}
