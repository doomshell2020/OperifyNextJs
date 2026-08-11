export const formatQty = (value: number | string | undefined | null): string => {
  if (value === undefined || value === null || value === '') return '0.000';
  const num = Number(value);
  return isNaN(num) ? '0.000' : num.toFixed(3);
};

export const formatAmt = (value: number | string | undefined | null): string => {
  if (value === undefined || value === null || value === '') return '0.00';
  const num = Number(value);
  return isNaN(num) ? '0.00' : num.toFixed(2);
};
