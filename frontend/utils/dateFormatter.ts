import { format, isValid } from 'date-fns';

/**
 * Formats a given date (string, number, or Date object) into DD-MM-YY.
 * Example: '19-08-26'
 * Returns an empty string if the date is invalid or falsy.
 */
export const formatDate = (date: Date | string | number | null | undefined): string => {
  if (!date) return '';
  const d = new Date(date);
  if (!isValid(d)) return '';
  return format(d, 'dd-MM-yy');
};
