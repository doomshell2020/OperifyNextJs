import React from 'react';
import { Page, Text, View, Document, StyleSheet, Image } from '@react-pdf/renderer';
import { numberToWords } from '@/utils/numberToWords';
import { formatQty, formatAmt } from '@/utils/formatters';
import { formatContractDate } from '../../../utils/dateFormatter';

const styles = StyleSheet.create({
  page: {
    padding: 30,
    fontSize: 9,
    fontFamily: 'Helvetica',
  },
  mainBox: {
    borderWidth: 1,
    borderColor: '#000',
    flexDirection: 'column',
  },
  headerRow: {
    position: 'relative',
    height: 96,
    padding: 0,
    borderBottomWidth: 1,
    borderBottomColor: '#000',
  },
  logoBlock: {
    position: 'absolute',
    left: 16,
    top: 25,
  },
  logo: {
    width: 42,
    height: 42,
  },
  companyName: {
    position: 'absolute',
    left: 16,
    bottom: 9,
    fontSize: 10,
    lineHeight: 1.2,
    fontFamily: 'Helvetica-Bold',
  },
  companyInfo: {
    position: 'absolute',
    top: 4,
    right: 0,
    width: '50%',
    textAlign: 'center',
    fontSize: 7,
    lineHeight: 1.25,
  },
  bold: {
    fontFamily: 'Helvetica-Bold',
  },
  titleRow: {
    height: 15,
    padding: 0,
    borderBottomWidth: 1,
    borderBottomColor: '#000',
    alignItems: 'center',
    justifyContent: 'center',
  },
  title: {
    fontSize: 10,
    fontFamily: 'Helvetica-Bold',
  },
  detailsRow: {
    flexDirection: 'row',
    padding: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#000',
  },
  detailsColLeft: {
    width: '50%',
  },
  detailsColRight: {
    width: '50%',
  },
  detailLine: {
    flexDirection: 'row',
    marginBottom: 6,
  },
  detailLabel: {
    width: 80,
    fontFamily: 'Helvetica-Bold',
  },
  detailValue: {
    flex: 1,
  },
  tableHeaderRow: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderBottomColor: '#000',
    fontFamily: 'Helvetica-Bold',
    fontSize: 8,
    alignItems: 'stretch',
    textAlign: 'center',
  },
  tableRow: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderBottomColor: '#000',
    fontSize: 8,
    alignItems: 'stretch',
    textAlign: 'center',
  },
  colSNo: { width: '4%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colItem: { width: '18.12%', borderRightWidth: 1, borderRightColor: '#000', padding: 3, textAlign: 'left' },
  colOrderQty: { width: '11.76%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colRecQty: { width: '11.76%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colRate: { width: '9.6%', borderRightWidth: 1, borderRightColor: '#000', padding: 3, textAlign: 'right' },
  colPrice: { width: '15.96%', borderRightWidth: 1, borderRightColor: '#000', padding: 3, textAlign: 'right' },
  colTaxRate: { width: '9.6%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colTaxAmt: { width: '9.6%', borderRightWidth: 1, borderRightColor: '#000', padding: 3, textAlign: 'right' },
  colAmount: { width: '9.6%', padding: 3, textAlign: 'right' },
  
  footerRow: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderBottomColor: '#000',
  },
  footerLabel: {
    width: '71.2%',
    padding: 3,
    borderRightWidth: 1,
    borderRightColor: '#000',
    textAlign: 'right',
    fontFamily: 'Helvetica-Bold',
    fontSize: 8,
  },
  footerValue: {
    width: '18.8%',
    padding: 3,
    textAlign: 'right',
    fontSize: 8,
  },
  footerTaxStatus: {
    width: '10%',
    padding: 3,
    borderRightWidth: 1,
    borderRightColor: '#000',
    textAlign: 'center',
    fontSize: 7,
    fontFamily: 'Helvetica-Bold',
  },
  wordsColLabel: {
    width: '30%',
    padding: 3,
    borderRightWidth: 1,
    borderRightColor: '#000',
    fontFamily: 'Helvetica-Bold',
    fontSize: 8,
  },
  wordsColText: {
    width: '60%',
    padding: 3,
    borderRightWidth: 1,
    borderRightColor: '#000',
    fontSize: 8,
  },
  wordsColTotal: {
    width: '10%',
    flexDirection: 'column',
    alignItems: 'center',
  },
  totalAmountLabel: {
    fontFamily: 'Helvetica-Bold',
    fontSize: 8,
    paddingTop: 3,
  },
  totalAmountValue: {
    fontSize: 8,
    paddingTop: 5,
    paddingBottom: 3,
    textAlign: 'right',
    width: '100%',
    paddingRight: 3,
  },
  remarksColLabel: {
    width: '30%',
    padding: 3,
    borderRightWidth: 1,
    borderRightColor: '#000',
    fontFamily: 'Helvetica-Bold',
    fontSize: 8,
  },
  remarksColText: {
    width: '70%',
    padding: 3,
    fontSize: 8,
  },
  signaturesRow: {
    flexDirection: 'row',
    height: 70,
    justifyContent: 'space-between',
    padding: 5,
    paddingTop: 50,
    fontSize: 8,
    fontFamily: 'Helvetica-Bold',
  }
});

