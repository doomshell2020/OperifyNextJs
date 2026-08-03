const jwt = require('jsonwebtoken');

/**
 * Authentication middleware.
 * Verifies JWT tokens and attaches user details to req.user context.
 */
function authenticateMiddleware(req, res, next) {
  try {
    const authHeader = req.headers.authorization;
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return res.status(401).json({
        success: false,
        error: {
          code: 'UNAUTHORIZED',
          message: 'Access token is missing or invalid'
        }
      });
    }

    const token = authHeader.split(' ')[1];
    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'super_secret_key');
    
    // Attach decoded token claims (id, email, mobile, db, role_id, tech_id, c_id) to the request
    req.user = decoded;
    
    next();
  } catch (error) {
    return res.status(401).json({
      success: false,
      error: {
        code: 'UNAUTHORIZED',
        message: 'Invalid or expired access token',
        details: error.message
      }
    });
  }
}

module.exports = authenticateMiddleware;
