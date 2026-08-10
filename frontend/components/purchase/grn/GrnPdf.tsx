import React from 'react';
import { Page, Text, View, Document, StyleSheet, Image } from '@react-pdf/renderer';
import { numberToWords } from '@/utils/numberToWords';

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
    flexDirection: 'row',
    justifyContent: 'space-between',
    padding: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#000',
  },
  logoBlock: {
    width: '40%',
  },
  logo: {
    width: 60,
    height: 60,
    marginBottom: 10,
  },
  companyName: {
    fontSize: 11,
    fontFamily: 'Helvetica-Bold',
  },
  companyInfo: {
    width: '60%',
    textAlign: 'right',
    lineHeight: 1.4,
  },
  bold: {
    fontFamily: 'Helvetica-Bold',
  },
  titleRow: {
    padding: 5,
    borderBottomWidth: 1,
    borderBottomColor: '#000',
    alignItems: 'center',
  },
  title: {
    fontSize: 12,
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
  // Table widths to perfectly align with footer
  // SNo: 5%, Item: 25% -> Total 30%
  // Order: 10%, Rec: 10%, Rate: 10%, Price: 12%, TaxR: 8%, TaxA: 10% -> Total 60%
  // Amount: 10%
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
  colSNo: { width: '5%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colItem: { width: '25%', borderRightWidth: 1, borderRightColor: '#000', padding: 3, textAlign: 'left' },
  colOrderQty: { width: '10%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colRecQty: { width: '10%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colRate: { width: '10%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colPrice: { width: '12%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colTaxRate: { width: '8%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colTaxAmt: { width: '10%', borderRightWidth: 1, borderRightColor: '#000', padding: 3 },
  colAmount: { width: '10%', padding: 3, textAlign: 'right' },
  
  footerRow: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderBottomColor: '#000',
  },
  footerLabel: {
    width: '90%',
    padding: 3,
    borderRightWidth: 1,
    borderRightColor: '#000',
    textAlign: 'right',
    fontFamily: 'Helvetica-Bold',
    fontSize: 8,
  },
  footerValue: {
    width: '10%',
    padding: 3,
    textAlign: 'right',
    fontSize: 8,
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

  const formatDate = (dateStr?: string) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-IN', {
      day: '2-digit', month: '2-digit', year: 'numeric'
    }).replace(/\//g, '-');
  };

  const taxExcludedAmount = Number(grn.total_amt) || 0;
  const freightCharges = 0; 
  const totalAmount = taxExcludedAmount + Number(grn.total_tax || 0);

  return (
    <Document>
      <Page size="A4" style={styles.page}>
        <View style={styles.mainBox}>
          
          {/* Row 1: Header */}
          <View style={styles.headerRow}>
            <View style={styles.logoBlock}>
              <Image 
                src="http://localhost:5000/public/uploads/logos/tirupati_tppl_logo.png" 
                style={styles.logo}
              />
              <Text style={styles.companyName}>TIRUPATI PLASTOMATICS PVT. LTD.</Text>
            </View>
            <View style={styles.companyInfo}>
              <Text>B-141(A), Rd Number 9D, Vishwakarma Industrial Area,</Text>
              <Text>Jaipur, Rajasthan 302013</Text>
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
                <Text style={styles.detailValue}>: {formatDate(grn.inwarddate)}</Text>
              </View>
              <View style={styles.detailLine}>
                <Text style={styles.detailLabel}>Bill Date</Text>
                <Text style={styles.detailValue}>: {formatDate(grn.bill_date)}</Text>
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
            const price = qty * rate;
            const taxAmt = Number(item.tax) || 0;
            const amount = price + taxAmt;
            const taxRate = price > 0 ? Math.round((taxAmt / price) * 100) : 0;
            const orderQty = item.order_qty || qty; 
            
            return (
              <View style={styles.tableRow} key={idx}>
                <Text style={styles.colSNo}>{idx + 1}.</Text>
                <Text style={styles.colItem}>{item.item_name}</Text>
                <Text style={styles.colOrderQty}>{orderQty}</Text>
                <Text style={styles.colRecQty}>{qty}</Text>
                <Text style={styles.colRate}>{rate.toFixed(2)}</Text>
                <Text style={styles.colPrice}>{price.toFixed(2)}</Text>
                <Text style={styles.colTaxRate}>{taxRate || 18}</Text>
                <Text style={styles.colTaxAmt}>{taxAmt.toFixed(2)}</Text>
                <Text style={styles.colAmount}>{amount.toFixed(2)}</Text>
              </View>
            );
          })}

          {/* Footer: Amount Tax Excluded */}
          <View style={styles.footerRow}>
            <Text style={styles.footerLabel}>Amount Tax Excluded</Text>
            <Text style={styles.footerValue}>{taxExcludedAmount.toFixed(2)}</Text>
          </View>

          {/* Footer: Freight Charges */}
          <View style={styles.footerRow}>
            <Text style={styles.footerLabel}>Freight Charges</Text>
            <Text style={styles.footerValue}>{freightCharges.toFixed(2)}</Text>
          </View>
          
          {/* Footer: Words and Total Amount */}
          <View style={styles.footerRow}>
            <Text style={styles.wordsColLabel}>Amount (In Words)</Text>
            <Text style={styles.wordsColText}>{numberToWords(totalAmount)}</Text>
            <View style={styles.wordsColTotal}>
              <Text style={styles.totalAmountLabel}>Total Amount</Text>
              <Text style={styles.totalAmountValue}>{totalAmount.toFixed(2)}</Text>
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