interface GrnPdfProps {
  data: any;
}

export const GrnPdf: React.FC<GrnPdfProps> = ({ data }) => {
  const { grn, items } = data || {};
  
  if (!grn) return <Document><Page size="A4"><Text>No data</Text></Page></Document>;

  
  const freightCharges = Number(grn.freight) || 0; 
  const itemAmountTotal = (items || []).reduce((sum: number, item: any) => {
    const qty = Number(item.quantity) || 0;
    const rate = Number(item.rate) || 0;
    const price = item.cost_price !== undefined && item.cost_price !== null ? Number(item.cost_price) : qty * rate;
    const taxAmt = item.tax !== undefined && item.tax !== null ? Number(item.tax) : 0;
    const amount = item.amount !== undefined && item.amount !== null ? Number(item.amount) : price + taxAmt;
    return sum + amount;
  }, 0);
  const hasTaxIncludedItem = (items || []).some((item: any) => Number(item.cost_price) === Number(item.amount));
  const totalAmount = itemAmountTotal + freightCharges;

  return (
    <Document>
      <Page size="A4" style={styles.page}>
        <View style={styles.mainBox}>
          
          {/* Row 1: Header */}
          <View style={styles.headerRow}>
            <View style={styles.logoBlock}>
              <Image 
                src="http://localhost:5000/public/uploads/logos/d80960ce77aede66a5c3c8eef8dfafda.png" 
                style={styles.logo}
              />
            </View>
            <Text style={styles.companyName}>TIRUPATI PLASTOMATICS PVT. LTD.</Text>
            <View style={styles.companyInfo}>
              <Text>B-141(A), Rd Number 9D, Vishwakarma Industrial Area, Jaipur, Rajasthan 302013</Text>
              <Text><Text style={styles.bold}>Phone : </Text>9829287189</Text>
              <Text><Text style={styles.bold}>Email : </Text>contact@tirupatiplastomatics.com</Text>
              <Text><Text style={styles.bold}>Website : </Text>www.tirupatiplastomatics.com</Text>
            </View>
          </View>

          {/* Row 2: Title */}
          <View style={styles.titleRow}>
            <Text style={styles.title}>GOOD RECEIPT NOTE (GRN)</Text>
          </View>

          {/* Row 3: Details */}
          <View style={styles.detailsRow}>
            <View style={styles.detailsColLeft}>
              <View style={styles.detailLine}>
                <Text style={styles.detailLabel}>GRN No.</Text>
                <Text style={styles.detailValue}>: {grn.id}</Text>
              </View>
              <View style={styles.detailLine}>
                <Text style={styles.detailLabel}>Inward Date</Text>
                <Text style={styles.detailValue}>: {formatContractDate(grn.inwarddate)}</Text>
              </View>
              <View style={styles.detailLine}>
                <Text style={styles.detailLabel}>Bill Date</Text>
                <Text style={styles.detailValue}>: {formatContractDate(grn.bill_date)}</Text>
              </View>
              <View style={styles.detailLine}>
                <Text style={styles.detailLabel}>Bill No</Text>
                <Text style={styles.detailValue}>: {grn.bill_no}</Text>
              </View>
            </View>
            <View style={styles.detailsColRight}>
              <View style={styles.detailLine}>
                <Text style={styles.detailLabel}>GSTIN NO.</Text>
                <Text style={styles.detailValue}>: {grn.vendor_gstin || ''}</Text>
              </View>
              <View style={styles.detailLine}>
                <Text style={styles.detailLabel}>Vendor Name</Text>
                <Text style={styles.detailValue}>: {grn.vendor_name}</Text>
              </View>
              <View style={styles.detailLine}>
                <Text style={styles.detailLabel}>PO No.</Text>
                <Text style={styles.detailValue}>: {grn.purchaseorder_id}</Text>
              </View>
            </View>
          </View>

          {/* Row 4: Table Header */}
          <View style={styles.tableHeaderRow}>
            <Text style={styles.colSNo}>S.No</Text>
            <Text style={styles.colItem}>ITEM</Text>
            <Text style={styles.colOrderQty}>ORDER QTY.</Text>
            <Text style={styles.colRecQty}>RECEIVED QTY.</Text>
            <Text style={styles.colRate}>RATE</Text>
            <Text style={styles.colPrice}>PRICE (INR)</Text>
            <Text style={styles.colTaxRate}>TAX RATE</Text>
            <Text style={styles.colTaxAmt}>TAX AMT</Text>
            <Text style={styles.colAmount}>AMOUNT</Text>
          </View>
          
          {/* Row 5...N: Table Items */}
          {(items || []).map((item: any, idx: number) => {
            const qty = Number(item.quantity) || 0;
            const rate = Number(item.rate) || 0;
            const price = item.cost_price !== undefined && item.cost_price !== null ? Number(item.cost_price) : qty * rate;
            const taxAmt = item.tax !== undefined && item.tax !== null ? Number(item.tax) : 0;
            const amount = item.amount !== undefined && item.amount !== null ? Number(item.amount) : price + taxAmt;
            const taxRate = price > 0 ? Math.round((taxAmt / price) * 100) : 0;
            const orderQty = item.order_qty !== undefined && item.order_qty !== null ? Number(item.order_qty) : qty; 
            
            return (
              <View style={styles.tableRow} key={idx}>
                <Text style={styles.colSNo}>{idx + 1}.</Text>
                <Text style={styles.colItem}>{item.item_name}</Text>
                <Text style={styles.colOrderQty}>{formatQty(orderQty)}</Text>
                <Text style={styles.colRecQty}>{formatQty(qty)}</Text>
                <Text style={styles.colRate}>{formatAmt(rate)}</Text>
                <Text style={styles.colPrice}>{formatAmt(price)}</Text>
                <Text style={styles.colTaxRate}>{taxRate || 18}</Text>
                <Text style={styles.colTaxAmt}>{formatAmt(taxAmt)}</Text>
                <Text style={styles.colAmount}>{formatAmt(amount)}</Text>
              </View>
            );
          })}

          {/* Footer: Amount */}
          <View style={styles.footerRow}>
            <Text style={styles.footerLabel}>Amount</Text>
            <Text style={styles.footerTaxStatus}>{hasTaxIncludedItem ? 'Tax\nIncluded' : 'Tax\nExcluded'}</Text>
            <Text style={styles.footerValue}>{formatAmt(itemAmountTotal)}</Text>
          </View>

          {/* Footer: Freight Charges */}
          <View style={styles.footerRow}>
            <Text style={styles.footerLabel}>Freight Charges</Text>
            <Text style={styles.footerValue}>{formatAmt(freightCharges)}</Text>
          </View>
          
          {/* Footer: Words and Total Amount */}
          <View style={styles.footerRow}>
            <Text style={styles.wordsColLabel}>Amount (In Words)</Text>
            <Text style={styles.wordsColText}>{numberToWords(totalAmount)}</Text>
            <View style={styles.wordsColTotal}>
              <Text style={styles.totalAmountLabel}>Total Amount</Text>
              <Text style={styles.totalAmountValue}>{formatAmt(totalAmount)}</Text>
            </View>
          </View>

          {/* Remarks */}
          <View style={styles.footerRow}>
            <Text style={styles.remarksColLabel}>Remarks</Text>
            <Text style={styles.remarksColText}>{grn.remark || 'Ok'}</Text>
          </View>

          {/* Signatures */}
          <View style={styles.signaturesRow}>
            <Text>For {grn.vendor_name || 'JSK INDUSTRIES PVT. LTD.'}</Text>
            <Text>Inspected By</Text>
            <Text>Store Incharge</Text>
            <Text>Checked by</Text>
            <Text>Signature Authority</Text>
          </View>

        </View>
      </Page>
    </Document>
  );
};

export default GrnPdf;
