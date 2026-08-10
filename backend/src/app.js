const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const compression = require('compression');
const cookieParser = require('cookie-parser');
require('dotenv').config();

const authRoutes = require('./modules/auth/auth.routes');
const dashboardRoutes = require('./modules/dashboard/dashboard.routes');
const purchaseOrderRoutes = require('./modules/purchaseOrder/purchaseOrder.routes');
const vendorRoutes = require('./modules/vendor/vendor.routes');
const contractRoutes = require('./modules/contract/contract.routes');
const emdRoutes = require('./modules/emd/emd.routes');
const paymentRoutes = require('./modules/payment/payment.routes');
const quotationRoutes = require('./modules/quotation/quotation.routes');
const settingsRoutes = require('./modules/settings/settings.routes');
const indentRoutes = require('./modules/indent/indent.routes');
const indentpoRoutes = require('./modules/indentpo/indentpo.routes');
const designsheetRoutes = require('./modules/designsheet/designsheet.routes');
const grnInspectionRoutes = require('./modules/grnInspection/grnInspection.routes');
const grnRoutes = require('./modules/grn/grn.routes');
const tenantMiddleware = require('./middleware/tenant');
const errorHandler = require('./middleware/errorHandler');

const app = express();

// Standard Security & Utility Middlewares
app.use(helmet({
  crossOriginResourcePolicy: { policy: "cross-origin" }
}));
app.use(cors({
  origin: '*', // Adjust origin rules in production
  credentials: true
}));
app.use(morgan('dev'));
app.use(compression());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());
// Static Files (for uploads like logos)
const path = require('path');
app.use('/public', express.static(path.join(__dirname, '../public')));

// API Routes
app.use('/api/auth', authRoutes);
app.use('/api/dashboard', dashboardRoutes);
app.use('/api/purchase-orders', purchaseOrderRoutes);
app.use('/api/vendors', vendorRoutes);
app.use('/api/contracts', contractRoutes);
app.use('/api/emd', emdRoutes);
app.use('/api/payments', paymentRoutes);
app.use('/api/quotations', quotationRoutes);
app.use('/api/settings', settingsRoutes);
app.use('/api/indents', indentRoutes);
app.use('/api/indentpo', indentpoRoutes);
app.use('/api/designsheets', designsheetRoutes);
app.use('/api/grn-inspection', grnInspectionRoutes);
app.use('/api/grn', grnRoutes);

// Health check endpoint
app.get('/health', (req, res) => {
  res.status(200).json({
    status: 'healthy',
    timestamp: new Date()
  });
});

// Centralized error boundary
app.use(errorHandler);

module.exports = app;
