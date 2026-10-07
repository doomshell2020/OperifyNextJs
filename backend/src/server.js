const app = require('./app');
const { PORT, BACKEND_URL } = require('./config/environment');

const server = app.listen(PORT, () => {
  console.log(`=========================================`);
  console.log(`  Operify ERP Backend Server Running     `);
  console.log(`  Port: ${PORT}                          `);
  console.log(`  Backend URL: ${BACKEND_URL}`);
  console.log(`  Environment: ${process.env.NODE_ENV || 'development'}`);
  console.log(`=========================================`);
});

// Graceful shutdown handling
process.on('SIGTERM', () => {
  console.log('SIGTERM signal received: closing HTTP server');
  server.close(() => {
    console.log('HTTP server closed');
  });
});
