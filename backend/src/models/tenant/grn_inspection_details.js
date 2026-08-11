const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('grn_inspection_details', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    purchaseorder_id: {
      type: DataTypes.STRING(55),
      allowNull: true,
      comment: "purchase order primary key"
    },
    inspection_id: {
      type: DataTypes.STRING(55),
      allowNull: true,
      comment: "goods received primary id"
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
    store_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N','R'),
      allowNull: true,
      defaultValue: "Y",
      comment: "R for check PO is revised or not"
    },
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    delivery_schedule_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'grn_inspection_details',
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
    ]
  });
};
