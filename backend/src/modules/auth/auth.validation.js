const { body, validationResult } = require('express-validator');

// Validation schemas for auth routes
const loginSchema = [
  body('mobile')
    .notEmpty().withMessage('Mobile number is required')
    .isString().withMessage('Mobile number must be a string'),
  body('password')
    .notEmpty().withMessage('Password is required')
];

const refreshSchema = [
  body('refreshToken')
    .notEmpty().withMessage('Refresh token is required')
];

// Middleware to capture express-validation errors
const validate = (req, res, next) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({
      success: false,
      error: {
        code: 'VALIDATION_ERROR',
        message: 'Invalid request inputs',
        details: errors.array().map(e => ({ field: e.path, error: e.msg }))
      }
    });
  }
  next();
};

module.exports = {
  loginSchema,
  refreshSchema,
  validate
};
