const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_stock_cancel_sales_return', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    po_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      comment: "it's a purchase order id"
    },
    purchaseorder_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      comment: "purchase order primary key"
    },
    goods_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      comment: "goods received primary id"
    },
    indent_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    item_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    issue_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    delivery_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    quantity: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    rate: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    cost_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    sale_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    tax_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    tax: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false,
      defaultValue: 0.00
    },
    amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    central_store_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    central_store_type: {
      type: DataTypes.ENUM('0','1','2'),
      allowNull: false,
      defaultValue: "0"
    },
    store_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    store_type: {
      type: DataTypes.ENUM('0','1','2','3','4'),
      allowNull: false,
      defaultValue: "0"
    },
    store_quantity: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    student_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    cancel_created_time: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    status: {
      type: DataTypes.ENUM('Y','N','R'),
      allowNull: false,
      defaultValue: "Y",
      comment: "R for check PO is revised or not"
    },
    is_revised: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0,
      comment: "PO revised count"
    }
  }, {
    sequelize,
    tableName: 'st_stock_cancel_sales_return',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "id",
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
