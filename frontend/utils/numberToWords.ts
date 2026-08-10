export function numberToWords(amount: number): string {
  if (amount === 0) return 'Zero Rupees Only';

  const a = [
    '', 'One ', 'Two ', 'Three ', 'Four ', 'Five ', 'Six ', 'Seven ', 'Eight ', 'Nine ', 'Ten ', 'Eleven ', 'Twelve ', 'Thirteen ', 'Fourteen ', 'Fifteen ', 'Sixteen ', 'Seventeen ', 'Eighteen ', 'Nineteen '
  ];
  const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

  const regex = /^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/;

  const getWord = (n: number) => {
    let str = '';
    str += (n != 0) ? (a[Number(n)] || b[Math.floor(n / 10)] + ' ' + a[n % 10]) : '';
    return str;
  };

  const numStr = amount.toFixed(2);
  const [rupeesStr, paiseStr] = numStr.split('.');
  
  let rupees = parseInt(rupeesStr, 10);
  const paise = parseInt(paiseStr, 10);

  if (rupees > 999999999) {
    return 'Amount too large';
  }

  const nStr = ('000000000' + rupees).slice(-9);
  const match = nStr.match(regex);
  if (!match) return '';

  let word = '';
  word += (Number(match[1]) != 0) ? (getWord(Number(match[1])) + 'Crore ') : '';
  word += (Number(match[2]) != 0) ? (getWord(Number(match[2])) + 'Lakh ') : '';
  word += (Number(match[3]) != 0) ? (getWord(Number(match[3])) + 'Thousand ') : '';
  word += (Number(match[4]) != 0) ? (getWord(Number(match[4])) + 'Hundred ') : '';
  word += (Number(match[5]) != 0) ? ((word != '') ? 'and ' : '') + getWord(Number(match[5])) : '';

  word = word.trim();

  if (paise > 0) {
    const paiseWord = getWord(paise).trim();
    word += ` Rupees and ${paiseWord} Paisa Only`;
  } else {
    word += ' Rupees Only';
  }

  return word;
}
