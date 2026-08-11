const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_received_quotations_details', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    quotation_id: {
      type: DataTypes.STRING(55),
      allowNull: true
    },
    received_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    item_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    uom: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    tax_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    item_qty: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    item_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    item_tax_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    item_total_amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    item_price_bid: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    item_amt_bid: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    item_tax_amt_bid: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    item_total_amount_bid: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    }
  }, {
    sequelize,
    tableName: 'st_received_quotations_details',
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
