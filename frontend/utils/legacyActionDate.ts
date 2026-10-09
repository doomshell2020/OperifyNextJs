/** PHP bootstrap.php uses Asia/Kolkata for its same-day action restriction. */
export function isLegacyToday(value: string | Date | null | undefined, now = new Date()): boolean {
  if (!value) return false;
  const date = value instanceof Date ? value.toISOString().slice(0, 10) : String(value).slice(0, 10);
  return date === new Intl.DateTimeFormat('en-CA', {
    timeZone: 'Asia/Kolkata', year: 'numeric', month: '2-digit', day: '2-digit',
  }).format(now);
}
