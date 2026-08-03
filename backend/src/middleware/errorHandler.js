/**
 * Centralized global error handling middleware.
 * Standardizes backend error formats.
 */
function errorHandlerMiddleware(err, req, res, next) {
  console.error('Error occurred in request:', err);

  const statusCode = err.status || 500;
  const errCode = err.code || 'INTERNAL_SERVER_ERROR';
  const message = err.message || 'An unexpected error occurred on the server';
  const details = err.details || null;

  return res.status(statusCode).json({
    success: false,
    error: {
      code: errCode,
      message,
      ...(details && { details })
    }
  });
}

module.exports = errorHandlerMiddleware;
