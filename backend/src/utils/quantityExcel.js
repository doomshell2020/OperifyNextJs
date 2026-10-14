const QUANTITY_NUMBER_FORMAT = '0.000';

// Keep quantities numeric and preserve their stored precision; Excel's number
// format controls display without changing values used by formulas.
function formatQuantityColumns(sheet, columns) {
  for (const column of columns) sheet.getColumn(column).numFmt = QUANTITY_NUMBER_FORMAT;
}

module.exports = { QUANTITY_NUMBER_FORMAT, formatQuantityColumns };
