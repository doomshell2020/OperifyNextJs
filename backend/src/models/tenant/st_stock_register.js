const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_stock_register', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    po_id: {
      type: DataTypes.STRING(55),
      allowNull: true,
      comment: "it's a purchase order id"
    },
    purchaseorder_id: {
      type: DataTypes.STRING(55),
      allowNull: true,
      comment: "purchase order primary key"
    },
    goods_id: {
      type: DataTypes.STRING(55),
      allowNull: true,
      comment: "goods received primary id"
    },
    indent_id: {
      type: DataTypes.STRING(55),
      allowNull: true
    },
    reverse_id: {
      type: DataTypes.STRING(55),
      allowNull: true
    },
    return_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    item_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
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
      type: DataTypes.FLOAT,
      allowNull: true
    },
    rate: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    cost_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    tax_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    tax: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    added_time: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    central_store_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    central_store_type: {
      type: DataTypes.ENUM('0','1','2','3'),
      allowNull: true,
      defaultValue: "0"
    },
    store_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    store_type: {
      type: DataTypes.ENUM('0','1','2','3','4','5','6'),
      allowNull: true,
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
    status: {
      type: DataTypes.ENUM('Y','N','R'),
      allowNull: true,
      defaultValue: "Y",
      comment: "R for check PO is revised or not"
    },
    is_revised: {
      type: DataTypes.INTEGER,
      allowNull: true,
      defaultValue: 0,
      comment: "PO revised count"
    },
    group_entry: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    mrn: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    weight: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    sale_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    contract_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    finishedproduct_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    delivery_schedule_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_stock_register',
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
