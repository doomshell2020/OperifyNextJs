const stockRegisterRepository = require('./stockRegister.repository');

class StockRegisterService {
  async getCategories(dbPool) {
    return await stockRegisterRepository.getCategories(dbPool);
  }

  async getDailyStockAsOfDate(dbPool, filters) {
    if (!filters.date) {
      throw new Error('date is required.');
    }
    return await stockRegisterRepository.getDailyStockAsOfDate(dbPool, filters);
  }

  async exportDailyStockExcel(dbPool, filters, res) {
    const data = await this.getDailyStockAsOfDate(dbPool, filters);

    const ExcelJS = require('exceljs');
    const fs = require('fs');
    const path = require('path');
    
    // Fetch company name
    let companyName = 'TIRUPATI PLASTOMATICS PVT. LTD.';
    try {
      const [settings] = await dbPool.query('SELECT company_name FROM sitesettings_details WHERE sitesettings_id = 1 LIMIT 1');
      if (settings && settings.length > 0 && settings[0].company_name) {
        companyName = settings[0].company_name;
      }
    } catch (e) {
      console.error('Error fetching company name:', e);
    }

    const workbook = new ExcelJS.Workbook();
    const sheet = workbook.addWorksheet('Daily Stock');

    sheet.columns = [
      { header: 'S.No', key: 'sno', width: 10 },
      { header: 'Item Name', key: 'item_name', width: 30 },
      { header: 'Category', key: 'category_name', width: 20 },
      { header: 'Opening Stock', key: 'opening', width: 20 },
      { header: 'Received Stock', key: 'received', width: 20 },
      { header: 'Issued Stock', key: 'issued', width: 20 },
      { header: 'Reverse Stock', key: 'reverse', width: 20 },
      { header: 'Return Stock', key: 'return_qty', width: 20 },
      { header: 'Closing Stock', key: 'closing', width: 20 },
    ];

    // Insert top header row for Company Name
    sheet.spliceRows(1, 0, []);
    sheet.mergeCells('A1:I1');
    const titleCell = sheet.getCell('A1');
    titleCell.value = companyName;
    titleCell.font = { bold: true, size: 14 };
    titleCell.alignment = { horizontal: 'center', vertical: 'middle' };
    sheet.getRow(1).height = 40;

    // Attach logo if it exists
    try {
      const dirPath = path.join(__dirname, '../../../public/uploads/logos');
      if (fs.existsSync(dirPath)) {
        const files = fs.readdirSync(dirPath);
        // We'll look for default_logo.png etc
        const logoFile = files.find(f => f.includes('_logo.'));
        if (logoFile) {
          const logoPath = path.join(dirPath, logoFile);
          const ext = path.extname(logoFile).substring(1);
          const imageId = workbook.addImage({
            filename: logoPath,
            extension: ext,
          });
          sheet.addImage(imageId, {
            tl: { col: 0, row: 0 },
            ext: { width: 100, height: 40 }
          });
        }
      }
    } catch (e) {
      console.error('Error embedding logo:', e);
    }

    // Now headers are on row 2
    sheet.getRow(2).font = { bold: true };

    let sno = 1;
    for (const row of data) {
      sheet.addRow({
        sno: sno++,
        item_name: row.item_name,
        category_name: row.category_name,
        opening: row.opening_stock,
        received: row.received_stock,
        issued: row.issued_stock,
        reverse: row.reverse_stock,
        return_qty: row.return_stock,
        closing: row.closing_stock
      });
    }

    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    res.setHeader('Content-Disposition', 'attachment; filename=' + 'Daily_Stock_Register.xlsx');

    await workbook.xlsx.write(res);
    res.end();
  }

  async getStockRegister(dbPool, filters) {
    if (!filters.date_from || !filters.date_to) {
      throw new Error('date_from and date_to are required.');
    }
    
    // Validate that Date From cannot be greater than Date To
    if (new Date(filters.date_from) > new Date(filters.date_to)) {
      throw new Error('Date From cannot be greater than Date To.');
    }

    return await stockRegisterRepository.getStockRegister(dbPool, filters);
  }

  async getReceivedStockDetails(dbPool, filters) {
    if (!filters.date || !filters.product_id) {
      throw new Error('date and product_id are required.');
    }
    return await stockRegisterRepository.getReceivedStockDetails(dbPool, filters);
  }

  async getDispatchedStockDetails(dbPool, filters) {
    if (!filters.date || !filters.product_id) {
      throw new Error('date and product_id are required.');
    }
    return await stockRegisterRepository.getDispatchedStockDetails(dbPool, filters);
  }

  async exportExcel(dbPool, filters, res) {
    const data = await this.getStockRegister(dbPool, filters);

    const ExcelJS = require('exceljs');
    const fs = require('fs');
    const path = require('path');
    
    // Fetch company name
    let companyName = 'TIRUPATI PLASTOMATICS PVT. LTD.';
    try {
      const [settings] = await dbPool.query('SELECT company_name FROM sitesettings_details WHERE sitesettings_id = 1 LIMIT 1');
      if (settings && settings.length > 0 && settings[0].company_name) {
        companyName = settings[0].company_name;
      }
    } catch (e) {
      console.error('Error fetching company name:', e);
    }

    const workbook = new ExcelJS.Workbook();
    const sheet = workbook.addWorksheet('Stock Register');

    sheet.columns = [
      { header: 'S.No', key: 'sno', width: 10 },
      { header: 'Date', key: 'date', width: 15 },
      { header: 'Opening Stock', key: 'opening', width: 20 },
      { header: 'Received Stock', key: 'received', width: 20 },
      { header: 'Dispatched Stock', key: 'dispatched', width: 20 },
      { header: 'Closing Stock', key: 'closing', width: 20 },
    ];

    // Insert top header row for Company Name
    sheet.spliceRows(1, 0, []);
    sheet.mergeCells('A1:F1');
    const titleCell = sheet.getCell('A1');
    titleCell.value = companyName;
    titleCell.font = { bold: true, size: 14 };
    titleCell.alignment = { horizontal: 'center', vertical: 'middle' };
    sheet.getRow(1).height = 40;

    // Attach logo if it exists
    try {
      const dirPath = path.join(__dirname, '../../../public/uploads/logos');
      if (fs.existsSync(dirPath)) {
        const files = fs.readdirSync(dirPath);
        // We'll look for default_logo.png etc
        const logoFile = files.find(f => f.includes('_logo.'));
        if (logoFile) {
          const logoPath = path.join(dirPath, logoFile);
          const ext = path.extname(logoFile).substring(1);
          const imageId = workbook.addImage({
            filename: logoPath,
            extension: ext,
          });
          sheet.addImage(imageId, {
            tl: { col: 0, row: 0 },
            ext: { width: 100, height: 40 }
          });
        }
      }
    } catch (e) {
      console.error('Error embedding logo:', e);
    }

    // Make header bold
    sheet.getRow(2).font = { bold: true };

    sheet.getColumn('date').numFmt = 'dd-mm-yy';

    let sno = 1;
    for (const row of data) {
      sheet.addRow({
        sno: sno++,
        date: row.date_range ? new Date(row.date_range) : null,
        opening: row.opening_stock,
        received: row.received_stock,
        dispatched: row.dispatched_stock,
        closing: row.closing_stock
      });
    }

    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    res.setHeader('Content-Disposition', 'attachment; filename=' + 'Stock_Register.xlsx');

    await workbook.xlsx.write(res);
    res.end();
  }
}

module.exports = new StockRegisterService();
