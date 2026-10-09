const jwt = require('jsonwebtoken');

/**
 * Authentication middleware.
 * Verifies JWT tokens and attaches user details to req.user context.
 */
async function authenticateMiddleware(req, res, next) {
  try {
    const authHeader = req.headers.authorization;
    let token = '';
    
    if (authHeader && authHeader.startsWith('Bearer ')) {
      token = authHeader.split(' ')[1];
    } else if (req.query.token) {
      token = req.query.token;
    }

    if (!token) {
      return res.status(401).json({
        success: false,
        error: {
          code: 'UNAUTHORIZED',
          message: 'Access token is missing or invalid'
        }
      });
    }

    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'super_secret_key');
    
    // Attach decoded token claims (id, email, mobile, db, role_id, tech_id, c_id) to the request
    // Recheck current company grants on every protected request, including
    // older JWTs issued before tenant identity was preserved on switching.
    req.user = await require('../modules/auth/auth.service').getSession(decoded.mobile || '', decoded.db);
    
    next();
  } catch (error) {
    if (error.status === 403) return next(error);
    if (!error.status && !['JsonWebTokenError', 'TokenExpiredError', 'NotBeforeError'].includes(error.name)) return next(error);
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
