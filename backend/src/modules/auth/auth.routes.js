const express = require('express');
const authController = require('./auth.controller');
const { loginSchema, refreshSchema, validate } = require('./auth.validation');
const authenticate = require('../../middleware/auth');

const router = express.Router();

// Public routes
router.post('/login', loginSchema, validate, authController.login);
router.post('/refresh', refreshSchema, validate, authController.refresh);
router.post('/logout', authController.logout);

// Protected routes
router.get('/me', authenticate, authController.me);

module.exports = router;
